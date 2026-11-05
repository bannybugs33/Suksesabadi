<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JatuhTempoController extends Controller
{
    /**
     * Kategori pengingat yang ditampilkan sebagai tab.
     * Kunci tab => kode tempo yang termasuk di dalamnya (kode dihitung oleh Kuitansi::infoTempo).
     *
     * @var array<string, array<int, string>>
     */
    private const KATEGORI = [
        'h-7' => ['h-1', 'h-2', 'h-3', 'h-4', 'h-5', 'h-6', 'h-7'],
        'hari-ini' => ['hari_ini'],
        'terlambat' => ['terlambat'],
    ];

    /**
     * Halaman Jatuh Tempo: daftar tagihan aktif yang masih bersisa dan sudah masuk pengingat
     * (mendekati jatuh tempo mulai H-7, hari ini, atau terlambat).
     *
     * Aturan "aktif" dan "pengingat" sama dengan Dashboard dan Arsip (lihat Kuitansi::HARI_PENGINGAT
     * dan Kuitansi::infoTempo), sehingga jumlahnya tidak pernah berbeda antarhalaman.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id() ?? 1;
        $tanggalAcuan = $request->filled('tanggal_acuan')
            ? Carbon::parse($request->query('tanggal_acuan'))->startOfDay()
            : Carbon::now()->startOfDay();

        $pengingat = $this->ambilPengingat($userId, $tanggalAcuan);

        $jumlah = [
            'semua' => $pengingat->count(),
            'h-7' => $pengingat->whereIn('tempo_code', self::KATEGORI['h-7'])->count(),
            'hari-ini' => $pengingat->whereIn('tempo_code', self::KATEGORI['hari-ini'])->count(),
            'terlambat' => $pengingat->whereIn('tempo_code', self::KATEGORI['terlambat'])->count(),
        ];

        $tab = $request->query('tab', 'semua');
        if (! array_key_exists($tab, self::KATEGORI)) {
            $tab = 'semua';
        }

        $daftar = $tab === 'semua'
            ? $pengingat
            : $pengingat->whereIn('tempo_code', self::KATEGORI[$tab])->values();

        return view('jatuh-tempo.index', [
            'tanggalAcuan' => $tanggalAcuan,
            'tab' => $tab,
            'daftar' => $daftar,
            'jumlah' => $jumlah,
            'totalSisa' => (float) $pengingat->sum('sisa'),
            'sisaPiutang' => (float) $pengingat->where('jenis', 'piutang')->sum('sisa'),
            'sisaUtang' => (float) $pengingat->where('jenis', 'utang')->sum('sisa'),
            'dipilihId' => (int) $request->query('selected', 0),
            'hariIni' => Carbon::now()->toDateString(),
        ]);
    }

    /**
     * Tagihan aktif (sisa > 0, belum dibatalkan) yang masuk pengingat, urut dari yang paling mendesak.
     * Hanya pembayaran berstatus aktif yang dihitung.
     *
     * @return Collection<int, object>
     */
    private function ambilPengingat(int $userId, Carbon $tanggalAcuan): Collection
    {
        $pembayaran = DB::table('pembayaran')
            ->where('status', 'aktif')
            ->select('kuitansi_id')
            ->selectRaw('SUM(nominal) AS total_terbayar')
            ->groupBy('kuitansi_id');

        return DB::table('kuitansi as k')
            ->leftJoinSub($pembayaran, 'p', function ($join) {
                $join->on('k.id', '=', 'p.kuitansi_id');
            })
            ->where('k.user_id', $userId)
            ->whereNull('k.dibatalkan_pada')
            // Aktif = masih bersisa (dihitung dari pembayaran), sama seperti Dashboard dan Arsip.
            ->whereRaw('ROUND(k.nominal_transaksi - COALESCE(p.total_terbayar, 0), 2) > 0')
            ->select('k.*')
            ->selectRaw('COALESCE(p.total_terbayar, 0) AS terbayar')
            ->selectRaw('ROUND(k.nominal_transaksi - COALESCE(p.total_terbayar, 0), 2) AS sisa')
            ->orderBy('k.tanggal_jatuh_tempo')
            ->orderBy('k.id')
            ->get()
            ->map(function ($item) use ($tanggalAcuan) {
                $item->sisa = (float) $item->sisa;
                $item->terbayar = (float) $item->terbayar;

                $tempo = Kuitansi::infoTempo($item->tanggal_jatuh_tempo, 'belum_lunas', $tanggalAcuan);
                $item->tempo_code = $tempo['code'];
                [$item->badge, $item->badge_kelas] = Kuitansi::badgeTempo($item->tanggal_jatuh_tempo, $tanggalAcuan);

                return $item;
            })
            ->filter(fn ($item) => in_array($item->tempo_code, Kuitansi::KODE_PENGINGAT, true))
            ->values();
    }
}
