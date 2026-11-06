<?php

namespace App\Console\Commands;

use App\Mail\PengingatJatuhTempoMail;
use App\Models\Kuitansi;
use App\Models\PengingatEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

class KirimPengingatJatuhTempo extends Command
{
    protected $signature = 'pengingat:kirim
        {--tanggal= : Tanggal acuan (Y-m-d), untuk uji coba}
        {--dry-run : Tampilkan yang akan dikirim tanpa benar-benar mengirim}';

    protected $description = 'Kirim email pengingat untuk tagihan yang jatuh tempo hari ini dan 7 hari lagi';

    public function handle(): int
    {
        $hariIni = $this->option('tanggal')
            ? Carbon::parse((string) $this->option('tanggal'))->startOfDay()
            : Carbon::today();
        $tujuhHariLagi = $hariIni->copy()->addDays(Kuitansi::HARI_PENGINGAT);
        $dryRun = (bool) $this->option('dry-run');

        // Hanya tagihan yang masih bersisa dan belum dibatalkan (pembayaran dibatalkan tidak dihitung).
        $kandidat = Kuitansi::query()
            ->with('user')
            ->whereNull('dibatalkan_pada')
            ->where(function ($query) use ($hariIni, $tujuhHariLagi) {
                $query->whereDate('tanggal_jatuh_tempo', $hariIni->toDateString())
                    ->orWhereDate('tanggal_jatuh_tempo', $tujuhHariLagi->toDateString());
            })
            ->withSum('pembayaran as total_terbayar', 'nominal')
            ->get()
            ->filter(fn (Kuitansi $k): bool => round((float) $k->nominal_transaksi - (float) $k->total_terbayar, 2) > 0);

        $sudahTerkirim = PengingatEmail::query()
            ->whereIn('kuitansi_id', $kandidat->pluck('id')->all())
            ->get()
            ->map(fn (PengingatEmail $log): string => $this->kunci($log->kuitansi_id, $log->jenis, $log->tanggal_jatuh_tempo->toDateString()))
            ->all();

        $antrean = $kandidat
            ->map(fn (Kuitansi $k): array => [
                'kuitansi' => $k,
                'jenis' => $k->tanggal_jatuh_tempo->isSameDay($hariIni) ? 'hari-h' : 'h-7',
            ])
            ->reject(fn (array $item): bool => in_array(
                $this->kunci($item['kuitansi']->id, $item['jenis'], $item['kuitansi']->tanggal_jatuh_tempo->toDateString()),
                $sudahTerkirim,
                true
            ));

        if ($antrean->isEmpty()) {
            $this->info('Tidak ada pengingat yang perlu dikirim.');

            return self::SUCCESS;
        }

        $jumlahEmail = 0;
        $jumlahPengingat = 0;
        $gagal = 0;

        foreach ($antrean->groupBy(fn (array $item) => $item['kuitansi']->user_id) as $daftar) {
            $pemilik = $daftar->first()['kuitansi']->user;

            if ($pemilik === null) {
                continue;
            }

            $tujuan = $this->penerima($pemilik->email);
            $hariH = $this->susunBaris($daftar->where('jenis', 'hari-h'));
            $tujuhHari = $this->susunBaris($daftar->where('jenis', 'h-7'));
            $ringkas = count($hariH).' hari ini, '.count($tujuhHari).' tujuh hari lagi';

            if ($dryRun) {
                $this->line('[dry-run] '.implode(', ', $tujuan)." ({$ringkas})");

                continue;
            }

            try {
                Mail::to($tujuan)->send(new PengingatJatuhTempoMail($pemilik->name, $hariH, $tujuhHari));
            } catch (Throwable $e) {
                report($e);
                $this->error('Gagal mengirim ke '.implode(', ', $tujuan).": {$e->getMessage()}");
                $gagal++;

                continue;
            }

            // Dicatat setelah email berhasil dikirim; bila gagal, akan dicoba lagi pada jam berikutnya.
            foreach ($daftar as $item) {
                PengingatEmail::query()->firstOrCreate(
                    [
                        'kuitansi_id' => $item['kuitansi']->id,
                        'jenis' => $item['jenis'],
                        'tanggal_jatuh_tempo' => $item['kuitansi']->tanggal_jatuh_tempo->toDateString(),
                    ],
                    [
                        'penerima' => implode(', ', $tujuan),
                        'dikirim_pada' => now(),
                    ]
                );
            }

            $jumlahEmail++;
            $jumlahPengingat += $daftar->count();
            $this->line('Terkirim ke '.implode(', ', $tujuan)." ({$ringkas})");
        }

        $this->info("Selesai. {$jumlahEmail} email dikirim, {$jumlahPengingat} pengingat.");

        return $gagal > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function penerima(string $emailPemilik): array
    {
        $khusus = array_values(array_filter(array_map('trim', explode(',', (string) config('pengingat.email_tujuan')))));

        return $khusus !== [] ? $khusus : [$emailPemilik];
    }

    /**
     * @param  Collection<int, array{kuitansi: Kuitansi, jenis: string}>  $items
     * @return array<int, array<string, string>>
     */
    private function susunBaris(Collection $items): array
    {
        return $items
            ->map(function (array $item): array {
                $kuitansi = $item['kuitansi'];
                $sisa = max(round((float) $kuitansi->nominal_transaksi - (float) $kuitansi->total_terbayar, 2), 0);

                return [
                    'nomor' => (string) $kuitansi->nomor_kuitansi,
                    'nama' => (string) $kuitansi->nama_pihak,
                    'jenis' => $kuitansi->jenis === 'piutang' ? 'Piutang' : 'Utang',
                    'tempo' => $kuitansi->tanggal_jatuh_tempo->translatedFormat('d M Y'),
                    'sisa' => Kuitansi::rupiah($sisa),
                    'url' => route('arsip-kuitansi.show', $kuitansi),
                ];
            })
            ->sortBy('nama')
            ->values()
            ->all();
    }

    private function kunci(int $kuitansiId, string $jenis, string $tanggal): string
    {
        return "{$kuitansiId}|{$jenis}|{$tanggal}";
    }
}
