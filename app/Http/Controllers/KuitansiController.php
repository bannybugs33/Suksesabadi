<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Mengarsipkan kuitansi baru.
 *
 * Aturan (revisi analis sistem, poin 2):
 *  - tepat satu foto per arsip (JPG/PNG), tanpa PDF dan tanpa lampiran tambahan;
 *  - nomor arsip dibuat otomatis oleh sistem; nomor kuitansi fisik opsional dan terpisah;
 *  - owner mengisi nominal pembayaran (bukan memilih status); status dihitung dengan aturan
 *    yang sama seperti pembayaran selanjutnya;
 *  - jatuh tempo wajib hanya jika masih ada sisa; transaksi langsung lunas boleh tanpa jatuh tempo.
 */
class KuitansiController extends Controller
{
    /**
     * Tampilkan formulir pembuatan kuitansi baru.
     */
    public function create(): View
    {
        return view('kuitansi.create', [
            'hariIni' => now()->toDateString(),
            'nomorSementara' => Kuitansi::nomorArsipBerikut(now()),
        ]);
    }

    /**
     * Nomor arsip sementara untuk tanggal kuitansi yang dipilih (ditampilkan di Ringkasan Arsip).
     * Nomor final dibuat saat menyimpan.
     */
    public function nomorSementara(Request $request): JsonResponse
    {
        $tanggal = $request->validate(['tanggal' => ['required', 'date']])['tanggal'];

        return response()->json(['nomor' => Kuitansi::nomorArsipBerikut(Carbon::parse($tanggal))]);
    }

