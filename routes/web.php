<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController; // Ini tidak digunakan, bisa dihapus jika tidak ada AuthController lain
use App\Http\Controllers\Auth\RoleLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome'); // Landing page → hanya ada tombol login
});

Route::get('/pilih-login', function () {
    return view('auth/pilih-login'); // Halaman pilih role login
});

// Login khusus role
Route::get('/login/admin', [RoleLoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::get('/login/kasir', [RoleLoginController::class, 'showKasirLoginForm'])->name('login.kasir');

// Route untuk PDF struk (tanpa middleware auth agar bisa diakses publik)
Route::get('/penjualan/{id}/struk/pdf', [PenjualanController::class, 'strukPdf'])->name('penjualan.struk.pdf');

    // ... (bagian atas file)

// Grup route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Route untuk Admin (hanya bisa diakses oleh role 'admin')
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Master Data
        Route::resource('obat', ObatController::class);
        Route::resource('supplier', SupplierController::class);
        // Route::resource('pelanggan', PelangganController::class); // Pindahkan ini
        // Transaksi
        Route::resource('pembelian', PembelianController::class);
        Route::get('/laporan/profit', [LaporanController::class, 'profitBulanan'])->name('profit');
        Route::get('/supplier/{id}/obat', [PembelianController::class, 'getObatBySupplier']);
        Route::get('pembelian/faktur/{pembelian}', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
        Route::get('pembelian/pdf/{pembelian}', [PembelianController::class, 'pdf'])->name('pembelian.faktur');
        Route::resource('retur', ReturController::class);
        Route::get('retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');
        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
            Route::get('penjualan/pdf', [LaporanController::class, 'penjualanPdf'])->name('penjualan.pdf');
            Route::get('penjualan/excel', [LaporanController::class, 'penjualanExcel'])->name('penjualan.excel');
            Route::get('penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])->name('penjualan.bulanan');
            Route::get('penjualan-bulanan/pdf', [LaporanController::class, 'penjualanBulananPdf'])->name('penjualan.bulanan.pdf');
            Route::get('penjualan-bulanan/excel', [LaporanController::class, 'penjualanBulananExcel'])->name('penjualan.bulanan.excel');
            Route::get('stok', [LaporanController::class, 'stok'])->name('stok');
            Route::get('profit', [LaporanController::class, 'profitBulanan'])->name('profit');
            Route::get('/laporan/profit/detail/{tanggal}', [LaporanController::class, 'profitDetailJson']);
        });
        Route::resource('users', UserController::class);
        Route::resource('biaya-operasional', BiayaOperasionalController::class);
        // Route detail retur
        Route::get('/retur/{retur}', [\App\Http\Controllers\ReturController::class, 'show'])->name('retur.show');
    });
    // Route untuk Kasir (hanya bisa diakses oleh role 'kasir')
    Route::middleware('role:kasir')->group(function () {
        // POS
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/add', [POSController::class, 'add'])->name('pos.add');
        Route::post('/pos/update', [POSController::class, 'updateQty'])->name('pos.update');
        Route::post('/pos/remove', [POSController::class, 'remove'])->name('pos.remove');
        Route::post('/pos/checkout', [PenjualanController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/search', [POSController::class, 'search'])->name('pos.search');
        // Quick Search Obat (API)
        Route::get('/api/obat/search', [ObatController::class, 'search'])->name('api.obat.search');
        // Print Options
        Route::get('/pos/print/{id}', [PenjualanController::class, 'printOptions'])->name('pos.print.options');
        Route::get('/pos/print/faktur/{id}', [PenjualanController::class, 'printFaktur'])->name('pos.print.faktur'); // struk.blade.php
        Route::get('/pos/print/kwitansi/{id}', [PenjualanController::class, 'printKwitansi'])->name('pos.print.kwitansi');
        Route::get('/pos/print/struk-pdf/{id}', [PenjualanController::class, 'strukPdf'])->name('pos.print.struk.pdf');
        // Riwayat & Success
        Route::get('/kasir/riwayat', [PenjualanController::class, 'riwayatKasir'])->name('kasir.riwayat');
        Route::get('/kasir/success/{id}', [PenjualanController::class, 'success'])->name('kasir.success');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        // Master Data yang bisa diakses kasir
        // Tambahkan akses ke daftar obat untuk kasir
        Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
    });
    Route::resource('pelanggan', PelangganController::class);
    // Riwayat Penjualan (bisa diakses oleh role 'kasir' dan 'admin')
    // Route::middleware('role:kasir')->group(function () { // Ini sudah dihandle di atas, bisa dihapus atau diganti
    //     Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    //     Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
    //     Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');
    // });
});
require __DIR__ . '/auth.php';
