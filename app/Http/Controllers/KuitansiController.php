<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KuitansiController extends Controller
{
    /**
     * Tampilkan formulir pembuatan kuitansi baru.
     */
    public function create(): View
    {
        $today = now();
        $tanggalKuitansi = $today->format('Y-m-d');
        $tanggalJatuhTempo = $today->copy()->addDays(14)->format('Y-m-d');

        // Format nomor kuitansi sementara seperti KWT-240929
        $baseNomor = 'KWT-'.$today->format('ymd');
        $nomorSementara = $baseNomor;
        $counter = 1;
        while (Kuitansi::where('nomor_kuitansi', $nomorSementara)->exists()) {
            $counter++;
            $nomorSementara = $baseNomor.'-'.$counter;
        }

        return view('kuitansi.create', compact('tanggalKuitansi', 'tanggalJatuhTempo', 'nomorSementara'));
    }

    /**
     * Simpan data kuitansi baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        // Bersihkan formatting rupiah (misal "Rp 1.000.000" -> 1000000)
        $cleanNominalTransaksi = (float) preg_replace('/[^\d]/', '', (string) $request->input('nominal_transaksi'));
        $cleanNominalDibayar = (float) preg_replace('/[^\d]/', '', (string) $request->input('nominal_dibayar', 0));

        $request->merge([
            'nominal_transaksi' => $cleanNominalTransaksi,
            'nominal_dibayar' => $cleanNominalDibayar,
        ]);

        $validated = $request->validate([
            'tanggal_kuitansi' => ['required', 'date'],
            'nama_pihak' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:utang,piutang'],
            'nominal_transaksi' => ['required', 'numeric', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'status_pembayaran' => ['required', 'in:belum_lunas,dp,cicilan,lunas'],
            'nominal_dibayar' => ['nullable', 'numeric', 'min:0'],
            'tanggal_jatuh_tempo' => ['required', 'date'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ]);

        // Role pihak
        $rolePihak = $validated['jenis'] === 'piutang' ? 'Pelanggan' : 'Supplier';

        // Buat nomor kuitansi unik berbasis tanggal
        $kuitansiDate = Carbon::parse($validated['tanggal_kuitansi']);
        $baseNomor = 'KWT-'.$kuitansiDate->format('ymd');
        $nomorKuitansi = $baseNomor;
        $counter = 1;
        while (Kuitansi::where('nomor_kuitansi', $nomorKuitansi)->exists()) {
            $counter++;
            $nomorKuitansi = $baseNomor.'-'.$counter;
        }

        // Simpan file bukti kuitansi jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $storedFile = $request->file('bukti')->store('kuitansi', 'public');
            $buktiPath = 'storage/'.$storedFile;
        }

        // Tentukan status pembayaran akhir
        $statusPembayaran = $validated['status_pembayaran'];
        if ($statusPembayaran === 'belum_lunas' && $cleanNominalDibayar > 0) {
            $statusPembayaran = $cleanNominalDibayar >= $cleanNominalTransaksi ? 'lunas' : 'cicilan';
        }

        $kuitansi = Kuitansi::create([
            'user_id' => Auth::id(),
            'nomor_kuitansi' => $nomorKuitansi,
            'nama_pihak' => $validated['nama_pihak'],
            'role_pihak' => $rolePihak,
            'jenis' => $validated['jenis'],
            'nominal_transaksi' => $validated['nominal_transaksi'],
            'status_pembayaran' => $statusPembayaran,
            'tanggal_kuitansi' => $validated['tanggal_kuitansi'],
            'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
            'keterangan' => $validated['catatan'] ?? null,
            'bukti_kuitansi' => $buktiPath,
        ]);

        // Jika ada pembayaran awal
        if ($cleanNominalDibayar > 0 && in_array($statusPembayaran, ['dp', 'cicilan', 'lunas'])) {
            Pembayaran::create([
                'kuitansi_id' => $kuitansi->id,
                'nominal' => $cleanNominalDibayar,
                'tanggal_pembayaran' => $validated['tanggal_kuitansi'],
                'metode_pembayaran' => 'Tunai',
                'catatan' => 'Pembayaran awal ('.strtoupper($statusPembayaran).')',
                'dicatat_oleh' => Auth::user()->name ?? 'Owner • Toko Tempo',
            ]);
        }

        return redirect()->route('arsip-kuitansi.show', $kuitansi->id)
            ->with('status', "Kuitansi {$nomorKuitansi} ({$kuitansi->nama_pihak}) berhasil disimpan!");
    }
}
