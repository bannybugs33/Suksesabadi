<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\RekapitulasiPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekapitulasiController extends Controller
{
    private const NAMA_TOKO = 'Toko Sukses Abadi';

    public function index(Request $request): View
    {
        return view('rekapitulasi.index', $this->ambilData($request));
    }

    /**
     * Unduh rekapitulasi sebagai PDF, dengan filter periode dan arah yang sama seperti halaman.
     */
    public function pdf(Request $request): Response
    {
        $data = $this->ambilData($request);

        $labelPeriode = $data['periode']
            ? Carbon::createFromFormat('Y-m-d', $data['periode'].'-01')->translatedFormat('F Y')
            : 'Semua periode';

        $labelArah = match ($data['arah']) {
            'penerimaan' => 'Penerimaan',
            'pengeluaran' => 'Pengeluaran',
            default => 'Semua arah',
        };

        $dokumen = new RekapitulasiPdf(
            self::NAMA_TOKO,
            "Periode: {$labelPeriode} | Arah: {$labelArah}",
            'Dicetak '.$data['dataPer']->translatedFormat('d M Y, H.i').' oleh '.(Auth::user()->name ?? 'Owner'),
        );

        $baris = $data['pembayaran']->map(fn (object $b): array => [
            'tanggal' => $b->tanggal->translatedFormat('d M Y'),
            'nama' => (string) $b->nama_pihak,
            'nomor' => (string) $b->nomor_kuitansi,
            'arah' => $b->arah === 'penerimaan' ? 'Penerimaan' : 'Pengeluaran',
            'metode' => (string) $b->metode,
            'nominal' => (float) $b->nominal,
        ]);

        $isi = $dokumen->render([
            'penerimaan' => $data['totalPenerimaan'],
            'pengeluaran' => $data['totalPengeluaran'],
            'bersih' => $data['posisiBersih'],
            'jumlah' => $data['jumlahPembayaran'],
        ], $baris);

        $namaBerkas = 'rekapitulasi_'.($data['periode'] ?? 'semua-periode').'_'.($data['arah'] ?? 'semua-arah').'.pdf';

        return response($isi, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$namaBerkas.'"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Data rekapitulasi yang dipakai halaman dan PDF, supaya angkanya selalu sama.
     *
     * @return array<string, mixed>
     */
    private function ambilData(Request $request): array
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

        return [
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
        ];
    }
}
