<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
// use App\Http\Controllers\PenjualanController; // Dihapus (Out of Scope)
use App\Http\Controllers\ReturController;
// use App\Http\Controllers\LaporanController; // Dihapus (Out of Scope)
// use App\Http\Controllers\UserController; // Dihapus (Out of Scope)
// use App\Http\Controllers\BiayaOperasionalController; // Dihapus (Out of Scope)
// use App\Http\Controllers\POSController; // Dihapus (Out of Scope)
// use App\Http\Controllers\POSPrintController; // Dihapus (Out of Scope)
// use App\Http\Controllers\POSSearchController; // Dihapus (Out of Scope)
// use App\Http\Controllers\StockMovementController; // Dihapus (Out of Scope)
use App\Http\Controllers\SuratPesananController;
// use App\Http\Controllers\ShiftController; // Dihapus (Out of Scope)
// use App\Http\Controllers\PelangganController; // Dihapus (Out of Scope)
use App\Http\Controllers\StockOpnameController;
// use App\Models\Shift; // Dihapus (Out of Scope)
// use App\Models\CashierShift; // Dihapus (Out of Scope)

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Publik
// --- MODIFIKASI: Arahkan root / ke halaman login admin ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- MODIFIKASI: /login sekarang adalah halaman login admin ---
Route::get('/login', [RoleLoginController::class, 'showAdminLoginForm'])->name('login');
// Route::get('/login/kasir', [RoleLoginController::class, 'showKasirLoginForm'])->name('login.kasir'); // Dihapus (Out of Scope)
Route::post('/login', [CustomAuthenticatedSessionController::class, 'store']); // Tetap, tapi controller akan dimodifikasi


// Route yang Memerlukan Autentikasi
Route::middleware('auth')->group(function () {
    
    // --- MODIFIKASI: Logout redirect ke halaman login admin ---
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login'); // Redirect ke /login (admin)
    })->name('logout');


    // Profil Pengguna (Disimpan)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===================================================================
    // GRUP ROUTE ADMIN (HANYA INI YANG DIGUNAKAN)
    // ===================================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- Master Data (Sesuai Scope) ---
        Route::resource('obat', ObatController::class);
        Route::get('/obat-search', [ObatController::class, 'search'])->name('obat.search');
        Route::resource('supplier', SupplierController::class);
        
        // --- Dihapus (Out of Scope) ---
        // Route::resource('users', UserController::class);
        // Route::resource('pelanggan', PelangganController::class);
        // Route::get('/pelanggan/{pelanggan}/riwayat-json', [\App\Http\Controllers\PelangganController::class, 'riwayatPembelianJson'])->name('pelanggan.riwayatJson');
        
        // --- Transaksi (Sesuai Scope) ---
        Route::resource('surat_pesanan', SuratPesananController::class);
        Route::get('surat_pesanan/{id}/details', [SuratPesananController::class, 'getSpDetails'])->name('surat_pesanan.details');
        Route::get('/surat-pesanan/get-obat-by-supplier/{supplier}', [SuratPesananController::class, 'getObatBySupplier'])->name('surat_pesanan.getObatBySupplier');
        Route::get('/surat_pesanan/{id}/pdf', [SuratPesananController::class, 'generatePdf'])->name('surat_pesanan.pdf');

        // --- Rute Pembelian (Sesuai Scope) ---
        Route::resource('pembelian', PembelianController::class);
        Route::post('pembelian/from-sp/{suratPesanan}', [PembelianController::class, 'createFromSp'])->name('pembelian.createFromSp');
        Route::get('pembelian/{pembelian}/faktur', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
        Route::get('pembelian/{pembelian}/pdf', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
        Route::get('/pembelian/get-obat-by-supplier/{supplierId}', [PembelianController::class, 'getObatBySupplier'])->name('pembelian.getObatBySupplier');
        
        // --- Retur (Sesuai Scope, akan dimodifikasi) ---
        Route::get('/retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');
        Route::resource('retur', ReturController::class);

        
        // --- Keuangan (Dihapus, Out of Scope) ---
        // Route::resource('biaya-operasional', BiayaOperasionalController::class);
        
        // --- Laporan (Dihapus, Out of Scope) ---
        // Route::prefix('laporan')->name('laporan.')->group(function () {
        //     Route::get('/', [LaporanController::class, 'index'])->name('index');
        //     Route::get('/penjualan/{format}', [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
        // });
        
        // --- Stok (Sesuai Scope) ---
        Route::resource('stock-opname', StockOpnameController::class);
        Route::post('stock-opname/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock_opname.approve');
        Route::post('stock-opname/{stock_opname}/reject', [StockOpnameController::class, 'reject'])->name('stock_opname.reject');
        Route::get('stock-opname/{stock_opname}/pdf', [StockOpnameController::class, 'generatePdf'])->name('stock_opname.pdf');
        
        // --- Laporan Stok (Dihapus, Out of Scope) ---
        // Route::get('/stock-movement/detail', [StockMovementController::class, 'detail'])->name('stock_movement.detail');

    });
    
    // ===================================================================
    // GRUP ROUTE KASIR (SEMUA DIHAPUS)
    // ===================================================================
    // Route::middleware(['auth', 'role:kasir'])->group(function () {
        // ... semua route POS dihapus ...
    // });

});

// File auth.php standar Laravel tetap dimuat untuk proses reset password, dll.
require __DIR__.'/auth.php';