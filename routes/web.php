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
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BiayaOperasionalController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SuratPesananController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\StockOpnameController;
use App\Models\Shift;
use App\Models\CashierShift;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Publik
Route::get('/', function () {
    return view('auth.pilih-login');
})->name('pilih-login');


Route::get('/login/admin', [RoleLoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::get('/login/kasir', [RoleLoginController::class, 'showKasirLoginForm'])->name('login.kasir');
Route::post('/login', [CustomAuthenticatedSessionController::class, 'store'])->name('login');


// Route yang Memerlukan Autentikasi
Route::middleware('auth')->group(function () {
    // Rute Logout dimodifikasi untuk redirect ke halaman pilih-login
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('pilih-login');
    })->name('logout');


    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===================================================================
    // GRUP ROUTE ADMIN
    // ===================================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- Master Data ---
        Route::resource('obat', ObatController::class);
        Route::get('/obat-search', [ObatController::class, 'search'])->name('obat.search');
        
        Route::resource('supplier', SupplierController::class);
        Route::resource('users', UserController::class);
        Route::resource('pelanggan', PelangganController::class);
        Route::get('/pelanggan/{pelanggan}/riwayat-json', [\App\Http\Controllers\PelangganController::class, 'riwayatPembelianJson'])->name('pelanggan.riwayatJson');
        
        // --- Transaksi ---
        Route::resource('surat_pesanan', SuratPesananController::class);
        Route::get('surat_pesanan/{id}/details', [SuratPesananController::class, 'getSpDetails'])->name('surat_pesanan.details');
        Route::get('/surat-pesanan/get-obat-by-supplier/{supplier}', [SuratPesananController::class, 'getObatBySupplier'])->name('surat_pesanan.getObatBySupplier');
        Route::get('/surat_pesanan/{id}/pdf', [SuratPesananController::class, 'generatePdf'])->name('surat_pesanan.pdf');

        // --- Rute Pembelian ---
        Route::resource('pembelian', PembelianController::class);
        Route::post('pembelian/from-sp/{suratPesanan}', [PembelianController::class, 'createFromSp'])->name('pembelian.createFromSp');
        Route::get('pembelian/{pembelian}/faktur', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
        Route::get('pembelian/{pembelian}/pdf', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
        Route::get('/pembelian/get-obat-by-supplier/{supplierId}', [PembelianController::class, 'getObatBySupplier'])->name('pembelian.getObatBySupplier');
        
        Route::get('/retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');
        Route::resource('retur', ReturController::class);

        
        // --- Keuangan ---
        Route::resource('biaya-operasional', BiayaOperasionalController::class);
        
        // --- Laporan ---
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/penjualan/{format}', [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
            Route::get('/stok/{format}', [LaporanController::class, 'exportStok'])->name('stok.export');
            Route::get('/pelanggan/{format}', [LaporanController::class, 'exportPelanggan'])->name('pelanggan.export');
            Route::get('/laba/{format}', [LaporanController::class, 'exportLaba'])->name('laba.export');
        });
        
        Route::resource('stock-opname', StockOpnameController::class);
        Route::post('stock-opname/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock_opname.approve');
        Route::post('stock-opname/{stock_opname}/reject', [StockOpnameController::class, 'reject'])->name('stock_opname.reject');
        Route::get('stock-opname/{stock_opname}/pdf', [StockOpnameController::class, 'generatePdf'])->name('stock_opname.pdf');
        
        Route::get('/stock-movement/detail', [StockMovementController::class, 'detail'])->name('stock_movement.detail');

    });
    
        Route::middleware(['auth', 'role:kasir'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        
        // Rute Baru: Untuk mengatur modal awal saat memulai sesi
        Route::post('/pos/set-initial-cash', [POSController::class, 'setInitialCash'])->name('pos.setInitialCash');
        
        // Rute Baru: Untuk logout / mengakhiri sesi kasir
        Route::post('/pos/clear-initial-cash', [POSController::class, 'clearInitialCash'])->name('pos.clearInitialCash');
        
        // Operasi POS tidak lagi memerlukan middleware shift
        Route::post('/pos/add', [POSController::class, 'add'])->name('pos.add');
        Route::post('/pos/update', [POSController::class, 'updateQty'])->name('pos.update');
        Route::post('/pos/remove', [POSController::class, 'remove'])->name('pos.remove');
        Route::post('/pos/set-diskon', [POSController::class, 'setDiskon'])->name('pos.setDiskon');
        Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        
        Route::get('/pos/print-options/{id}', [POSController::class, 'printOptions'])->name('pos.print.options');
        Route::get('/pos/print-faktur/{id}', [POSController::class, 'printFaktur'])->name('pos.print.faktur');
        Route::get('/pos/print-invoice/{id}', [POSController::class, 'printInvoice'])->name('pos.print.invoice');
        
        Route::get('/pos/riwayat', [POSController::class, 'riwayatKasir'])->name('kasir.riwayat');
        Route::get('/pos/riwayat/{id}', [POSController::class, 'show'])->name('penjualan.show');
        
        Route::get('/pos/search', [POSController::class, 'search'])->name('pos.search');
        Route::get('/pos/search-pelanggan', [POSController::class, 'searchPelanggan'])->name('pos.searchPelanggan');
        Route::post('/pos/add-pelanggan-cepat', [POSController::class, 'addPelangganCepat'])->name('pos.addPelangganCepat');
    });

});

require __DIR__.'/auth.php';