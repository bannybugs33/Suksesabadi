<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    public const TYPE_RECEIVABLE = 'receivable';
    public const TYPE_PAYABLE = 'payable';
    public const STATUS_LUNAS = 'lunas';

    public const STATUS_LABELS = [
        'lunas' => 'Lunas',
        'cicilan' => 'Cicilan',
        'dp' => 'DP',
        'belum_lunas' => 'Belum lunas',
    ];

    protected $fillable = [
        'code', 'party_id', 'type', 'total_amount', 'paid_amount',
        'issued_at', 'due_date', 'status', 'settled_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'paid_amount' => 'integer',
            'issued_at' => 'date',
            'due_date' => 'date',
            'settled_at' => 'datetime',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Kuitansi yang belum lunas. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_LUNAS);
    }

    /** Jatuh tempo dalam N hari ke depan (termasuk yang sudah lewat). */
    public function scopeDueWithin(Builder $query, int $days): Builder
    {
        return $query->whereDate('due_date', '<=', today()->addDays($days));
    }

    public function getRemainingAttribute(): int
    {
        return max($this->total_amount - $this->paid_amount, 0);
    }

    /** Selisih hari ke jatuh tempo (negatif = terlambat). */
    public function getDaysLeftAttribute(): int
    {
        return (int) today()->diffInDays($this->due_date->copy()->startOfDay(), false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->days_left < 0;
    }

    public function getDueLabelAttribute(): string
    {
        $days = $this->days_left;

        return match (true) {
            $days < 0 => 'Terlambat ' . abs($days) . ' hari',
            $days === 0 => 'Jatuh tempo hari ini',
            $days === 1 => 'Jatuh tempo besok',
            default => 'Tempo ' . $days . ' hari lagi',
        };
    }
}
