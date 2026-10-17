<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m'],
            'jenis' => ['nullable', 'in:utang,piutang'],
            'status' => ['nullable', 'in:belum_lunas,dp,cicilan,lunas'],
        ]);

        $pembayaran = DB::table('pembayaran')
            ->select('kuitansi_id')
            ->selectRaw('SUM(nominal) AS total_terbayar')
            ->groupBy('kuitansi_id');

        // Owner contoh untuk latihan lokal sebelum login tersedia.
        $query = DB::table('kuitansi as k')
            ->leftJoinSub($pembayaran, 'p', function ($join) {
                $join->on('k.id', '=', 'p.kuitansi_id');
            })
            ->where('k.user_id', 1)
            ->select('k.*')
            ->selectRaw('COALESCE(p.total_terbayar, 0) AS terbayar')
            ->selectRaw(
                'k.nominal_transaksi - COALESCE(p.total_terbayar, 0) AS sisa'
            );

        if (!empty($filter['periode'])) {
            [$tahun, $bulan] = explode('-', $filter['periode']);

            $query->whereYear('k.tanggal_kuitansi', $tahun)
                ->whereMonth('k.tanggal_kuitansi', $bulan);
        }

        if (!empty($filter['jenis'])) {
            $query->where('k.jenis', $filter['jenis']);
        }

        if (!empty($filter['status'])) {
            $query->where('k.status_pembayaran', $filter['status']);
        }

        $kuitansi = $query
            ->orderByDesc('k.tanggal_kuitansi')
            ->get();

        $totalPiutang = $kuitansi
    ->where('jenis', 'piutang')
    ->sum('nominal_transaksi');

$totalUtang = $kuitansi
    ->where('jenis', 'utang')
    ->sum('nominal_transaksi');

$totalTerbayar = $kuitansi->sum('terbayar');
$totalSisa = $kuitansi->sum('sisa');

return view('rekapitulasi.index', compact(
    'kuitansi',
    'totalPiutang',
    'totalUtang',
    'totalTerbayar',
    'totalSisa'
));
    }
}