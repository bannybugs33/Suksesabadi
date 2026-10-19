<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends Model
{
    protected $fillable = ['name', 'type', 'phone'];

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
