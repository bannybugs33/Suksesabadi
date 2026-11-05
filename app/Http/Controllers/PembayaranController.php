<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    /**
     * Catat pembayaran baru untuk sebuah kuitansi (utang / piutang),
     * lalu perbarui status_pembayaran secara otomatis.
     */
    public function store(Request $request, Kuitansi $kuitansi): RedirectResponse
    {
        // Hanya pemilik kuitansi yang boleh mencatat pembayaran.
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $data = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1'],
            'tanggal_pembayaran' => ['required', 'date', 'before_or_equal:today'],
            'metode_pembayaran' => ['required', 'in:Tunai,Transfer bank'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'nominal.required' => 'Nominal pembayaran wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal Rp1.',
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
            'tanggal_pembayaran.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
        ]);

        $statusBaru = DB::transaction(function () use ($kuitansi, $data): string {
            // Ambil ulang di dalam transaksi supaya sisa tagihan akurat.
            $kuitansi = Kuitansi::query()->lockForUpdate()->findOrFail($kuitansi->id);

            $nominalTransaksi = (float) $kuitansi->nominal_transaksi;
            $terbayar = (float) $kuitansi->pembayaran()->sum('nominal');
            $sisa = round($nominalTransaksi - $terbayar, 2);
            $nominal = round((float) $data['nominal'], 2);

            if ($kuitansi->dibatalkan_pada !== null) {
                throw ValidationException::withMessages([
                    'nominal' => 'Transaksi ini sudah dibatalkan, pembayaran tidak bisa ditambah.',
                ]);
            }

            if ($kuitansi->status_pembayaran === 'lunas' || $sisa <= 0) {
                throw ValidationException::withMessages([
                    'nominal' => 'Kuitansi ini sudah lunas.',
                ]);
            }

            if ($nominal > $sisa) {
                throw ValidationException::withMessages([
                    'nominal' => 'Nominal melebihi sisa tagihan (Rp'.number_format($sisa, 0, ',', '.').').',
                ]);
            }

            $kuitansi->pembayaran()->create([
                'nominal' => $nominal,
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'catatan' => $data['catatan'] ?? null,
                'dicatat_oleh' => Auth::user()->name,
            ]);

            $status = self::tentukanStatus(
                $nominalTransaksi,
                $terbayar + $nominal,
                $kuitansi->pembayaran()->count()
            );

            $kuitansi->update(['status_pembayaran' => $status]);

            return $status;
        });

        $label = $kuitansi->jenis === 'piutang' ? 'Piutang' : 'Utang';

        // Pembayaran dari halaman detail arsip kembali ke halaman detail yang sama.
        if ($request->input('redirect_to') === 'arsip') {
            return redirect()
                ->route('arsip-kuitansi.show', $kuitansi)
                ->with('status', $statusBaru === 'lunas'
                    ? "Pembayaran dicatat. {$label} {$kuitansi->nama_pihak} sekarang LUNAS."
                    : 'Pembayaran berhasil dicatat. Status sekarang: '.Kuitansi::labelStatus($statusBaru).'.');
        }

        if ($statusBaru === 'lunas') {
            // Kuitansi lunas tidak tampil lagi di daftar jatuh tempo.
            return redirect()
                ->route('jatuh-tempo.index')
                ->with('status', "{$label} {$kuitansi->nama_pihak} ({$kuitansi->nomor_kuitansi}) sudah LUNAS.");
        }

        return redirect()
            ->route('jatuh-tempo.index', ['selected' => $kuitansi->id])
            ->with('status', 'Pembayaran berhasil dicatat. Status sekarang: '.strtoupper(str_replace('_', ' ', $statusBaru)).'.');
    }

    /**
     * Aturan status ada di Kuitansi::tentukanStatus (dipakai juga oleh pembayaran awal).
     */
    public static function tentukanStatus(float $nominalTransaksi, float $totalTerbayar, int $jumlahPembayaran): string
    {
        return Kuitansi::tentukanStatus($nominalTransaksi, $totalTerbayar, $jumlahPembayaran);
    }
}
