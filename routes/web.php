<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BukuController::class, 'index'])->name('home');

Route::prefix('buku')->name('buku.')->group(function () {
    Route::get('/', [BukuController::class, 'index'])->name('index');
    Route::post('/store', [BukuController::class, 'store'])->name('store');
    Route::post('/update/{id}', [BukuController::class, 'update'])->name('update');
    Route::post('/delete/{id}', [BukuController::class, 'destroy'])->name('delete');
});

Route::prefix('pengguna')->name('pengguna.')->group(function () {
    Route::get('/', [PenggunaController::class, 'index'])->name('index');
    Route::post('/store', [PenggunaController::class, 'store'])->name('store');
    Route::post('/update/{id}', [PenggunaController::class, 'update'])->name('update');
    Route::post('/delete/{id}', [PenggunaController::class, 'destroy'])->name('delete');
});

Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    Route::get('/', [PeminjamanController::class, 'index'])->name('index');
    Route::post('/store', [PeminjamanController::class, 'store'])->name('store');
    Route::post('/update', [PeminjamanController::class, 'pengembalian'])->name('update');
});

