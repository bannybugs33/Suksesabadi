<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = ['user_id', 'receipt_id', 'type', 'title', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    /** [nama ikon, kelas warna] */
    public function getIconAttribute(): array
    {
        return match ($this->type) {
            'payment_recorded' => ['coin', 'bg-slate-100 text-slate-500'],
            'receipt_created' => ['file-plus', 'bg-brand-50 text-brand-500'],
            default => ['check-circle', 'bg-sky-50 text-sky-600'],
        };
    }

    public function getWhenLabelAttribute(): string
    {
        $time = $this->created_at->format('H.i');

        return match (true) {
            $this->created_at->isToday() => "Hari ini, $time",
            $this->created_at->isYesterday() => "Kemarin, $time",
            default => $this->created_at->isoFormat('D MMM') . ", $time",
        };
    }
}
