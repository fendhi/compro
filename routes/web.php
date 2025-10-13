<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    
    // Master Data - Barang
    Route::get('/master-data/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/master-data/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/master-data/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/master-data/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    
    // Master Data - Kategori
    Route::get('/master-data/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/master-data/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/master-data/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/master-data/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    
    // Laporan
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    
    // Management User
    Route::get('/management/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/management/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/management/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/management/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});
