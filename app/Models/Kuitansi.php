<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Kuitansi extends Model
{
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
        } elseif (file_exists(public_path('images/'.$trimmed))) {
            $sizeBytes = filesize(public_path('images/'.$trimmed));
            $url = asset('images/'.$trimmed);
        } elseif (file_exists(public_path('images/'.pathinfo($trimmed, PATHINFO_FILENAME).'.svg'))) {
            $svgFile = pathinfo($trimmed, PATHINFO_FILENAME).'.svg';
            $sizeBytes = filesize(public_path('images/'.$svgFile));
            $url = asset('images/'.$svgFile);
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
                $meta .= ' • '.max(1, (int) round($sizeBytes / 1024)).' KB';
            } else {
                $meta .= ' • '.number_format($sizeBytes / (1024 * 1024), 1, ',', '.').' MB';
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
