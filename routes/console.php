<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat jatuh tempo (H-7 dan hari H). Dijalankan tiap jam pada jam kerja; setiap pengingat
// hanya terkirim sekali (dicatat di tabel pengingat_email), jadi aman diulang bila server sempat mati.
Schedule::command('pengingat:kirim')
    ->hourly()
    ->between('07:00', '20:00')
    ->withoutOverlapping();
