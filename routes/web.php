<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PembelianController; // Tambahkan ini
use App\Http\Controllers\ReturController;     // Tambahkan ini
use App\Http\Controllers\POSController;       // Tambahkan ini
use App\Http\Controllers\PenjualanController; // Tambahkan ini

Route::view('/', 'dashboard');

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

// Master Data Routes
Route::resource('barang', BarangController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('obat', ObatController::class);

// Transaksi Pembelian Routes (Day 15-18)
Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
Route::get('/pembelian/faktur/{id}', [PembelianController::class, 'faktur'])->name('pembelian.faktur');
Route::get('/pembelian/faktur/{id}/pdf', [PembelianController::class, 'pdf'])->name('pembelian.pdf');

// Transaksi Retur Routes (Day 19-21)
Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create');
Route::post('/retur', [ReturController::class, 'store'])->name('retur.store');
Route::get('/retur/sumber/{jenis}/{id}', [ReturController::class, 'sumber'])->name('retur.sumber');

// POS & Penjualan Routes (Day 22-28)
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/add', [POSController::class, 'add'])->name('pos.add');
Route::post('/pos/update', [POSController::class, 'updateQty'])->name('pos.update');
Route::post('/pos/remove', [POSController::class, 'remove'])->name('pos.remove');
Route::post('/pos/checkout', [PenjualanController::class, 'checkout'])->name('pos.checkout');
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index'); // Riwayat Penjualan
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show'); // Detail Penjualan
Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk'); // Cetak Struk

// Rute lama yang sudah digantikan atau tidak relevan lagi (dihapus/dikomentari)
// Route::get('/pembelian', [\App\Http\Controllers\PembelianController::class, 'index']); // Diganti dengan name route
// Route::get('/penjualan', [\App\Http\Controllers\PenjualanController::class, 'index']); // Diganti dengan name route
// Route::get('/retur', function () { return view('transaksi.retur.index'); }); // Diganti dengan ReturController
// Route::get('/laporan', function () { return view('laporan.index'); });
// Route::get('/kasir/pos', function () { return view('kasir.pos'); }); // Diganti dengan POSController
// Route::get('/kasir/riwayat', [\App\Http\Controllers\PenjualanController::class, 'index']); // Diganti dengan PenjualanController index
// Route::get('/pembelian/create', function () { return view('transaksi.pembelian.create'); })->name('pembelian.create'); // Diganti dengan PembelianController
// Route::get('/pembelian/faktur', function () { return view('transaksi.pembelian.faktur'); })->name('pembelian.faktur'); // Diganti dengan PembelianController

// Test routes (bisa dihapus setelah development)
Route::get('/test-barang', function () {
    return \App\Models\Barang::with('supplier')->get();
});

Route::get('/test-pembelian', function () {
    return \App\Models\Pembelian::with(['pembelianDetail.barang.supplier'])->get();
});
