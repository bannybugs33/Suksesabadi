<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Kuitansi extends Model
{
    /**
     * Kode tempo yang dianggap "perlu perhatian" (pengingat aktif mulai H-3).
     *
     * @var array<int, string>
     */
    public const KODE_PENGINGAT = ['terlambat', 'hari_ini', 'h-1', 'h-2', 'h-3'];

    /**
     * @var string
     */
    protected $table = 'kuitansi';

    /**
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_transaksi' => 'decimal:2',
            'tanggal_kuitansi' => 'date',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'kuitansi_id');
    }

    /**
     * Hitung label dan kode tempo sebuah kuitansi terhadap tanggal acuan.
     * Kuitansi lunas atau tanpa jatuh tempo selalu dianggap aman.
     *
     * @return array{label: string, code: string}
     */
    public static function infoTempo(mixed $tanggalJatuhTempo, ?string $status, Carbon $tanggalAcuan): array
    {
        if (! $tanggalJatuhTempo || $status === 'lunas') {
            return ['label' => '-', 'code' => 'aman'];
        }

        $acuan = $tanggalAcuan->copy()->startOfDay();
        $tempo = Carbon::parse($tanggalJatuhTempo)->startOfDay();
        $selisih = (int) abs($acuan->diffInDays($tempo));

        if ($tempo->lt($acuan)) {
            return ['label' => "Terlambat {$selisih} hari", 'code' => 'terlambat'];
        }

        if ($selisih === 0) {
            return ['label' => 'Jatuh tempo hari ini', 'code' => 'hari_ini'];
        }

        if ($selisih === 1) {
            return ['label' => 'Jatuh tempo besok', 'code' => 'h-1'];
        }

        if ($selisih <= 3) {
            return ['label' => "H-{$selisih}", 'code' => "h-{$selisih}"];
        }

        return ['label' => 'Mendatang', 'code' => 'mendatang'];
    }

    /**
     * Ringkasan pengingat jatuh tempo: kuitansi belum lunas yang sudah lewat
     * atau jatuh tempo dalam 3 hari ke depan.
     * Jika $userId null, dihitung untuk seluruh toko (hanya angka agregat).
     *
     * @return array{jumlah: int, terlambat: int, sisa: float}
     */
    public static function ringkasanPengingat(?int $userId = null, ?Carbon $tanggalAcuan = null): array
    {
        $acuan = ($tanggalAcuan ?? Carbon::today())->copy()->startOfDay();
        $batas = $acuan->copy()->addDays(3)->toDateString();

        $items = static::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('status_pembayaran', '!=', 'lunas')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->whereDate('tanggal_jatuh_tempo', '<=', $batas)
            ->withSum('pembayaran as total_terbayar', 'nominal')
            ->get();

        return [
            'jumlah' => $items->count(),
            'terlambat' => $items->filter(fn (self $k) => $k->tanggal_jatuh_tempo->lt($acuan))->count(),
            'sisa' => (float) $items->sum(
                fn (self $k) => max((float) $k->nominal_transaksi - (float) $k->total_terbayar, 0)
            ),
        ];
    }

    /**
     * Data grafik batang piutang yang dicatat per bulan (untuk panel halaman login).
     *
     * @return array{bars: array<int, array{label: string, nilai: float, tinggi: int, aktif: bool}>, total: float, perubahan: ?float}
     */
    public static function grafikPiutangBulanan(?Carbon $tanggalAcuan = null, int $jumlahBulan = 7): array
    {
        $bulanIni = ($tanggalAcuan ?? Carbon::today())->copy()->startOfMonth();
        $awal = $bulanIni->copy()->subMonths($jumlahBulan - 1);

        $perBulan = static::query()
            ->where('jenis', 'piutang')
            ->whereDate('tanggal_kuitansi', '>=', $awal->toDateString())
            ->get(['tanggal_kuitansi', 'nominal_transaksi'])
            ->groupBy(fn (self $k) => $k->tanggal_kuitansi->format('Y-m'))
            ->map(fn ($grup) => (float) $grup->sum('nominal_transaksi'));

        $bars = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $bulan = $awal->copy()->addMonths($i);
            $bars[] = [
                'label' => $bulan->translatedFormat('M'),
                'nilai' => (float) $perBulan->get($bulan->format('Y-m'), 0.0),
                'tinggi' => 12,
                'aktif' => $i === $jumlahBulan - 1,
            ];
        }

        $maks = max(array_column($bars, 'nilai'));
        foreach ($bars as $i => $bar) {
            $bars[$i]['tinggi'] = $maks > 0 ? max((int) round($bar['nilai'] / $maks * 100), 12) : 12;
        }

        $sekarang = $bars[$jumlahBulan - 1]['nilai'];
        $sebelumnya = $bars[$jumlahBulan - 2]['nilai'];

        return [
            'bars' => $bars,
            'total' => $sekarang,
            'perubahan' => $sebelumnya > 0 ? round(($sekarang - $sebelumnya) / $sebelumnya * 100, 1) : null,
        ];
    }

    /**
     * Disk privat tempat foto/berkas bukti kuitansi disimpan (storage/app/private).
     * Berkas di disk ini tidak punya URL publik; hanya bisa dibuka lewat route
     * arsip-kuitansi.bukti yang mewajibkan login.
     */
    public const DISK_BUKTI = 'local';

    /**
     * Cari berkas bukti di penyimpanan server.
     *
     * Urutan pencarian: disk privat (lokasi baru), lalu disk public (data lama yang
     * disimpan sebagai "storage/kuitansi/..."), lalu nama berkas saja di folder kuitansi.
     *
     * @return array{disk: string, path: string}|null null jika bukan berkas lokal atau tidak ditemukan
     */
    public static function lokasiBukti(?string $bukti): ?array
    {
        $nilai = trim((string) $bukti);

        if ($nilai === '' || self::adalahUrlLuar($nilai) || str_contains($nilai, '..') || str_contains($nilai, "\0")) {
            return null;
        }

        $relatif = ltrim(str_starts_with($nilai, 'storage/') ? substr($nilai, 8) : $nilai, '/');

        $kandidat = [
            [self::DISK_BUKTI, $relatif],
            ['public', $relatif],
            [self::DISK_BUKTI, 'kuitansi/'.basename($relatif)],
        ];

        foreach ($kandidat as [$disk, $path]) {
            if (Storage::disk($disk)->exists($path)) {
                return ['disk' => $disk, 'path' => $path];
            }
        }

        return null;
    }

    private static function adalahUrlLuar(string $nilai): bool
    {
        return str_starts_with($nilai, 'http://')
            || str_starts_with($nilai, 'https://')
            || str_starts_with($nilai, 'data:');
    }

    /**
     * Gambar netral (tanpa data toko) yang dipakai saat foto tidak dapat dimuat.
     */
    public static function gambarPengganti(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300">'
            .'<rect width="400" height="300" fill="#eeede9"/>'
            .'<text x="200" y="155" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" fill="#6b6b86">'
            .'Foto tidak dapat ditampilkan</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Memproses info bukti kuitansi untuk ditampilkan di halaman.
     *
     * Berkas yang tersimpan di server TIDAK diberi URL publik. URL-nya mengarah ke route
     * terlindungi (arsip-kuitansi.bukti) sehingga hanya owner yang sudah login yang bisa membuka foto.
     *
     * @param  int|null  $kuitansiId  id kuitansi pemilik bukti (dipakai untuk membentuk URL terlindungi)
     * @return array{
     *     has_bukti: bool,
     *     bukti_kuitansi: ?string,
     *     bukti_kuitansi_name: ?string,
     *     bukti_kuitansi_meta: ?string,
     *     bukti_kuitansi_url: ?string,
     *     fallback_bukti_url: ?string
     * }
     */
    public static function resolveBuktiInfo(?string $bukti, ?int $kuitansiId = null): array
    {
        $trimmed = trim((string) $bukti);
        if ($trimmed === '') {
            return [
                'has_bukti' => false,
                'bukti_kuitansi' => null,
                'bukti_kuitansi_name' => null,
                'bukti_kuitansi_meta' => null,
                'bukti_kuitansi_url' => null,
                'fallback_bukti_url' => null,
            ];
        }

        $fileName = basename(parse_url($trimmed, PHP_URL_PATH) ?: $trimmed);
        $extension = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION) ?: 'JPG');
        $sizeBytes = null;

        if (self::adalahUrlLuar($trimmed)) {
            $url = $trimmed;
        } else {
            $lokasi = self::lokasiBukti($trimmed);
            if ($lokasi !== null) {
                $sizeBytes = Storage::disk($lokasi['disk'])->size($lokasi['path']);
            }

            $url = $kuitansiId !== null ? route('arsip-kuitansi.bukti', $kuitansiId) : null;
        }

        $meta = $extension;
        if ($sizeBytes !== null && $sizeBytes > 0) {
            if ($sizeBytes < 1024 * 1024) {
                $meta .= ' • '.max(1, (int) round($sizeBytes / 1024)).' KB';
            } else {
                $meta .= ' • '.number_format($sizeBytes / (1024 * 1024), 1, ',', '.').' MB';
            }
        } else {
            $meta .= ' • Berkas kuitansi';
        }

        return [
            'has_bukti' => true,
            'bukti_kuitansi' => $trimmed,
            'bukti_kuitansi_name' => $fileName,
            'bukti_kuitansi_meta' => $meta,
            'bukti_kuitansi_url' => $url,
            'fallback_bukti_url' => self::gambarPengganti(),
        ];
    }

    /**
     * Accessor untuk informasi bukti kuitansi terstruktur.
     *
     * @return array{
     *     has_bukti: bool,
     *     bukti_kuitansi: ?string,
     *     bukti_kuitansi_name: ?string,
     *     bukti_kuitansi_meta: ?string,
     *     bukti_kuitansi_url: ?string,
     *     fallback_bukti_url: ?string
     * }
     */
    public function getBuktiInfoAttribute(): array
    {
        return self::resolveBuktiInfo($this->bukti_kuitansi, $this->id);
    }

    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_url'];
    }

    public function getBuktiFileNameAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_name'];
    }

    public function getBuktiFileMetaAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_meta'];
    }

    public function getHasBuktiAttribute(): bool
    {
        return $this->bukti_info['has_bukti'];
    }
}
