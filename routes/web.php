<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ObatController; // Pastikan ini di-import
use App\Http\Controllers\SupplierController; // Pastikan ini di-import
use App\Http\Controllers\PembelianController; // Pastikan ini di-import
use App\Http\Controllers\PenjualanController; // Pastikan ini di-import
use App\Http\Controllers\ReturController; // Pastikan ini di-import
use App\Http\Controllers\LaporanController; // Pastikan ini di-import
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Grup route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {

    // Route untuk Admin (hanya bisa diakses oleh role 'admin')
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::resource('obat', ObatController::class);
        Route::resource('supplier', SupplierController::class);

        // Transaksi
        Route::resource('pembelian', PembelianController::class);
        Route::get('pembelian/faktur/{pembelian}', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
        Route::get('pembelian/pdf/{pembelian}', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
        
        Route::resource('retur', ReturController::class);
        Route::get('retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
            Route::get('penjualan/pdf', [LaporanController::class, 'penjualanPdf'])->name('penjualan.pdf');
            Route::get('penjualan/excel', [LaporanController::class, 'penjualanExcel'])->name('penjualan.excel');
            Route::get('stok', [LaporanController::class, 'stok'])->name('stok');
        });
    });

    // Route untuk Kasir (bisa diakses oleh role 'kasir' dan 'admin')
    Route::middleware('role:kasir|admin')->group(function () {
        // POS (Point of Sale)
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/add', [POSController::class, 'add'])->name('pos.add');
        Route::post('/pos/update', [POSController::class, 'updateQty'])->name('pos.update');
        Route::post('/pos/remove', [POSController::class, 'remove'])->name('pos.remove');
        Route::post('/pos/checkout', [PenjualanController::class, 'checkout'])->name('pos.checkout');

        // Riwayat Penjualan (Kasir)
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::get('/penjualan/{penjualan}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');
    });
});

require __DIR__.'/auth.php';
