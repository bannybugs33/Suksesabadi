<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ArsipKuitansiController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'in:utang,piutang'],
            'status' => ['nullable', 'in:belum_lunas,dp,cicilan,lunas,dibatalkan'],
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

        if (($filter['status'] ?? null) === 'dibatalkan') {
            $query->whereNotNull('dibatalkan_pada');
        } elseif (! empty($filter['status'])) {
            $query->whereNull('dibatalkan_pada')->where('status_pembayaran', $filter['status']);
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

            // Penanda tempo hanya untuk tagihan yang masih bersisa dan belum dibatalkan.
            [$item->tempo_teks, $item->tempo_kelas] = ($sisa > 0 && $item->tanggal_jatuh_tempo && $item->dibatalkan_pada === null)
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

        $kuitansi->load(['semuaPembayaran', 'koreksi']);

        // Hanya pembayaran aktif yang dihitung; pembayaran dibatalkan tetap tampil di riwayat.
        $terbayar = (float) $kuitansi->semuaPembayaran->where('status', 'aktif')->sum('nominal');
        $sisa = max(round((float) $kuitansi->nominal_transaksi - $terbayar, 2), 0);

        return view('arsip-kuitansi.show', compact('kuitansi', 'terbayar', 'sisa'));
    }

    /**
     * Ubah data kuitansi (termasuk mengganti foto). Nomor arsip tidak pernah berubah.
     */
    public function update(Request $request, Kuitansi $kuitansi): RedirectResponse
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        if (is_array($request->file('bukti')) || count($request->allFiles()) > 1) {
            throw ValidationException::withMessages([
                'bukti' => 'Satu arsip hanya boleh memiliki satu foto.',
            ]);
        }

        $request->merge([
            'nama_pihak' => trim((string) $request->input('nama_pihak')),
            'nomor_kuitansi_fisik' => ($fisik = trim((string) $request->input('nomor_kuitansi_fisik'))) !== '' ? $fisik : null,
        ]);

        $data = $request->validate([
            'nama_pihak' => ['required', 'string', 'max:255'],
            'tanggal_kuitansi' => ['required', 'date'],
            'nominal_transaksi' => ['required', 'numeric', 'min:1'],
            'tanggal_jatuh_tempo' => ['nullable', 'date', 'after_or_equal:tanggal_kuitansi'],
            'nomor_kuitansi_fisik' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
        ], [
            'nama_pihak.required' => 'Nama pelanggan atau supplier wajib diisi.',
            'tanggal_kuitansi.required' => 'Tanggal kuitansi wajib diisi.',
            'nominal_transaksi.required' => 'Nominal transaksi wajib diisi.',
            'nominal_transaksi.min' => 'Nominal transaksi minimal Rp1.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Jatuh tempo tidak boleh sebelum tanggal kuitansi.',
            'bukti.file' => 'Unggah tepat satu foto kuitansi.',
            'bukti.mimes' => 'Foto harus berformat JPG atau PNG. PDF tidak diterima.',
            'bukti.max' => 'Ukuran foto maksimal 10 MB.',
            'bukti.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya tidak lebih dari 10 MB.',
        ]);

        $lokasiLama = Kuitansi::lokasiBukti($kuitansi->bukti_kuitansi);
        $buktiBaru = $request->hasFile('bukti')
            ? $request->file('bukti')->store('kuitansi', Kuitansi::DISK_BUKTI)
            : null;

        try {
            $berubah = DB::transaction(function () use ($kuitansi, $data, $buktiBaru): bool {
                $kuitansi = Kuitansi::query()->lockForUpdate()->findOrFail($kuitansi->id);
                $this->pastikanBisaDiubah($kuitansi);

                $nominalBaru = round((float) $data['nominal_transaksi'], 2);
                $terbayar = (float) $kuitansi->pembayaran()->sum('nominal');

                if ($nominalBaru < $terbayar) {
                    throw ValidationException::withMessages([
                        'nominal_transaksi' => 'Nominal tidak boleh lebih kecil dari total pembayaran aktif ('.Kuitansi::rupiah($terbayar).').',
                    ]);
                }

                $catatanBaru = $data['catatan'] ?? null;
                $tempoBaru = $data['tanggal_jatuh_tempo'] ?? null;
                $fisikBaru = $data['nomor_kuitansi_fisik'] ?? null;

                $perubahan = [];
                $catat = function (string $label, string $lama, string $baru) use (&$perubahan): void {
                    if ($lama !== $baru) {
                        $perubahan[] = "{$label}: {$lama} → {$baru}";
                    }
                };

                $catat('Nama', (string) $kuitansi->nama_pihak, $data['nama_pihak']);
                $catat('Tanggal kuitansi', $kuitansi->tanggal_kuitansi->translatedFormat('d M Y'), Carbon::parse($data['tanggal_kuitansi'])->translatedFormat('d M Y'));
                $catat('Nominal', Kuitansi::rupiah($kuitansi->nominal_transaksi), Kuitansi::rupiah($nominalBaru));
                $catat('Jatuh tempo', $kuitansi->tanggal_jatuh_tempo?->translatedFormat('d M Y') ?? '-', $tempoBaru ? Carbon::parse($tempoBaru)->translatedFormat('d M Y') : '-');
                $catat('Nomor kuitansi fisik', $kuitansi->nomor_kuitansi_fisik ?: '-', $fisikBaru ?: '-');

                if ((string) $kuitansi->keterangan !== (string) $catatanBaru) {
                    $perubahan[] = 'Catatan diubah';
                }

                if ($buktiBaru !== null) {
                    $perubahan[] = 'Foto kuitansi diganti';
                }

                if ($perubahan === []) {
                    return false;
                }

                $kuitansi->update([
                    'nama_pihak' => $data['nama_pihak'],
                    'tanggal_kuitansi' => $data['tanggal_kuitansi'],
                    'nominal_transaksi' => $nominalBaru,
                    'tanggal_jatuh_tempo' => $tempoBaru,
                    'nomor_kuitansi_fisik' => $fisikBaru,
                    'keterangan' => $catatanBaru,
                    'bukti_kuitansi' => $buktiBaru ?? $kuitansi->bukti_kuitansi,
                    'status_pembayaran' => Kuitansi::tentukanStatus(
                        $nominalBaru,
                        $terbayar,
                        $kuitansi->pembayaran()->count()
                    ),
                ]);

                $this->catatKoreksi($kuitansi, 'Data kuitansi diubah', implode('; ', $perubahan));

                return true;
            });
        } catch (Throwable $e) {
            if ($buktiBaru !== null) {
                Storage::disk(Kuitansi::DISK_BUKTI)->delete($buktiBaru);
            }

            throw $e;
        }

        if (! $berubah) {
            return redirect()->route('arsip-kuitansi.show', $kuitansi)->with('status', 'Tidak ada perubahan yang disimpan.');
        }

        // Foto lama dihapus setelah data baru tersimpan, supaya tidak ada berkas yatim.
        if ($buktiBaru !== null && $lokasiLama !== null) {
            Storage::disk($lokasiLama['disk'])->delete($lokasiLama['path']);
        }

        return redirect()->route('arsip-kuitansi.show', $kuitansi)->with('status', 'Perubahan kuitansi berhasil disimpan.');
    }

    /**
     * Koreksi satu pembayaran aktif. Alasan wajib dan tercatat di riwayat koreksi.
     */
    public function updatePembayaran(Request $request, Kuitansi $kuitansi, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);
        abort_unless($pembayaran->kuitansi_id === $kuitansi->id, 404);

        $data = $request->validate([
            'tanggal_pembayaran' => ['required', 'date', 'before_or_equal:today'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'alasan_koreksi' => ['required', 'string', 'max:255'],
        ], [
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
            'tanggal_pembayaran.before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
            'nominal.required' => 'Jumlah pembayaran wajib diisi.',
            'nominal.min' => 'Jumlah minimal Rp1.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
            'alasan_koreksi.required' => 'Alasan koreksi wajib diisi.',
        ]);

        DB::transaction(function () use ($kuitansi, $pembayaran, $data): void {
            $kuitansi = Kuitansi::query()->lockForUpdate()->findOrFail($kuitansi->id);
            $pembayaran = Pembayaran::query()->findOrFail($pembayaran->id);

            $this->pastikanBisaDiubah($kuitansi);

            if ($pembayaran->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'transaksi' => 'Pembayaran yang sudah dibatalkan tidak bisa diedit.',
                ]);
            }

            $nominalBaru = round((float) $data['nominal'], 2);
            $terbayarLain = (float) $kuitansi->pembayaran()->where('id', '!=', $pembayaran->id)->sum('nominal');
            $nominalTransaksi = (float) $kuitansi->nominal_transaksi;

            if ($terbayarLain + $nominalBaru > $nominalTransaksi) {
                $batas = max($nominalTransaksi - $terbayarLain, 0);

                throw ValidationException::withMessages([
                    'nominal' => 'Jumlah melebihi sisa tagihan. Maksimal '.Kuitansi::rupiah($batas).'.',
                ]);
            }

            $perubahan = [];
            $tanggalBaru = Carbon::parse($data['tanggal_pembayaran']);

            if (! $pembayaran->tanggal_pembayaran->isSameDay($tanggalBaru)) {
                $perubahan[] = 'Tanggal: '.$pembayaran->tanggal_pembayaran->translatedFormat('d M Y').' → '.$tanggalBaru->translatedFormat('d M Y');
            }

            if (round((float) $pembayaran->nominal, 2) !== $nominalBaru) {
                $perubahan[] = 'Jumlah: '.Kuitansi::rupiah($pembayaran->nominal).' → '.Kuitansi::rupiah($nominalBaru);
            }

            if (Pembayaran::labelMetode($pembayaran->metode_pembayaran) !== Pembayaran::labelMetode($data['metode_pembayaran'])) {
                $perubahan[] = 'Metode: '.Pembayaran::labelMetode($pembayaran->metode_pembayaran).' → '.Pembayaran::labelMetode($data['metode_pembayaran']);
            }

            if ($perubahan === []) {
                throw ValidationException::withMessages([
                    'alasan_koreksi' => 'Belum ada data yang diubah. Ubah tanggal, jumlah, atau metode terlebih dulu.',
                ]);
            }

            $pembayaran->update([
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'nominal' => $nominalBaru,
                'metode_pembayaran' => $data['metode_pembayaran'],
            ]);

            $this->hitungUlangStatus($kuitansi);

            $this->catatKoreksi(
                $kuitansi,
                'Pembayaran '.Kuitansi::rupiah($nominalBaru).' dikoreksi',
                implode('; ', $perubahan),
                $data['alasan_koreksi'],
                $pembayaran
            );
        });

        return redirect()->route('arsip-kuitansi.show', $kuitansi)->with('status', 'Koreksi pembayaran berhasil disimpan.');
    }

    /**
     * Batalkan satu pembayaran. Data tidak dihapus: ditandai Dibatalkan, sisa dan status dihitung ulang.
     */
    public function batalkanPembayaran(Request $request, Kuitansi $kuitansi, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);
        abort_unless($pembayaran->kuitansi_id === $kuitansi->id, 404);

        $data = $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'max:255'],
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        DB::transaction(function () use ($kuitansi, $pembayaran, $data): void {
            $kuitansi = Kuitansi::query()->lockForUpdate()->findOrFail($kuitansi->id);
            $pembayaran = Pembayaran::query()->findOrFail($pembayaran->id);

            $this->pastikanBisaDiubah($kuitansi);

            if ($pembayaran->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'transaksi' => 'Pembayaran ini sudah dibatalkan.',
                ]);
            }

            $pembayaran->update([
                'status' => 'dibatalkan',
                'alasan_pembatalan' => $data['alasan_pembatalan'],
                'dibatalkan_pada' => now(),
            ]);

            $this->hitungUlangStatus($kuitansi);

            $this->catatKoreksi(
                $kuitansi,
                'Pembayaran '.Kuitansi::rupiah($pembayaran->nominal).' dibatalkan',
                Pembayaran::labelMetode($pembayaran->metode_pembayaran).' · '.$pembayaran->tanggal_pembayaran->translatedFormat('d M Y'),
                $data['alasan_pembatalan'],
                $pembayaran
            );
        });

        return redirect()->route('arsip-kuitansi.show', $kuitansi)->with('status', 'Pembayaran dibatalkan. Sisa tagihan sudah dihitung ulang.');
    }

    /**
     * Batalkan seluruh transaksi. Arsip dan riwayat pembayaran tetap tersimpan.
     */
    public function batalkan(Request $request, Kuitansi $kuitansi): RedirectResponse
    {
        abort_unless($kuitansi->user_id === Auth::id(), 403);

        $aturan = ['alasan_pembatalan' => ['required', 'string', 'max:500']];

        if ((float) $kuitansi->pembayaran()->sum('nominal') > 0) {
            $aturan['penyelesaian_uang'] = ['required', 'in:dikembalikan,tidak_dikembalikan'];
        }

        $data = $request->validate($aturan, [
            'alasan_pembatalan.required' => 'Alasan pembatalan wajib diisi.',
            'penyelesaian_uang.required' => 'Pilih penyelesaian uang: dikembalikan atau tidak.',
            'penyelesaian_uang.in' => 'Pilihan penyelesaian uang tidak valid.',
        ]);

        DB::transaction(function () use ($kuitansi, $data): void {
            $kuitansi = Kuitansi::query()->lockForUpdate()->findOrFail($kuitansi->id);
            $this->pastikanBisaDiubah($kuitansi);

            $terbayar = (float) $kuitansi->pembayaran()->sum('nominal');
            $dikembalikan = $terbayar > 0 ? ($data['penyelesaian_uang'] ?? null) === 'dikembalikan' : null;

            $kuitansi->update([
                'dibatalkan_pada' => now(),
                'alasan_pembatalan' => $data['alasan_pembatalan'],
                'uang_dikembalikan' => $dikembalikan,
            ]);

            $detail = $terbayar > 0
                ? 'Uang tercatat '.Kuitansi::rupiah($terbayar).($dikembalikan ? ' dikembalikan penuh' : ' tidak dikembalikan')
                : 'Belum ada uang tercatat';

            $this->catatKoreksi($kuitansi, 'Transaksi dibatalkan', $detail, $data['alasan_pembatalan']);
        });

        return redirect()->route('arsip-kuitansi.show', $kuitansi)->with('status', 'Transaksi dibatalkan. Arsip dan riwayatnya tetap tersimpan.');
    }

    private function pastikanBisaDiubah(Kuitansi $kuitansi): void
    {
        if ($kuitansi->sudahDibatalkan()) {
            throw ValidationException::withMessages([
                'transaksi' => 'Transaksi ini sudah dibatalkan dan tidak bisa diubah lagi.',
            ]);
        }
    }

    private function hitungUlangStatus(Kuitansi $kuitansi): void
    {
        $kuitansi->update([
            'status_pembayaran' => Kuitansi::tentukanStatus(
                (float) $kuitansi->nominal_transaksi,
                (float) $kuitansi->pembayaran()->sum('nominal'),
                $kuitansi->pembayaran()->count()
            ),
        ]);
    }

    private function catatKoreksi(Kuitansi $kuitansi, string $ringkasan, ?string $detail = null, ?string $alasan = null, ?Pembayaran $pembayaran = null): void
    {
        $kuitansi->koreksi()->create([
            'pembayaran_id' => $pembayaran?->id,
            'ringkasan' => $ringkasan,
            'detail' => $detail,
            'alasan' => $alasan,
            'dicatat_oleh' => Auth::user()->name ?? 'Owner',
        ]);
    }

    /**
     * Tampilkan foto/berkas bukti kuitansi (disk privat, wajib login, hanya pemilik).
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
