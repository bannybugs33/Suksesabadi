<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/rekapitulasi', [
    \App\Http\Controllers\RekapitulasiController::class,
    'index',
])->name('rekapitulasi.index');