    /**
     * Periksa arsip yang mirip sebelum menyimpan, agar owner bisa memutuskan apakah benar duplikat.
     */
    public function cekDuplikat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_pihak' => ['required', 'string', 'max:255'],
            'tanggal_kuitansi' => ['required', 'date'],
            'nominal_transaksi' => ['required'],
            'nomor_kuitansi_fisik' => ['nullable', 'string', 'max:50'],
        ]);

        $mirip = Kuitansi::yangMirip(
            (int) Auth::id(),
            $data['nama_pihak'],
            $data['tanggal_kuitansi'],
            $this->bersihkanAngka($data['nominal_transaksi']),
            $data['nomor_kuitansi_fisik'] ?? null
        );

        return response()->json([
            'duplikat' => $mirip->map(fn (Kuitansi $k) => [
                'nomor' => $k->nomor_kuitansi,
                'nama' => $k->nama_pihak,
                'tanggal' => $k->tanggal_kuitansi->translatedFormat('d M Y'),
                'nominal' => 'Rp'.number_format((float) $k->nominal_transaksi, 0, ',', '.'),
                'url' => route('arsip-kuitansi.show', $k),
            ])->values(),
        ]);
    }

    /**
     * Simpan arsip kuitansi baru beserta pembayaran awal (jika ada).
     */
    public function store(Request $request): RedirectResponse
    {
        // Tepat satu foto: tolak beberapa file atau lampiran tambahan dalam satu arsip.
        if (is_array($request->file('bukti')) || count($request->allFiles()) > 1) {
            throw ValidationException::withMessages([
                'bukti' => 'Satu arsip hanya boleh memiliki satu foto. Simpan foto lain sebagai arsip terpisah bila memang transaksi berbeda.',
            ]);
        }

        // Bersihkan format rupiah (mis. "1.000.000" -> 1000000) dan rapikan teks.
        $nominalTransaksi = $this->bersihkanAngka($request->input('nominal_transaksi'));
        $nominalDibayar = $this->bersihkanAngka($request->input('nominal_dibayar', 0));
        $sisa = round($nominalTransaksi - $nominalDibayar, 2);

        $request->merge([
            'nominal_transaksi' => $nominalTransaksi,
            'nominal_dibayar' => $nominalDibayar,
            'nama_pihak' => trim((string) $request->input('nama_pihak')),
            'nomor_kuitansi_fisik' => ($fisik = trim((string) $request->input('nomor_kuitansi_fisik'))) !== '' ? $fisik : null,
        ]);

        $adaPembayaran = $nominalDibayar > 0;

        $validated = $request->validate([
            'tanggal_kuitansi' => ['required', 'date'],
            'nama_pihak' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:utang,piutang'],
            'nominal_transaksi' => ['required', 'numeric', 'min:1'],
            'nomor_kuitansi_fisik' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'nominal_dibayar' => ['nullable', 'numeric', 'min:0', 'lte:nominal_transaksi'],
            // Pembayaran awal memakai aturan yang sama dengan pembayaran selanjutnya.
            'tanggal_pembayaran' => [Rule::requiredIf($adaPembayaran), 'nullable', 'date', 'before_or_equal:today'],
            'metode_pembayaran' => [Rule::requiredIf($adaPembayaran), 'nullable', 'in:Tunai,Transfer bank'],
            // Jatuh tempo wajib hanya jika masih ada sisa.
            'tanggal_jatuh_tempo' => [Rule::requiredIf($sisa > 0), 'nullable', 'date'],
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'konfirmasi_duplikat' => ['nullable', 'boolean'],
        ], [
            'tanggal_kuitansi.required' => 'Tanggal kuitansi wajib diisi.',
            'nama_pihak.required' => 'Nama pelanggan atau supplier wajib diisi.',
            'jenis.required' => 'Pilih jenis kuitansi: utang atau piutang.',
            'nominal_transaksi.required' => 'Nominal transaksi wajib diisi.',
            'nominal_transaksi.min' => 'Nominal transaksi minimal Rp1.',
            'nominal_dibayar.lte' => 'Nominal dibayar tidak boleh melebihi nominal transaksi.',
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi karena sudah ada pembayaran.',
            'tanggal_pembayaran.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran karena sudah ada pembayaran.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
            'tanggal_jatuh_tempo.required' => 'Tanggal jatuh tempo wajib diisi karena transaksi masih memiliki sisa.',
            'bukti.required' => 'Foto kuitansi wajib diunggah (tepat satu foto).',
            'bukti.file' => 'Unggah tepat satu foto kuitansi.',
            'bukti.mimes' => 'Foto harus berformat JPG atau PNG. PDF tidak diterima.',
            'bukti.max' => 'Ukuran foto maksimal 10 MB.',
            'bukti.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya tidak lebih dari 10 MB.',
        ]);

        // Peringatan arsip mirip. Form biasanya sudah memeriksanya lebih dulu (cek-duplikat);
        // pemeriksaan ini menjaga agar tetap berlaku jika form dikirim tanpa pengecekan itu.
        if (! $request->boolean('konfirmasi_duplikat')) {
            $mirip = Kuitansi::yangMirip(
                (int) Auth::id(),
                $validated['nama_pihak'],
                $validated['tanggal_kuitansi'],
                $nominalTransaksi,
                $validated['nomor_kuitansi_fisik'] ?? null
            )->first();

            if ($mirip) {
                throw ValidationException::withMessages([
                    'duplikat' => "Kemungkinan arsip duplikat: {$mirip->nomor_kuitansi} ({$mirip->nama_pihak}, "
                        .$mirip->tanggal_kuitansi->translatedFormat('d M Y').', Rp'.number_format((float) $mirip->nominal_transaksi, 0, ',', '.')
                        .'). Periksa dulu. Jika memang transaksi berbeda, pilih foto lagi lalu klik Simpan kuitansi sekali lagi.',
                ]);
            }
        }

        // Semua validasi lolos. Simpan foto di disk privat (tanpa URL publik), lalu simpan arsip.
        // Jika penyimpanan gagal, foto dihapus agar tidak ada berkas yatim.
        $buktiPath = $request->file('bukti')->store('kuitansi', Kuitansi::DISK_BUKTI);

        try {
            $kuitansi = $this->simpanArsip($validated, $buktiPath, $nominalTransaksi, $nominalDibayar, $sisa);
        } catch (Throwable $e) {
            Storage::disk(Kuitansi::DISK_BUKTI)->delete($buktiPath);

            throw $e;
        }

        return redirect()->route('arsip-kuitansi.show', $kuitansi->id)
            ->with('status', "Arsip {$kuitansi->nomor_kuitansi} ({$kuitansi->nama_pihak}) berhasil disimpan!");
    }

    /**
     * Buat nomor arsip unik, lalu simpan arsip dan pembayaran awal dalam satu transaksi.
     * Bila dua arsip disimpan bersamaan dan nomornya bertabrakan, nomor dihitung ulang.
     *
     * @param  array<string, mixed>  $data
     */
    private function simpanArsip(array $data, string $buktiPath, float $nominalTransaksi, float $nominalDibayar, float $sisa): Kuitansi
    {
        $tanggalKuitansi = Carbon::parse($data['tanggal_kuitansi']);
        $ada = $nominalDibayar > 0;

        for ($coba = 1; ; $coba++) {
            try {
                return DB::transaction(function () use ($data, $buktiPath, $nominalTransaksi, $nominalDibayar, $sisa, $tanggalKuitansi, $ada) {
                    $kuitansi = Kuitansi::create([
                        'user_id' => Auth::id(),
                        'nomor_kuitansi' => Kuitansi::nomorArsipBerikut($tanggalKuitansi),
                        'nomor_kuitansi_fisik' => $data['nomor_kuitansi_fisik'] ?? null,
                        'nama_pihak' => $data['nama_pihak'],
                        'role_pihak' => $data['jenis'] === 'piutang' ? 'Pelanggan' : 'Supplier',
                        'jenis' => $data['jenis'],
                        'nominal_transaksi' => $nominalTransaksi,
                        'status_pembayaran' => Kuitansi::tentukanStatus($nominalTransaksi, $nominalDibayar, $ada ? 1 : 0),
                        'tanggal_kuitansi' => $data['tanggal_kuitansi'],
                        // Transaksi yang langsung lunas tidak memerlukan jatuh tempo.
                        'tanggal_jatuh_tempo' => $sisa > 0 ? $data['tanggal_jatuh_tempo'] : null,
                        'keterangan' => $data['catatan'] ?? null,
                        'bukti_kuitansi' => $buktiPath,
                    ]);

                    if ($ada) {
                        $kuitansi->pembayaran()->create([
                            'nominal' => $nominalDibayar,
                            'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'catatan' => 'Pembayaran awal',
                            'dicatat_oleh' => Auth::user()->name ?? 'Owner',
                        ]);
                    }

                    return $kuitansi;
                });
            } catch (UniqueConstraintViolationException $e) {
                if ($coba >= 5) {
                    throw $e;
                }
            }
        }
    }

    private function bersihkanAngka(mixed $nilai): float
    {
        return (float) preg_replace('/[^\d]/', '', (string) $nilai);
    }
}
