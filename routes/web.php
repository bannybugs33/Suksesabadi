<?php

use App\Http\Controllers\ArsipKuitansiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JatuhTempoController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RekapitulasiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/jatuh-tempo', [JatuhTempoController::class, 'index'])->name('jatuh-tempo.index');
    Route::post('/jatuh-tempo/{kuitansi}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/arsip-kuitansi', [ArsipKuitansiController::class, 'index'])->name('arsip-kuitansi.index');
    Route::get('/arsip-kuitansi/{kuitansi}', [ArsipKuitansiController::class, 'show'])->name('arsip-kuitansi.show');
    Route::get('/rekapitulasi', [RekapitulasiController::class, 'index'])->name('rekapitulasi.index');
});
