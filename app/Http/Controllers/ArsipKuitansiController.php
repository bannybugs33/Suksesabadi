<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArsipKuitansiController extends Controller
{
    /**
     * Daftar arsip kuitansi dengan pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $kuitansi = Kuitansi::query()
            ->where('user_id', Auth::id())
            ->withSum('pembayaran as total_terbayar', 'nominal')
            ->when($request->filled('q'), function ($query) use ($request) {
                $kata = trim((string) $request->query('q'));
                $query->where(function ($sub) use ($kata) {
                    $sub->where('nama_pihak', 'like', "%{$kata}%")
                        ->orWhere('nomor_kuitansi', 'like', "%{$kata}%");
                });
            })
            ->when(in_array($request->query('jenis'), ['utang', 'piutang'], true), fn ($query) => $query->where('jenis', $request->query('jenis')))
            ->when(in_array($request->query('status'), ['belum_lunas', 'dp', 'cicilan', 'lunas'], true), fn ($query) => $query->where('status_pembayaran', $request->query('status')))
            ->when($request->filled('dari'), fn ($query) => $query->whereDate('tanggal_kuitansi', '>=', $request->query('dari')))
            ->when($request->filled('sampai'), fn ($query) => $query->whereDate('tanggal_kuitansi', '<=', $request->query('sampai')))
            ->orderByDesc('tanggal_kuitansi')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('arsip-kuitansi.index', compact('kuitansi'));
    }

    /**
     * Detail satu kuitansi beserta riwayat pembayaran dan form pembayaran.
     */
    public function show(Kuitansi $kuitansi): View
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $kuitansi->load(['pembayaran' => fn ($query) => $query->orderByDesc('tanggal_pembayaran')->orderByDesc('id')]);

        $terbayar = (float) $kuitansi->pembayaran->sum('nominal');
        $sisa = max(0, (float) $kuitansi->nominal_transaksi - $terbayar);

        return view('arsip-kuitansi.show', compact('kuitansi', 'terbayar', 'sisa'));
    }
}
