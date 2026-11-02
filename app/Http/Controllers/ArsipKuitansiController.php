<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArsipKuitansiController extends Controller
{
    /**
     * Daftar arsip kuitansi dengan pencarian dasar (nama pihak atau nomor kuitansi)
     * dan filter jenis serta status.
     *
     * Angka dan label di daftar memakai aturan yang sama dengan halaman detail
     * (sisa = nominal - pembayaran, label status dan tempo dari model Kuitansi),
     * jadi satu kuitansi tidak pernah tampil berbeda antara daftar dan detail.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'in:utang,piutang'],
            'status' => ['nullable', 'in:belum_lunas,dp,cicilan,lunas'],
            'urut' => ['nullable', 'in:terbaru,terlama'],
        ]);

        $query = Kuitansi::query()
            ->where('user_id', Auth::id())
            ->withSum('pembayaran as total_terbayar', 'nominal');

        $kata = trim((string) ($filter['q'] ?? ''));
        if ($kata !== '') {
            $query->where(function ($q) use ($kata) {
                $q->where('nama_pihak', 'like', "%{$kata}%")
                    ->orWhere('nomor_kuitansi', 'like', "%{$kata}%")
                    ->orWhere('nomor_kuitansi_fisik', 'like', "%{$kata}%");
            });
        }

        if (! empty($filter['jenis'])) {
            $query->where('jenis', $filter['jenis']);
        }

        if (! empty($filter['status'])) {
            $query->where('status_pembayaran', $filter['status']);
        }

        $urut = $filter['urut'] ?? 'terbaru';
        $arah = $urut === 'terlama' ? 'asc' : 'desc';

        $kuitansi = $query
            ->orderBy('tanggal_kuitansi', $arah)
            ->orderBy('id', $arah)
            ->paginate(5)
            ->withQueryString();

        $tanggalAcuan = Carbon::now()->startOfDay();

        $kuitansi->getCollection()->transform(function (Kuitansi $item) use ($tanggalAcuan) {
            $sisa = max(round((float) $item->nominal_transaksi - (float) $item->total_terbayar, 2), 0);

            // Penanda tempo hanya untuk tagihan yang masih bersisa (aktif).
            [$item->tempo_teks, $item->tempo_kelas] = ($sisa > 0 && $item->tanggal_jatuh_tempo)
                ? Kuitansi::badgeTempo($item->tanggal_jatuh_tempo, $tanggalAcuan)
                : [null, null];

            return $item;
        });

        return view('arsip-kuitansi.index', [
            'kuitansi' => $kuitansi,
            'kata' => $kata,
            'urut' => $urut,
            'adaFilter' => $kata !== '' || ! empty($filter['jenis']) || ! empty($filter['status']),
        ]);
    }

    public function show(Kuitansi $kuitansi): View
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $kuitansi->load(['pembayaran' => fn ($q) => $q->orderByDesc('tanggal_pembayaran')->orderByDesc('id')]);

        $terbayar = (float) $kuitansi->pembayaran->sum('nominal');
        $sisa = max((float) $kuitansi->nominal_transaksi - $terbayar, 0);

        return view('arsip-kuitansi.show', compact('kuitansi', 'terbayar', 'sisa'));
    }

    /**
     * Tampilkan foto/berkas bukti kuitansi.
     *
     * Berkas disimpan di disk privat (tanpa URL publik), jadi satu-satunya jalan untuk
     * membukanya adalah route ini: wajib login dan hanya untuk pemilik kuitansi.
     */
    public function bukti(Kuitansi $kuitansi): StreamedResponse
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $lokasi = Kuitansi::lokasiBukti($kuitansi->bukti_kuitansi);
        abort_if($lokasi === null, 404);

        $headers = [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ];

        // SVG bisa memuat skrip jika dibuka langsung; kunci agar tidak bisa dieksekusi.
        if (strtolower(pathinfo($lokasi['path'], PATHINFO_EXTENSION)) === 'svg') {
            $headers['Content-Security-Policy'] = "default-src 'none'; style-src 'unsafe-inline'";
        }

        return Storage::disk($lokasi['disk'])->response($lokasi['path'], basename($lokasi['path']), $headers);
    }
}
