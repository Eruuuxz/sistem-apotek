<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
use App\Models\Shift;
use App\Models\CashierShift;

Route::get('/', function () {
    return view('auth.pilih-login');
});

// Route untuk memilih jenis login
Route::get('/login/admin', [RoleLoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::get('/login/kasir', [RoleLoginController::class, 'showKasirLoginForm'])->name('login.kasir');

// Route untuk proses login
Route::post('/login', [CustomAuthenticatedSessionController::class, 'store'])->name('login');

// Route yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Obat
        Route::resource('obat', ObatController::class);
        Route::get('/obat-search', [ObatController::class, 'search'])->name('obat.search');

        // Manajemen Supplier
        Route::resource('supplier', SupplierController::class);
        
        // Manajemen Pelanggan
        Route::resource('pelanggan', PelangganController::class);

        // Manajemen Pembelian
        Route::resource('pembelian', PembelianController::class);
        Route::get('pembelian/{pembelian}/faktur', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
        Route::get('pembelian/{pembelian}/pdf', [PembelianController::class, 'pdf'])->name('pembelian.pdf');
        Route::get('/pembelian/get-obat-by-supplier/{supplierId}', [PembelianController::class, 'getObatBySupplier'])->name('pembelian.getObatBySupplier');

        // Manajemen Retur
        Route::resource('retur', ReturController::class);
        Route::get('/retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');

        // Manajemen User (Kasir)
        Route::resource('users', UserController::class);

        // Manajemen Biaya Operasional
        Route::resource('biaya-operasional', BiayaOperasionalController::class);

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
            Route::get('/profit-detail-json/{tanggal}', [LaporanController::class, 'profitDetailJson'])->name('profit.detail.json');
            Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
        });

        // Stock Movement
        Route::prefix('stock-movement')->name('stock.movement')->group(function () {
            Route::get('/', [StockMovementController::class, 'index']);
            Route::get('/detail', [StockMovementController::class, 'detail'])->name('.detail');
        });

        // Surat Pesanan
        Route::resource('surat_pesanan', SuratPesananController::class);
        Route::get('surat_pesanan/{surat_pesanan}/download', [SuratPesananController::class, 'downloadTemplate'])->name('surat_pesanan.download');
        Route::get('surat_pesanan/{id}/details', [SuratPesananController::class, 'getSpDetails'])->name('surat_pesanan.details');

        // Manajemen Shift (Admin)
        Route::resource('shifts', ShiftController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::get('shifts/summary', [ShiftController::class, 'summary'])->name('shifts.summary');
    });

    // Route untuk kasir (shift management tanpa check.shift middleware)
    Route::middleware('role:kasir')->group(function () {
        Route::get('/shifts/start', function () {
            $shifts = Shift::all();
            $activeShift = CashierShift::where('user_id', Auth::id())->where('status', 'open')->first();
            return view('shifts.start', compact('shifts', 'activeShift'));
        })->name('shifts.start.form');
        Route::post('/shifts/start', [ShiftController::class, 'startShift'])->name('shifts.start');
        Route::post('/shifts/end', [ShiftController::class, 'endShift'])->name('shifts.end');
        Route::get('/shifts/my-summary', [ShiftController::class, 'summary'])->name('shifts.my.summary');
    });

    // Kasir Routes dengan middleware check.shift
    Route::middleware(['role:kasir', 'check.shift'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/add', [POSController::class, 'add'])->name('pos.add');
        Route::post('/pos/update', [POSController::class, 'updateQty'])->name('pos.update');
        Route::post('/pos/remove', [POSController::class, 'remove'])->name('pos.remove');
        Route::post('/pos/set-diskon', [POSController::class, 'setDiskon'])->name('pos.setDiskon');
        Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/print-options/{id}', [POSController::class, 'printOptions'])->name('pos.print.options');
        Route::get('/pos/print-faktur/{id}', [POSController::class, 'printFaktur'])->name('pos.print.faktur');
        Route::get('/pos/print-kwitansi/{id}', [POSController::class, 'printKwitansi'])->name('pos.print.kwitansi');
        Route::get('/pos/struk-pdf/{id}', [POSController::class, 'strukPdf'])->name('pos.struk.pdf');
        Route::get('/pos/riwayat', [POSController::class, 'riwayatKasir'])->name('kasir.riwayat');
        Route::get('/pos/riwayat/{id}', [POSController::class, 'show'])->name('penjualan.show');
        Route::get('/pos/success/{id}', [POSController::class, 'success'])->name('kasir.success');
        Route::get('/pos/search', [POSController::class, 'search'])->name('pos.search');
        Route::get('/pos/search-pelanggan', [POSController::class, 'searchPelanggan'])->name('pos.searchPelanggan');
        Route::post('/pos/add-pelanggan-cepat', [POSController::class, 'addPelangganCepat'])->name('pos.addPelangganCepat');
    });
});

require __DIR__.'/auth.php';
