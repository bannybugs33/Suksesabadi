<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengingatEmail extends Model
{
    /**
     * @var string
     */
    protected $table = 'pengingat_email';

    public $timestamps = false;

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
            'tanggal_jatuh_tempo' => 'date',
            'dikirim_pada' => 'datetime',
        ];
    }
}
