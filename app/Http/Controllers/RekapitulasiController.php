<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekapitulasiController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m'],
            'arah' => ['nullable', 'in:penerimaan,pengeluaran'],
        ]);

        $userId = Auth::id() ?? 1;
        $periode = $filter['periode'] ?? null;
        $arah = $filter['arah'] ?? null;

        $dasar = DB::table('pembayaran as p')
            ->join('kuitansi as k', 'k.id', '=', 'p.kuitansi_id')
            ->where('k.user_id', $userId)
            // Pembayaran dibatalkan tidak dihitung. Transaksi dibatalkan dengan uang dikembalikan
            // juga tidak dihitung; bila uang tidak dikembalikan, uangnya memang sudah diterima/dibayar.
            ->where('p.status', 'aktif')
            ->whereRaw('(k.dibatalkan_pada IS NULL OR COALESCE(k.uang_dikembalikan, 0) = 0)');

        // Pilihan periode: bulan yang memang memiliki pembayaran, terbaru lebih dulu.
        $daftarPeriode = (clone $dasar)
            ->pluck('p.tanggal_pembayaran')
            ->map(fn ($tanggal) => Carbon::parse($tanggal)->format('Y-m'))
            ->when($periode, fn ($bulan) => $bulan->push($periode))
            ->unique()
            ->sortDesc()
            ->values()
            ->mapWithKeys(fn (string $bulan) => [
                $bulan => Carbon::createFromFormat('Y-m-d', $bulan.'-01')->translatedFormat('F Y'),
            ]);

        $query = (clone $dasar)
            ->select([
                'p.id', 'p.nominal', 'p.tanggal_pembayaran', 'p.metode_pembayaran',
                'k.id as kuitansi_id', 'k.nomor_kuitansi', 'k.nama_pihak', 'k.jenis',
            ]);

        if ($periode) {
            [$tahun, $bulan] = explode('-', $periode);

            $query->whereYear('p.tanggal_pembayaran', $tahun)
                ->whereMonth('p.tanggal_pembayaran', $bulan);
        }

        if ($arah) {
            $query->where('k.jenis', $arah === 'penerimaan' ? 'piutang' : 'utang');
        }

        $pembayaran = $query
            ->orderByDesc('p.tanggal_pembayaran')
            ->orderByDesc('p.id')
            ->get()
            ->map(function ($baris) {
                $baris->nominal = (float) $baris->nominal;
                $baris->tanggal = Carbon::parse($baris->tanggal_pembayaran);
                $baris->arah = $baris->jenis === 'piutang' ? 'penerimaan' : 'pengeluaran';
                $baris->metode = Pembayaran::labelMetode($baris->metode_pembayaran);

                return $baris;
            });

        $totalPenerimaan = (float) $pembayaran->where('arah', 'penerimaan')->sum('nominal');
        $totalPengeluaran = (float) $pembayaran->where('arah', 'pengeluaran')->sum('nominal');

        return view('rekapitulasi.index', [
            'pembayaran' => $pembayaran,
            'periode' => $periode,
            'arah' => $arah,
            'daftarPeriode' => $daftarPeriode,
            'totalPenerimaan' => $totalPenerimaan,
            'totalPengeluaran' => $totalPengeluaran,
            'posisiBersih' => $totalPenerimaan - $totalPengeluaran,
            'jumlahPembayaran' => $pembayaran->count(),
            'dataPer' => Carbon::now(),
            'adaFilter' => $periode !== null || $arah !== null,
        ]);
    }
}
