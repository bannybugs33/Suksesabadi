<?php

use App\Http\Controllers\ArsipKuitansiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JatuhTempoController;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Middleware\CegahCacheHalamanPrivat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Semua halaman data toko dan foto arsip hanya untuk owner yang sudah login.
Route::middleware(['auth', CegahCacheHalamanPrivat::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/jatuh-tempo', [JatuhTempoController::class, 'index'])->name('jatuh-tempo.index');
    Route::post('/jatuh-tempo/{kuitansi}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/arsip-kuitansi', [ArsipKuitansiController::class, 'index'])->name('arsip-kuitansi.index');
    Route::get('/arsip-kuitansi/{kuitansi}', [ArsipKuitansiController::class, 'show'])->name('arsip-kuitansi.show');
    Route::get('/arsip-kuitansi/{kuitansi}/bukti', [ArsipKuitansiController::class, 'bukti'])->name('arsip-kuitansi.bukti');
    Route::put('/arsip-kuitansi/{kuitansi}', [ArsipKuitansiController::class, 'update'])->name('arsip-kuitansi.update');
    Route::post('/arsip-kuitansi/{kuitansi}/batalkan', [ArsipKuitansiController::class, 'batalkan'])->name('arsip-kuitansi.batalkan');
    Route::put('/arsip-kuitansi/{kuitansi}/pembayaran/{pembayaran}', [ArsipKuitansiController::class, 'updatePembayaran'])->name('arsip-kuitansi.pembayaran.update');
    Route::post('/arsip-kuitansi/{kuitansi}/pembayaran/{pembayaran}/batalkan', [ArsipKuitansiController::class, 'batalkanPembayaran'])->name('arsip-kuitansi.pembayaran.batalkan');
    Route::get('/kuitansi/baru', [KuitansiController::class, 'create'])->name('kuitansi.create');
    Route::post('/kuitansi/baru', [KuitansiController::class, 'store'])->name('kuitansi.store');
    Route::get('/kuitansi/nomor-sementara', [KuitansiController::class, 'nomorSementara'])->name('kuitansi.nomor-sementara');
    Route::post('/kuitansi/cek-duplikat', [KuitansiController::class, 'cekDuplikat'])->name('kuitansi.cek-duplikat');
    Route::get('/rekapitulasi', [RekapitulasiController::class, 'index'])->name('rekapitulasi.index');
});
