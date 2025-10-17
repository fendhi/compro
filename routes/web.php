<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialController;

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
    
    // Dashboard - Semua role bisa akses
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // ========================================
    // TRANSAKSI (POS) - Semua role bisa akses
    // ========================================
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{id}/print', [TransaksiController::class, 'print'])->name('transaksi.print');
    
    // ========================================
    // MASTER DATA - READ: All, WRITE: Owner & Admin
    // ========================================
    // View (READ ONLY) - Semua role bisa lihat
    Route::get('/master-data/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/master-data/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    
    // Create/Update/Delete - Owner & Admin ONLY
    Route::middleware(['role:owner,admin'])->group(function () {
        Route::post('/master-data/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::put('/master-data/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/master-data/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        
        Route::post('/master-data/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('/master-data/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/master-data/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });
    
    // ========================================
    // LAPORAN - READ: All, EXPORT: Owner & Admin
    // ========================================
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    
    // ========================================
    // INVENTORY - READ: All, WRITE: Owner & Admin
    // ========================================
    // View (READ ONLY) - Semua role bisa lihat stok
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    
    // Actions - Owner & Admin ONLY
    Route::middleware(['role:owner,admin'])->group(function () {
        Route::post('/inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('/inventory/stock-opname', [InventoryController::class, 'stockOpname'])->name('inventory.stock-opname');
        Route::post('/inventory/adjustment', [InventoryController::class, 'adjustment'])->name('inventory.adjustment');
        Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
        Route::get('/inventory/low-stock-alert', [InventoryController::class, 'lowStockAlert'])->name('inventory.low-stock-alert');
    });

    // ========================================
    // AUDIT TRAIL - OWNER & ADMIN ONLY
    // ========================================
    Route::middleware(['role:owner,admin'])->group(function () {
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/audit/{id}', [AuditLogController::class, 'show'])->name('audit.show');
        Route::get('/audit-export', [AuditLogController::class, 'export'])->name('audit.export');
        Route::delete('/audit/clear-old', [AuditLogController::class, 'clearOld'])->name('audit.clear-old');
    });

    // ========================================
    // KEUANGAN - OWNER & ADMIN ONLY
    // ========================================
    Route::middleware(['role:owner,admin'])->group(function () {
        // Financial Dashboard
        Route::get('/keuangan/dashboard', [FinancialController::class, 'dashboard'])->name('keuangan.dashboard');
        
        // Pengeluaran (Expenses)
        Route::get('/keuangan/pengeluaran', [ExpenseController::class, 'index'])->name('pengeluaran.index');
        Route::get('/keuangan/pengeluaran/create', [ExpenseController::class, 'create'])->name('pengeluaran.create');
        Route::post('/keuangan/pengeluaran', [ExpenseController::class, 'store'])->name('pengeluaran.store');
        Route::get('/keuangan/pengeluaran/{id}', [ExpenseController::class, 'show'])->name('pengeluaran.show');
        Route::get('/keuangan/pengeluaran/{id}/edit', [ExpenseController::class, 'edit'])->name('pengeluaran.edit');
        Route::put('/keuangan/pengeluaran/{id}', [ExpenseController::class, 'update'])->name('pengeluaran.update');
        Route::delete('/keuangan/pengeluaran/{id}', [ExpenseController::class, 'destroy'])->name('pengeluaran.destroy');
        
        // Laporan Keuangan
        Route::get('/keuangan/laporan/laba-rugi', [FinancialController::class, 'labaRugi'])->name('keuangan.laba-rugi');
        Route::get('/keuangan/laporan/arus-kas', [FinancialController::class, 'arusKas'])->name('keuangan.arus-kas');
        Route::get('/keuangan/laporan/laba-rugi/pdf', [FinancialController::class, 'exportLabaRugiPDF'])->name('keuangan.laba-rugi.pdf');
        Route::get('/keuangan/laporan/arus-kas/pdf', [FinancialController::class, 'exportArusKasPDF'])->name('keuangan.arus-kas.pdf');
    });

    // ========================================
    // USER MANAGEMENT - OWNER ONLY
    // ========================================
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/management/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/management/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/management/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/management/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });
});
