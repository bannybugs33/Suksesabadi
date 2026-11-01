<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Toko Sukses Abadi.
     *
     * Semua angka dihitung dari data nyata (tabel kuitansi dan pembayaran), dengan aturan
     * yang sama seperti Arsip, Jatuh Tempo, dan Rekapitulasi:
     *  - terbayar = jumlah pembayaran, sisa = nominal transaksi - terbayar;
     *  - tagihan "aktif" = yang masih bersisa (sisa > 0);
     *  - pengingat = aktif dan terlambat atau jatuh tempo dalam 7 hari (Kuitansi::HARI_PENGINGAT).
     */
    public function index(Request $request): View
    {
        $userId = Auth::id() ?? 1;
        $tanggalAcuan = $request->filled('tanggal_acuan')
            ? Carbon::parse($request->query('tanggal_acuan'))->startOfDay()
            : Carbon::now()->startOfDay();

        $semua = $this->ambilKuitansi($userId, $tanggalAcuan);
        $aktif = $semua->filter(fn ($k) => $k->sisa > 0);

        // Sisa piutang (uang toko yang belum dibayar pelanggan) dan sisa utang (yang belum dibayar toko).
        $piutang = $aktif->where('jenis', 'piutang');
        $utang = $aktif->where('jenis', 'utang');

        $sisaPiutang = (float) $piutang->sum('sisa');
        $sisaUtang = (float) $utang->sum('sisa');

        // Pengingat H-7: yang paling mendesak (paling awal jatuh tempo) tampil lebih dulu.
        $pengingat = $aktif
            ->whereIn('tempo_code', Kuitansi::KODE_PENGINGAT)
            ->sortBy([['tanggal_jatuh_tempo', 'asc'], ['id', 'asc']])
            ->values();

        // Jatuh tempo terdekat: dua tagihan aktif yang belum lewat, mulai dari yang paling dekat.
        $terdekat = $aktif
            ->filter(fn ($k) => $k->tanggal_jatuh_tempo
                && Carbon::parse($k->tanggal_jatuh_tempo)->startOfDay()->gte($tanggalAcuan))
            ->sortBy([['tanggal_jatuh_tempo', 'asc'], ['id', 'asc']])
            ->take(2)
            ->values();

        $grafik = $this->buatGrafik($userId, $tanggalAcuan);
        $bulanIni = end($grafik['bulan']);

        return view('dashboard', [
            'tanggalAcuan' => $tanggalAcuan,
            'salam' => $this->salam(),
            'sisaPiutang' => $sisaPiutang,
            'jumlahPiutang' => $piutang->count(),
            'sisaUtang' => $sisaUtang,
            'jumlahUtang' => $utang->count(),
            'posisiBersih' => $sisaPiutang - $sisaUtang,
            'pengingat' => $pengingat,
            'perluPerhatianCount' => $pengingat->count(),
            'terlambatCount' => $pengingat->where('tempo_code', 'terlambat')->count(),
            'terdekat' => $terdekat,
            'grafik' => $grafik,
            'dibayarBulanIni' => $bulanIni['piutang'] + $bulanIni['utang'],
            'piutangMasukBulanIni' => $bulanIni['piutang'],
            'utangKeluarBulanIni' => $bulanIni['utang'],
            'statusPembayaran' => $this->buatStatusPembayaran($semua),
            'totalNilaiTercatat' => (float) $semua->sum('nominal_transaksi'),
            'totalTerbayar' => (float) $semua->sum('terbayar'),
            'totalSisa' => $sisaPiutang + $sisaUtang,
            'aktivitasTerbaru' => $this->buatAktivitasTerbaru($userId, $semua),
        ]);
    }

    /**
     * Seluruh kuitansi milik owner beserta total terbayar, sisa, dan kode tempo.
     *
     * @return Collection<int, object>
     */
    private function ambilKuitansi(int $userId, Carbon $tanggalAcuan): Collection
    {
        $pembayaran = DB::table('pembayaran')
            ->select('kuitansi_id')
            ->selectRaw('SUM(nominal) AS total_terbayar')
            ->selectRaw('MAX(tanggal_pembayaran) AS terakhir_bayar')
            ->groupBy('kuitansi_id');

        return DB::table('kuitansi as k')
            ->leftJoinSub($pembayaran, 'p', function ($join) {
                $join->on('k.id', '=', 'p.kuitansi_id');
            })
            ->where('k.user_id', $userId)
            ->select('k.*')
            ->selectRaw('COALESCE(p.total_terbayar, 0) AS terbayar')
            ->selectRaw('ROUND(k.nominal_transaksi - COALESCE(p.total_terbayar, 0), 2) AS sisa')
            ->selectRaw('p.terakhir_bayar AS terakhir_bayar')
            ->orderByDesc('k.tanggal_kuitansi')
            ->orderByDesc('k.id')
            ->get()
            ->map(function ($item) use ($tanggalAcuan) {
                $item->sisa = (float) $item->sisa;
                $item->terbayar = (float) $item->terbayar;

                // Tagihan yang sudah tidak bersisa dianggap aman, apa pun isi kolom statusnya.
                $tempo = Kuitansi::infoTempo(
                    $item->tanggal_jatuh_tempo,
                    $item->sisa > 0 ? $item->status_pembayaran : 'lunas',
                    $tanggalAcuan
                );

                $item->tempo_code = $tempo['code'];
                $item->tempo_label = $tempo['label'];
                [$item->badge, $item->badge_kelas] = $this->badgeTempo($item, $tanggalAcuan);

                return $item;
            });
    }

    /**
     * Teks singkat untuk badge tempo dan warna yang dipakai.
     *
     * @return array{0: string, 1: string}
     */
    private function badgeTempo(object $item, Carbon $tanggalAcuan): array
    {
        if (! $item->tanggal_jatuh_tempo) {
            return ['Tanpa tempo', 'netral'];
        }

        $tempo = Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
        $selisih = (int) abs($tanggalAcuan->diffInDays($tempo));

        return match (true) {
            $tempo->lt($tanggalAcuan) => ["Terlambat {$selisih} hari", 'merah'],
            $selisih === 0 => ['Hari ini', 'kuning'],
            $selisih <= Kuitansi::HARI_PENGINGAT => ["H-{$selisih}", 'kuning'],
            default => ["{$selisih} hari lagi", 'netral'],
        };
    }

    /**
     * Sapaan sesuai jam saat ini (zona waktu aplikasi).
     */
    private function salam(): string
    {
        $jam = (int) Carbon::now()->format('G');

        return match (true) {
            $jam >= 4 && $jam <= 10 => 'Selamat pagi',
            $jam >= 11 && $jam <= 14 => 'Selamat siang',
            $jam >= 15 && $jam <= 17 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    /**
     * Pembayaran 6 bulan terakhir, dipisah menjadi piutang masuk dan utang keluar,
     * berikut koordinat grafik garis (SVG 300x120). Diambil dari tanggal pembayaran.
     *
     * @return array{bulan: array<int, array<string, mixed>>, path_piutang: string, path_utang: string}
     */
    private function buatGrafik(int $userId, Carbon $tanggalAcuan): array
    {
        $jumlahBulan = 6;
        $awal = $tanggalAcuan->copy()->startOfMonth()->subMonths($jumlahBulan - 1);

        $perBulan = DB::table('pembayaran as p')
            ->join('kuitansi as k', 'k.id', '=', 'p.kuitansi_id')
            ->where('k.user_id', $userId)
            ->whereDate('p.tanggal_pembayaran', '>=', $awal->toDateString())
            ->get(['p.nominal', 'p.tanggal_pembayaran', 'k.jenis'])
            ->groupBy(fn ($row) => Carbon::parse($row->tanggal_pembayaran)->format('Y-m'));

        $bulan = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $tanggal = $awal->copy()->addMonths($i);
            $baris = $perBulan->get($tanggal->format('Y-m'), collect());

            $bulan[] = [
                'label' => $tanggal->translatedFormat('M'),
                'label_penuh' => $tanggal->translatedFormat('F Y'),
                'piutang' => (float) $baris->where('jenis', 'piutang')->sum('nominal'),
                'utang' => (float) $baris->where('jenis', 'utang')->sum('nominal'),
            ];
        }

        $maks = max(1.0, ...array_merge(array_column($bulan, 'piutang'), array_column($bulan, 'utang')));

        foreach ($bulan as $i => $b) {
            $bulan[$i]['x'] = round(24 + $i * (252 / ($jumlahBulan - 1)), 1);
            $bulan[$i]['y_piutang'] = round(96 - ($b['piutang'] / $maks) * 72, 1);
            $bulan[$i]['y_utang'] = round(96 - ($b['utang'] / $maks) * 72, 1);
        }

        return [
            'bulan' => $bulan,
            'path_piutang' => $this->jalurHalus(array_column($bulan, 'x'), array_column($bulan, 'y_piutang')),
            'path_utang' => $this->jalurHalus(array_column($bulan, 'x'), array_column($bulan, 'y_utang')),
        ];
    }

    /**
     * Jalur SVG berkurva halus melalui titik-titik (kurva Bezier dengan kontrol di tengah sumbu x).
     *
     * @param  array<int, float>  $xs
     * @param  array<int, float>  $ys
     */
    private function jalurHalus(array $xs, array $ys): string
    {
        $d = "M{$xs[0]},{$ys[0]}";

        for ($i = 1, $n = count($xs); $i < $n; $i++) {
            $tengah = round(($xs[$i - 1] + $xs[$i]) / 2, 1);
            $d .= " C{$tengah},{$ys[$i - 1]} {$tengah},{$ys[$i]} {$xs[$i]},{$ys[$i]}";
        }

        return $d;
    }

    /**
     * Jumlah kuitansi per status pembayaran (semua kuitansi, sama dengan filter status di Arsip).
     *
     * @return array<string, array<string, mixed>>
     */
    private function buatStatusPembayaran(Collection $kuitansi): array
    {
        $definisi = [
            'lunas' => ['Lunas', '#2a2a5c'],
            'cicilan' => ['Cicilan', '#4c3a78'],
            'dp' => ['DP', '#7b4768'],
            'belum_lunas' => ['Belum lunas', '#a3153f'],
        ];

        $total = $kuitansi->count();
        $hasil = [];

        foreach ($definisi as $kunci => [$label, $warna]) {
            $jumlah = $kuitansi->where('status_pembayaran', $kunci)->count();

            $hasil[$kunci] = [
                'label' => $label,
                'count' => $jumlah,
                'pct' => $total > 0 ? (int) round($jumlah / $total * 100) : 0,
                'color' => $warna,
            ];
        }

        return $hasil;
    }

    /**
     * Aktivitas terbaru: pembayaran dicatat, kuitansi baru, dan kuitansi lunas.
     *
     * @return array<int, array<string, string>>
     */
    private function buatAktivitasTerbaru(int $userId, Collection $kuitansi): array
    {
        $rupiah = fn ($nilai) => 'Rp'.number_format((float) $nilai, 0, ',', '.');
        $aktivitas = collect();

        $pembayaranTerbaru = DB::table('pembayaran as p')
            ->join('kuitansi as k', 'k.id', '=', 'p.kuitansi_id')
            ->where('k.user_id', $userId)
            ->orderByDesc('p.tanggal_pembayaran')
            ->orderByDesc('p.id')
            ->limit(5)
            ->get(['p.nominal', 'p.tanggal_pembayaran', 'k.nama_pihak']);

        foreach ($pembayaranTerbaru as $baris) {
            $aktivitas->push([
                'judul' => 'Pembayaran '.$rupiah($baris->nominal).' dicatat',
                'nama' => $baris->nama_pihak,
                'tanggal' => Carbon::parse($baris->tanggal_pembayaran),
            ]);
        }

        foreach ($kuitansi->take(5) as $item) {
            $aktivitas->push([
                'judul' => 'Kuitansi baru '.$rupiah($item->nominal_transaksi),
                'nama' => $item->nama_pihak,
                'tanggal' => Carbon::parse($item->tanggal_kuitansi),
            ]);
        }

        return $aktivitas
            ->sortByDesc(fn ($a) => $a['tanggal']->timestamp)
            ->take(4)
            ->map(fn ($a) => [
                'judul' => $a['judul'],
                'subjudul' => $a['nama'].' • '.$a['tanggal']->translatedFormat('d M Y'),
            ])
            ->values()
            ->all();
    }
}
