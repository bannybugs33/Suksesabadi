<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArsipKuitansiController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'in:utang,piutang'],
            'status' => ['nullable', 'in:belum_lunas,dp,cicilan,lunas'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $query = Kuitansi::query()->where('user_id', Auth::id());

        if (! empty($filter['q'])) {
            $kata = $filter['q'];
            $query->where(function ($q) use ($kata) {
                $q->where('nama_pihak', 'like', "%{$kata}%")
                    ->orWhere('nomor_kuitansi', 'like', "%{$kata}%");
            });
        }

        if (! empty($filter['jenis'])) {
            $query->where('jenis', $filter['jenis']);
        }

        if (! empty($filter['status'])) {
            $query->where('status_pembayaran', $filter['status']);
        }

        if (! empty($filter['dari'])) {
            $query->whereDate('tanggal_kuitansi', '>=', $filter['dari']);
        }

        if (! empty($filter['sampai'])) {
            $query->whereDate('tanggal_kuitansi', '<=', $filter['sampai']);
        }

        $kuitansi = $query
            ->orderByDesc('tanggal_kuitansi')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('arsip-kuitansi.index', compact('kuitansi'));
    }

    public function show(Kuitansi $kuitansi): View
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $kuitansi->load(['pembayaran' => fn ($q) => $q->orderByDesc('tanggal_pembayaran')->orderByDesc('id')]);

        $terbayar = (float) $kuitansi->pembayaran->sum('nominal');
        $sisa = max((float) $kuitansi->nominal_transaksi - $terbayar, 0);

        return view('arsip-kuitansi.show', compact('kuitansi', 'terbayar', 'sisa'));
    }
}
