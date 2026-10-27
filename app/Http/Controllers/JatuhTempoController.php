<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JatuhTempoController extends Controller
{
    /**
     * Tampilkan halaman Utang & Piutang — Jatuh Tempo.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id() ?? 1;

        $tanggalAcuan = $request->filled('tanggal_acuan')
            ? Carbon::parse($request->query('tanggal_acuan'))->startOfDay()
            : Carbon::now()->startOfDay();

        $pembayaranSub = DB::table('pembayaran')
            ->select('kuitansi_id')
            ->selectRaw('SUM(nominal) AS total_terbayar')
            ->groupBy('kuitansi_id');

        $baseQuery = DB::table('kuitansi as k')
            ->leftJoinSub($pembayaranSub, 'p', function ($join) {
                $join->on('k.id', '=', 'p.kuitansi_id');
            })
            ->where('k.user_id', $userId)
            ->select('k.*')
            ->selectRaw('COALESCE(p.total_terbayar, 0) AS terbayar')
            ->selectRaw('k.nominal_transaksi - COALESCE(p.total_terbayar, 0) AS sisa')
            ->where('k.status_pembayaran', '!=', 'lunas');

        // Ambil semua data kuitansi yang perlu perhatian untuk kalkulasi metrik
        $allPerluPerhatian = (clone $baseQuery)
            ->orderBy('k.tanggal_jatuh_tempo', 'asc')
            ->get()
            ->map(function ($item) use ($tanggalAcuan) {
                return $this->formatKuitansiItem($item, $tanggalAcuan);
            });

        // Hitung Tab Counts
        $countSemua = $allPerluPerhatian->count();
        $countH3 = $allPerluPerhatian->where('tempo_code', 'h-3')->count();
        $countH2 = $allPerluPerhatian->where('tempo_code', 'h-2')->count();
        $countH1 = $allPerluPerhatian->where('tempo_code', 'h-1')->count();
        $countHariIni = $allPerluPerhatian->where('tempo_code', 'hari_ini')->count();
        $countTerlambat = $allPerluPerhatian->where('tempo_code', 'terlambat')->count();

        // Hitung Metrik Ringkasan
        $sisaPerluDiselesaikan = $allPerluPerhatian->sum('sisa');

        $piutangItems = $allPerluPerhatian->where('jenis', 'piutang');
        $sisaPiutang = $piutangItems->sum('sisa');
        $jumlahPelangganPiutang = $piutangItems->pluck('nama_pihak')->unique()->count();

        $utangItems = $allPerluPerhatian->where('jenis', 'utang');
        $sisaUtang = $utangItems->sum('sisa');
        $jumlahSupplierUtang = $utangItems->pluck('nama_pihak')->unique()->count();

        // Terapkan Filter
        $filteredItems = $allPerluPerhatian;

        if ($request->filled('q')) {
            $q = strtolower(trim((string) $request->query('q')));
            $filteredItems = $filteredItems->filter(function ($item) use ($q) {
                return str_contains(strtolower($item->nama_pihak), $q)
                    || str_contains(strtolower($item->nomor_kuitansi), $q);
            });
        }

        if ($request->filled('jenis') && in_array($request->query('jenis'), ['utang', 'piutang'])) {
            $filteredItems = $filteredItems->where('jenis', $request->query('jenis'));
        }

        if ($request->filled('status') && in_array($request->query('status'), ['belum_lunas', 'dp', 'cicilan'])) {
            $filteredItems = $filteredItems->where('status_pembayaran', $request->query('status'));
        }

        $tab = $request->query('tab', 'semua');
        if ($tab !== 'semua') {
            $filteredItems = $filteredItems->where('tempo_code', $tab);
        }

        // Tentukan Kuitansi yang Sedang Dipilih untuk Rincian Detail
        $selectedId = $request->query('selected');
        $selectedKuitansi = null;

        if ($selectedId) {
            $selectedKuitansi = $allPerluPerhatian->firstWhere('id', (int) $selectedId);
        }

        $riwayatPembayaran = collect();
        if ($selectedKuitansi) {
            $riwayatPembayaran = DB::table('pembayaran')
                ->where('kuitansi_id', $selectedKuitansi->id)
                ->orderByDesc('tanggal_pembayaran')
                ->get();
        }

        $clientItems = $filteredItems;
        if ($selectedKuitansi && ! $clientItems->contains('id', $selectedKuitansi->id)) {
            $clientItems = $clientItems->push($selectedKuitansi);
        }

        $allItemIds = $clientItems->pluck('id');
        $allPembayaran = DB::table('pembayaran')
            ->whereIn('kuitansi_id', $allItemIds)
            ->orderByDesc('tanggal_pembayaran')
            ->get()
            ->groupBy('kuitansi_id');

        $kuitansiClientData = $clientItems->mapWithKeys(function ($item) use ($allPembayaran) {
            $payments = $allPembayaran->get($item->id, collect())->map(function ($p) {
                return [
                    'id' => $p->id,
                    'metode' => $p->metode_pembayaran ?? 'Pembayaran',
                    'nominal' => $p->nominal,
                    'nominal_formatted' => 'Rp'.number_format($p->nominal, 0, ',', '.'),
                    'tanggal_formatted' => Carbon::parse($p->tanggal_pembayaran)->translatedFormat('d M Y, H.i'),
                    'dicatat_oleh' => $p->dicatat_oleh ?? 'Andi Saputra',
                    'is_transfer' => str_contains(strtolower($p->metode_pembayaran ?? ''), 'transfer'),
                ];
            })->values();

            return [$item->id => [
                'id' => $item->id,
                'nomor_kuitansi' => $item->nomor_kuitansi,
                'nama_pihak' => $item->nama_pihak,
                'jenis' => $item->jenis,
                'role_pihak_display' => $item->role_pihak_display,
                'status_pembayaran' => $item->status_pembayaran,
                'status_pembayaran_label' => $item->status_pembayaran === 'dp' ? 'DP' : ucfirst(str_replace('_', ' ', $item->status_pembayaran)),
                'tanggal_kuitansi_formatted' => Carbon::parse($item->tanggal_kuitansi)->translatedFormat('d F Y'),
                'tanggal_jatuh_tempo_formatted' => Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d F Y'),
                'updated_at_formatted' => Carbon::parse($item->updated_at ?? $item->tanggal_kuitansi)->translatedFormat('d M Y, H.i'),
                'tempo_badge' => $item->tempo_badge,
                'tempo_code' => $item->tempo_code,
                'nominal_transaksi_formatted' => 'Rp'.number_format($item->nominal_transaksi, 0, ',', '.'),
                'terbayar_formatted' => 'Rp'.number_format($item->terbayar, 0, ',', '.'),
                'sisa_formatted' => 'Rp'.number_format($item->sisa, 0, ',', '.'),
                'sisa' => (float) $item->sisa,
                'pembayaran_url' => route('pembayaran.store', $item->id),
                'keterangan' => $item->keterangan ?? 'Tidak ada catatan tambahan untuk kuitansi ini.',
                'has_bukti' => $item->has_bukti,
                'bukti_kuitansi' => $item->bukti_kuitansi,
                'bukti_kuitansi_name' => $item->bukti_kuitansi_name,
                'bukti_kuitansi_meta' => $item->bukti_kuitansi_meta,
                'bukti_kuitansi_url' => $item->bukti_kuitansi_url,
                'fallback_bukti_url' => $item->fallback_bukti_url,
                'riwayat_pembayaran' => $payments,
                'total_terbayar_formatted' => 'Rp'.number_format($item->terbayar, 0, ',', '.'),
            ]];
        });

        return view('jatuh-tempo.index', compact(
            'tanggalAcuan',
            'allPerluPerhatian',
            'filteredItems',
            'sisaPerluDiselesaikan',
            'sisaPiutang',
            'jumlahPelangganPiutang',
            'sisaUtang',
            'jumlahSupplierUtang',
            'countSemua',
            'countH3',
            'countH2',
            'countH1',
            'countHariIni',
            'countTerlambat',
            'tab',
            'selectedKuitansi',
            'riwayatPembayaran',
            'kuitansiClientData'
        ));
    }

    /**
     * Format kuitansi item dengan atribut kalkulasi jatuh tempo.
     */
    private function formatKuitansiItem(object $item, Carbon $tanggalAcuan): object
    {
        $jatuhTempo = $item->tanggal_jatuh_tempo
            ? Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay()
            : null;

        $tempoLabel = '-';
        $tempoBadge = '';
        $tempoCode = 'aman';

        if ($jatuhTempo) {
            if ($jatuhTempo->lt($tanggalAcuan)) {
                $selisih = $jatuhTempo->diffInDays($tanggalAcuan);
                $tempoLabel = "Terlambat {$selisih} hari";
                $tempoBadge = "Terlambat {$selisih} hari";
                $tempoCode = 'terlambat';
            } elseif ($jatuhTempo->isSameDay($tanggalAcuan)) {
                $tempoLabel = 'Jatuh tempo hari ini';
                $tempoBadge = 'Hari ini';
                $tempoCode = 'hari_ini';
            } elseif ($jatuhTempo->isSameDay($tanggalAcuan->copy()->addDay())) {
                $tempoLabel = 'H-1 • Besok';
                $tempoBadge = 'H-1 • Besok';
                $tempoCode = 'h-1';
            } elseif ($jatuhTempo->isSameDay($tanggalAcuan->copy()->addDays(2))) {
                $tempoLabel = 'H-2';
                $tempoBadge = 'H-2';
                $tempoCode = 'h-2';
            } elseif ($jatuhTempo->isSameDay($tanggalAcuan->copy()->addDays(3))) {
                $tempoLabel = 'H-3';
                $tempoBadge = 'H-3';
                $tempoCode = 'h-3';
            } else {
                $selisih = $tanggalAcuan->diffInDays($jatuhTempo);
                $tempoLabel = "{$selisih} hari lagi";
                $tempoBadge = "{$selisih} hari lagi";
                $tempoCode = 'lainnya';
            }
        }

        $item->tempo_label = $tempoLabel;
        $item->tempo_badge = $tempoBadge;
        $item->tempo_code = $tempoCode;
        $item->role_pihak_display = $item->role_pihak ?? ($item->jenis === 'piutang' ? 'Pelanggan berutang kepada toko' : 'Supplier harus dibayar toko');

        $buktiInfo = Kuitansi::resolveBuktiInfo($item->bukti_kuitansi ?? null);
        $item->has_bukti = $buktiInfo['has_bukti'];
        $item->bukti_kuitansi_name = $buktiInfo['bukti_kuitansi_name'];
        $item->bukti_kuitansi_meta = $buktiInfo['bukti_kuitansi_meta'];
        $item->bukti_kuitansi_url = $buktiInfo['bukti_kuitansi_url'];
        $item->fallback_bukti_url = $buktiInfo['fallback_bukti_url'];

        return $item;
    }
}
