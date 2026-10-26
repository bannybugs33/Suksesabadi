<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Kuitansi extends Model
{
    /**
     * Kode tempo yang dianggap "perlu perhatian" (pengingat aktif mulai H-3).
     *
     * @var array<int, string>
     */
    public const KODE_PENGINGAT = ['terlambat', 'hari_ini', 'h-1', 'h-2', 'h-3'];

    /**
     * @var string
     */
    protected $table = 'kuitansi';

    /**
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_transaksi' => 'decimal:2',
            'tanggal_kuitansi' => 'date',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'kuitansi_id');
    }

    /**
     * Hitung label dan kode tempo sebuah kuitansi terhadap tanggal acuan.
     * Kuitansi lunas atau tanpa jatuh tempo selalu dianggap aman.
     *
     * @return array{label: string, code: string}
     */
    public static function infoTempo(mixed $tanggalJatuhTempo, ?string $status, Carbon $tanggalAcuan): array
    {
        if (! $tanggalJatuhTempo || $status === 'lunas') {
            return ['label' => '-', 'code' => 'aman'];
        }

        $acuan = $tanggalAcuan->copy()->startOfDay();
        $tempo = Carbon::parse($tanggalJatuhTempo)->startOfDay();
        $selisih = (int) abs($acuan->diffInDays($tempo));

        if ($tempo->lt($acuan)) {
            return ['label' => "Terlambat {$selisih} hari", 'code' => 'terlambat'];
        }

        if ($selisih === 0) {
            return ['label' => 'Jatuh tempo hari ini', 'code' => 'hari_ini'];
        }

        if ($selisih === 1) {
            return ['label' => 'Jatuh tempo besok', 'code' => 'h-1'];
        }

        if ($selisih <= 3) {
            return ['label' => "H-{$selisih}", 'code' => "h-{$selisih}"];
        }

        return ['label' => 'Mendatang', 'code' => 'mendatang'];
    }

    /**
     * Ringkasan pengingat jatuh tempo: kuitansi belum lunas yang sudah lewat
     * atau jatuh tempo dalam 3 hari ke depan.
     * Jika $userId null, dihitung untuk seluruh toko (hanya angka agregat).
     *
     * @return array{jumlah: int, terlambat: int, sisa: float}
     */
    public static function ringkasanPengingat(?int $userId = null, ?Carbon $tanggalAcuan = null): array
    {
        $acuan = ($tanggalAcuan ?? Carbon::today())->copy()->startOfDay();
        $batas = $acuan->copy()->addDays(3)->toDateString();

        $items = static::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('status_pembayaran', '!=', 'lunas')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->whereDate('tanggal_jatuh_tempo', '<=', $batas)
            ->withSum('pembayaran as total_terbayar', 'nominal')
            ->get();

        return [
            'jumlah' => $items->count(),
            'terlambat' => $items->filter(fn(self $k) => $k->tanggal_jatuh_tempo->lt($acuan))->count(),
            'sisa' => (float) $items->sum(
                fn(self $k) => max((float) $k->nominal_transaksi - (float) $k->total_terbayar, 0)
            ),
        ];
    }

    /**
     * Data grafik batang piutang yang dicatat per bulan (untuk panel halaman login).
     *
     * @return array{bars: array<int, array{label: string, nilai: float, tinggi: int, aktif: bool}>, total: float, perubahan: ?float}
     */
    public static function grafikPiutangBulanan(?Carbon $tanggalAcuan = null, int $jumlahBulan = 7): array
    {
        $bulanIni = ($tanggalAcuan ?? Carbon::today())->copy()->startOfMonth();
        $awal = $bulanIni->copy()->subMonths($jumlahBulan - 1);

        $perBulan = static::query()
            ->where('jenis', 'piutang')
            ->whereDate('tanggal_kuitansi', '>=', $awal->toDateString())
            ->get(['tanggal_kuitansi', 'nominal_transaksi'])
            ->groupBy(fn(self $k) => $k->tanggal_kuitansi->format('Y-m'))
            ->map(fn($grup) => (float) $grup->sum('nominal_transaksi'));

        $bars = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $bulan = $awal->copy()->addMonths($i);
            $bars[] = [
                'label' => $bulan->translatedFormat('M'),
                'nilai' => (float) $perBulan->get($bulan->format('Y-m'), 0.0),
                'tinggi' => 12,
                'aktif' => $i === $jumlahBulan - 1,
            ];
        }

        $maks = max(array_column($bars, 'nilai'));
        foreach ($bars as $i => $bar) {
            $bars[$i]['tinggi'] = $maks > 0 ? max((int) round($bar['nilai'] / $maks * 100), 12) : 12;
        }

        $sekarang = $bars[$jumlahBulan - 1]['nilai'];
        $sebelumnya = $bars[$jumlahBulan - 2]['nilai'];

        return [
            'bars' => $bars,
            'total' => $sekarang,
            'perubahan' => $sebelumnya > 0 ? round(($sekarang - $sebelumnya) / $sebelumnya * 100, 1) : null,
        ];
    }

    /**
     * Memproses dan mengekstrak info gambar bukti kuitansi secara dinamis
     * dari penyimpanan lokal, public storage, asset public, maupun URL eksternal.
     *
     * @return array{
     *     has_bukti: bool,
     *     bukti_kuitansi: ?string,
     *     bukti_kuitansi_name: ?string,
     *     bukti_kuitansi_meta: ?string,
     *     bukti_kuitansi_url: ?string,
     *     fallback_bukti_url: ?string
     * }
     */
    public static function resolveBuktiInfo(?string $bukti): array
    {
        $trimmed = trim((string) $bukti);
        if ($trimmed === '') {
            return [
                'has_bukti' => false,
                'bukti_kuitansi' => null,
                'bukti_kuitansi_name' => null,
                'bukti_kuitansi_meta' => null,
                'bukti_kuitansi_url' => null,
                'fallback_bukti_url' => null,
            ];
        }

        $url = null;
        $sizeBytes = null;
        $fileName = basename(parse_url($trimmed, PHP_URL_PATH) ?: $trimmed);
        $extension = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION) ?: 'JPG');

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://') || str_starts_with($trimmed, 'data:')) {
            $url = $trimmed;
        } elseif (str_starts_with($trimmed, 'storage/')) {
            $relativePath = substr($trimmed, 8);
            if (Storage::disk('public')->exists($relativePath)) {
                $sizeBytes = Storage::disk('public')->size($relativePath);
            } elseif (file_exists(public_path($trimmed))) {
                $sizeBytes = filesize(public_path($trimmed));
            }
            $url = asset($trimmed);
        } elseif (Storage::disk('public')->exists($trimmed)) {
            $sizeBytes = Storage::disk('public')->size($trimmed);
            $url = Storage::disk('public')->url($trimmed);
        } elseif (file_exists(public_path('images/' . $trimmed))) {
            $sizeBytes = filesize(public_path('images/' . $trimmed));
            $url = asset('images/' . $trimmed);
        } elseif (file_exists(public_path('images/' . pathinfo($trimmed, PATHINFO_FILENAME) . '.svg'))) {
            $svgFile = pathinfo($trimmed, PATHINFO_FILENAME) . '.svg';
            $sizeBytes = filesize(public_path('images/' . $svgFile));
            $url = asset('images/' . $svgFile);
            $extension = 'SVG';
        } elseif (file_exists(public_path($trimmed))) {
            $sizeBytes = filesize(public_path($trimmed));
            $url = asset($trimmed);
        } else {
            // Default ke public storage URL untuk file baru yang disimpan di disk public
            $url = Storage::disk('public')->url($trimmed);
        }

        $meta = $extension;
        if ($sizeBytes !== null && $sizeBytes > 0) {
            if ($sizeBytes < 1024 * 1024) {
                $meta .= ' • ' . max(1, (int) round($sizeBytes / 1024)) . ' KB';
            } else {
                $meta .= ' • ' . number_format($sizeBytes / (1024 * 1024), 1, ',', '.') . ' MB';
            }
        } else {
            $meta .= ' • Berkas kuitansi';
        }

        return [
            'has_bukti' => true,
            'bukti_kuitansi' => $trimmed,
            'bukti_kuitansi_name' => $fileName,
            'bukti_kuitansi_meta' => $meta,
            'bukti_kuitansi_url' => $url,
            'fallback_bukti_url' => asset('images/kuitansi-warung-bu-sari.svg'),
        ];
    }

    /**
     * Accessor untuk informasi bukti kuitansi terstruktur.
     *
     * @return array{
     *     has_bukti: bool,
     *     bukti_kuitansi: ?string,
     *     bukti_kuitansi_name: ?string,
     *     bukti_kuitansi_meta: ?string,
     *     bukti_kuitansi_url: ?string,
     *     fallback_bukti_url: ?string
     * }
     */
    public function getBuktiInfoAttribute(): array
    {
        return self::resolveBuktiInfo($this->bukti_kuitansi);
    }

    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_url'];
    }

    public function getBuktiFileNameAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_name'];
    }

    public function getBuktiFileMetaAttribute(): ?string
    {
        return $this->bukti_info['bukti_kuitansi_meta'];
    }

    public function getHasBuktiAttribute(): bool
    {
        return $this->bukti_info['has_bukti'];
    }
}
