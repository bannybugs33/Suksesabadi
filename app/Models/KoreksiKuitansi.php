<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoreksiKuitansi extends Model
{
    protected $table = 'koreksi_kuitansi';

    protected $guarded = ['id'];

    public function kuitansi(): BelongsTo
    {
        return $this->belongsTo(Kuitansi::class);
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
