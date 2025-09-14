<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BiayaOperasionalController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SuratPesananController;


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
    return view('auth.pilih-login');
});

// Rute untuk memilih jenis login
Route::get('/login/admin', [RoleLoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::get('/login/kasir', [RoleLoginController::class, 'showKasirLoginForm'])->name('login.kasir');

// Menggunakan CustomAuthenticatedSessionController untuk menangani login
Route::post('/login', [CustomAuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('obat', ObatController::class);
    Route::get('/obat-search', [ObatController::class, 'search'])->name('obat.search'); // Untuk pencarian obat di POS
    Route::resource('supplier', SupplierController::class);
    
    // Transaksi
    Route::resource('pembelian', PembelianController::class);
    Route::get('/pembelian/faktur/{id}', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
    Route::get('/pembelian/pdf/{id}', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
    Route::get('/pembelian/get-obat-by-supplier/{supplierId}', [PembelianController::class, 'getObatBySupplier'])->name('pembelian.getObatBySupplier');

    Route::resource('retur', ReturController::class);
    Route::get('/retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');
    
    Route::resource('surat_pesanan', SuratPesananController::class);
    Route::get('/surat_pesanan/{suratPesanan}/download', [SuratPesananController::class, 'downloadTemplate'])->name('surat_pesanan.downloadTemplate');
    Route::get('/surat_pesanan/details/{id}', [SuratPesananController::class, 'getSpDetails'])->name('surat_pesanan.getSpDetails');
    

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('/penjualan/pdf', [LaporanController::class, 'penjualanPdf'])->name('penjualan.pdf');
        Route::get('/penjualan/excel', [LaporanController::class, 'penjualanExcel'])->name('penjualan.excel');
        Route::get('/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])->name('penjualan.bulanan');
        Route::get('/penjualan-bulanan/pdf', [LaporanController::class, 'penjualanBulananPdf'])->name('penjualan.bulanan.pdf');
        Route::get('/penjualan-bulanan/excel', [LaporanController::class, 'penjualanBulananExcel'])->name('penjualan.bulanan.excel');
        Route::get('/profit', [LaporanController::class, 'profitBulanan'])->name('profit');
        Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
    });
    
    // Management User
    Route::resource('users', UserController::class);

    // Biaya Operasional
    Route::resource('biaya-operasional', BiayaOperasionalController::class);
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    // POS System
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::post('/add', [POSController::class, 'add'])->name('add');
        Route::post('/update', [POSController::class, 'updateQty'])->name('update');
        Route::post('/remove', [POSController::class, 'remove'])->name('remove');
        Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');
        Route::get('/search', [POSController::class, 'search'])->name('search');
        Route::post('/set-diskon', [POSController::class, 'setDiskon'])->name('setDiskon');
        
        // Print Options
        Route::get('/print-options/{id}', [POSController::class, 'printOptions'])->name('print.options');
        Route::get('/print-faktur/{id}', [POSController::class, 'printFaktur'])->name('print.faktur');
        Route::get('/print-kwitansi/{id}', [POSController::class, 'printKwitansi'])->name('print.kwitansi');
        Route::get('/struk-pdf/{id}', [POSController::class, 'strukPdf'])->name('struk.pdf');
        
        // Pelanggan AJAX
        Route::get('/search-pelanggan', [POSController::class, 'searchPelanggan'])->name('searchPelanggan');
        Route::post('/add-pelanggan-cepat', [POSController::class, 'addPelangganCepat'])->name('addPelangganCepat');
    });
    
    // Riwayat Penjualan Kasir
    Route::get('/riwayat-penjualan', [POSController::class, 'riwayatKasir'])->name('kasir.riwayat');
    Route::get('/penjualan/{id}', [POSController::class, 'show'])->name('penjualan.show');
    Route::get('/penjualan/success/{id}', [POSController::class, 'success'])->name('kasir.success');
});
Route::resource('pelanggan', PelangganController::class);

require __DIR__ . '/auth.php';
