<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;

Route::view('/', 'dashboard');
Route::view('/barang', 'master.barang.index');
Route::view('/barang/create', 'master.barang.create');

Route::view('/supplier', 'master.supplier.index');
Route::view('/pembelian', 'transaksi.pembelian.index');
Route::view('/penjualan', 'transaksi.penjualan.index');
Route::view('/retur', 'transaksi.retur.index');

Route::view('/laporan', 'laporan.index');

// Kasir
Route::view('/kasir/pos', 'kasir.pos');
Route::view('/kasir/riwayat', 'kasir.riwayat');


// Form create pembelian
Route::view('/pembelian/create', 'transaksi.pembelian.create')->name('pembelian.create');

// Faktur pembelian
Route::view('/pembelian/faktur', 'transaksi.pembelian.faktur')->name('pembelian.faktur');

/**      */ 

Route::get('/barang', [BarangController::class, 'index']);
Route::get('/test-barang', function () {
    return \App\Models\Barang::with('supplier')->get();
});

Route::get('/test-pembelian', function () {
    return \App\Models\Pembelian::with(['pembelianDetail.barang.supplier'])->get();
});

/*  */

Route::get('/barang', [BarangController::class, 'index']);
Route::get('/barang/create', [BarangController::class, 'create']);
Route::post('/barang', [BarangController::class, 'store']);
