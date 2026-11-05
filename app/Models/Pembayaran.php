<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_pembayaran' => 'date',
            'dibatalkan_pada' => 'datetime',
        ];
    }

    public function kuitansi(): BelongsTo
    {
        return $this->belongsTo(Kuitansi::class);
    }

    /**
     * Label metode pembayaran yang seragam: "Tunai" atau "Transfer".
     * Data lama boleh tersimpan sebagai "Transfer bank" atau "Tunai - DP"; tampilannya disatukan.
     */
    public static function labelMetode(?string $metode): string
    {
        $nilai = mb_strtolower(trim((string) $metode));

        return match (true) {
            $nilai === '' => '-',
            str_starts_with($nilai, 'transfer') => 'Transfer',
            str_starts_with($nilai, 'tunai') => 'Tunai',
            default => trim((string) $metode),
        };
    }
}
