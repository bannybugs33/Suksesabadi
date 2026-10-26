<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Toko Tempo.
     * Seluruh angka dihitung dari database (tanpa data dummy).
     */
    public function index(): View
    {
        $userId = Auth::id() ?? 1;
        $tanggalAcuan = request()->filled('tanggal_acuan')
            ? Carbon::parse(request()->query('tanggal_acuan'))->startOfDay()
            : Carbon::now()->startOfDay();

        // Total pembayaran dan tanggal pembayaran terakhir per kuitansi
        $pembayaran = DB::table('pembayaran')
            ->select('kuitansi_id')
            ->selectRaw('SUM(nominal) AS total_terbayar')
            ->selectRaw('MAX(tanggal_pembayaran) AS terakhir_bayar')
            ->groupBy('kuitansi_id');

        $kuitansi = DB::table('kuitansi as k')
            ->leftJoinSub($pembayaran, 'p', function ($join) {
                $join->on('k.id', '=', 'p.kuitansi_id');
            })
            ->where('k.user_id', $userId)
            ->select('k.*')
            ->selectRaw('COALESCE(p.total_terbayar, 0) AS terbayar')
            ->selectRaw('k.nominal_transaksi - COALESCE(p.total_terbayar, 0) AS sisa')
            ->selectRaw('p.terakhir_bayar AS terakhir_bayar')
            ->orderByDesc('k.tanggal_kuitansi')
            ->get();

        $kuitansi->transform(function ($item) use ($tanggalAcuan) {
            $tempo = Kuitansi::infoTempo($item->tanggal_jatuh_tempo, $item->status_pembayaran, $tanggalAcuan);

            $item->tempo_label = $tempo['label'];
            $item->tempo_code = $tempo['code'];

            return $item;
        });

        $belumLunas = $kuitansi->where('status_pembayaran', '!=', 'lunas');

        // Kuitansi yang perlu perhatian: sudah terlambat atau jatuh tempo dalam 3 hari (H-3).
        // Diurutkan dari jatuh tempo paling awal agar yang paling mendesak tampil lebih dulu.
        $itemsPerluPerhatian = $belumLunas
            ->whereIn('tempo_code', Kuitansi::KODE_PENGINGAT)
            ->sortBy('tanggal_jatuh_tempo')
            ->values();

        $perluPerhatianCount = $itemsPerluPerhatian->count();
        $terlambatCount = $itemsPerluPerhatian->where('tempo_code', 'terlambat')->count();
        $besokCount = $itemsPerluPerhatian->where('tempo_code', 'h-1')->count();

        // Popup pengingat: prioritaskan yang sudah terlambat paling lama, lalu yang terdekat.
        $popupItem = $itemsPerluPerhatian->first();

        // 4 kartu metrik utama
        $piutangAktif = $belumLunas->where('jenis', 'piutang');
        $utangAktif = $belumLunas->where('jenis', 'utang');

        $sisaPiutangPelanggan = (float) $piutangAktif->sum('sisa');
        $kuitansiPelangganCount = $piutangAktif->count();

        $sisaUtangSupplier = (float) $utangAktif->sum('sisa');
        $kuitansiSupplierCount = $utangAktif->count();

        $awalBulan = $tanggalAcuan->copy()->startOfMonth();
        $akhirBulan = $tanggalAcuan->copy()->endOfMonth();
        $lunasBulanIniItems = $kuitansi
            ->where('status_pembayaran', 'lunas')
            ->filter(fn($item) => $item->terakhir_bayar
                && Carbon::parse($item->terakhir_bayar)->between($awalBulan, $akhirBulan));

        $lunasBulanIni = (float) $lunasBulanIniItems->sum('nominal_transaksi');
        $lunasBulanIniCount = $lunasBulanIniItems->count();

        $totalNilaiTercatat = (float) $kuitansi->sum('nominal_transaksi');

        $chartData = $this->buatChartData($userId, $tanggalAcuan);
        $statusPembayaran = $this->buatStatusPembayaran($kuitansi);
        $aktivitasTerbaru = $this->buatAktivitasTerbaru($userId, $kuitansi, $tanggalAcuan);

        return view('dashboard', compact(
            'tanggalAcuan',
            'popupItem',
            'kuitansi',
            'itemsPerluPerhatian',
            'perluPerhatianCount',
            'terlambatCount',
            'besokCount',
            'sisaPiutangPelanggan',
            'kuitansiPelangganCount',
            'sisaUtangSupplier',
            'kuitansiSupplierCount',
            'lunasBulanIni',
            'lunasBulanIniCount',
            'totalNilaiTercatat',
            'chartData',
            'statusPembayaran',
            'aktivitasTerbaru'
        ));
    }

    /**
     * Arus piutang masuk dan utang keluar (nilai pembayaran) selama 6 bulan terakhir.
     * 'piutang' dan 'utang' berisi tinggi batang dalam persen, '*_nominal' berisi nilai asli.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buatChartData(int $userId, Carbon $tanggalAcuan): array
    {
        $jumlahBulan = 6;
        $awal = $tanggalAcuan->copy()->startOfMonth()->subMonths($jumlahBulan - 1);

        $perBulan = DB::table('pembayaran as p')
            ->join('kuitansi as k', 'k.id', '=', 'p.kuitansi_id')
            ->where('k.user_id', $userId)
            ->whereDate('p.tanggal_pembayaran', '>=', $awal->toDateString())
            ->get(['p.nominal', 'p.tanggal_pembayaran', 'k.jenis'])
            ->groupBy(fn($row) => Carbon::parse($row->tanggal_pembayaran)->format('Y-m'));

        $data = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $bulan = $awal->copy()->addMonths($i);
            $baris = $perBulan->get($bulan->format('Y-m'), collect());

            $data[] = [
                'bulan' => $bulan->translatedFormat('M'),
                'piutang_nominal' => (float) $baris->where('jenis', 'piutang')->sum('nominal'),
                'utang_nominal' => (float) $baris->where('jenis', 'utang')->sum('nominal'),
                'piutang' => 0,
                'utang' => 0,
            ];
        }

        $maks = max(array_merge(
            array_column($data, 'piutang_nominal'),
            array_column($data, 'utang_nominal')
        ));

        foreach ($data as $i => $baris) {
            foreach (['piutang', 'utang'] as $jenis) {
                $nilai = $baris["{$jenis}_nominal"];
                $data[$i][$jenis] = ($maks > 0 && $nilai > 0)
                    ? max((int) round($nilai / $maks * 100), 3)
                    : 0;
            }
        }

        return $data;
    }

    /**
     * Rincian jumlah kuitansi per status pembayaran.
     *
     * @return array<string, array<string, mixed>>
     */
    private function buatStatusPembayaran(Collection $kuitansi): array
    {
        $definisi = [
            'lunas' => ['Lunas', '#474e68'],
            'cicilan' => ['Cicilan', '#1f5f8b'],
            'dp' => ['DP', '#b45309'],
            'belum_lunas' => ['Belum lunas', '#91283b'],
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
     * @return array<int, array<string, mixed>>
     */
    private function buatAktivitasTerbaru(int $userId, Collection $kuitansi, Carbon $tanggalAcuan): array
    {
        $rupiah = fn($nilai) => 'Rp' . number_format((float) $nilai, 0, ',', '.');
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
                'type' => 'cicilan',
                'title' => 'Pembayaran dicatat',
                'subtitle' => $baris->nama_pihak . ' • ' . $rupiah($baris->nominal),
                'tanggal' => Carbon::parse($baris->tanggal_pembayaran),
                'badge_bg' => '#f3f4f6',
                'badge_color' => '#4b5563',
            ]);
        }

        foreach ($kuitansi->take(5) as $item) {
            $aktivitas->push([
                'type' => 'kuitansi',
                'title' => 'Kuitansi baru ditambahkan',
                'subtitle' => $item->nama_pihak . ' • ' . $rupiah($item->nominal_transaksi),
                'tanggal' => Carbon::parse($item->tanggal_kuitansi),
                'badge_bg' => '#fef2f2',
                'badge_color' => '#ef4444',
            ]);
        }

        foreach ($kuitansi->where('status_pembayaran', 'lunas')->whereNotNull('terakhir_bayar') as $item) {
            $aktivitas->push([
                'type' => 'lunas',
                'title' => 'Kuitansi ditandai lunas',
                'subtitle' => $item->nama_pihak . ' • ' . $rupiah($item->nominal_transaksi),
                'tanggal' => Carbon::parse($item->terakhir_bayar),
                'badge_bg' => '#eff6ff',
                'badge_color' => '#3b82f6',
            ]);
        }

        return $aktivitas
            ->sortByDesc(fn($a) => $a['tanggal']->timestamp)
            ->take(4)
            ->map(function ($a) use ($tanggalAcuan) {
                $a['time'] = match (true) {
                    $a['tanggal']->isSameDay($tanggalAcuan) => 'Hari ini',
                    $a['tanggal']->isSameDay($tanggalAcuan->copy()->subDay()) => 'Kemarin',
                    default => $a['tanggal']->translatedFormat('d M Y'),
                };

                return $a;
            })
            ->values()
            ->all();
    }
}
