<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController; // Import SupplierController
use App\Http\Controllers\ObatController;     // Import ObatController

Route::view('/', 'dashboard');

// Hapus route::view yang sudah digantikan oleh resource controller
// Route::view('/barang', 'master.barang.index');
// Route::view('/barang/create', 'master.barang.create');
// Route::view('/supplier', 'master.supplier.index');
// Route::view('/pembelian', 'transaksi.pembelian.index');
// Route::view('/penjualan', 'transaksi.penjualan.index');
// Route::view('/retur', 'transaksi.retur.index');
// Route::view('/laporan', 'laporan.index');
// Route::view('/kasir/pos', 'kasir.pos');
// Route::view('/kasir/riwayat', 'kasir.riwayat');
// Route::view('/pembelian/create', 'transaksi.pembelian.create')->name('pembelian.create');
// Route::view('/pembelian/faktur', 'transaksi.pembelian.faktur')->name('pembelian.faktur');


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

// Existing routes (pastikan tidak ada duplikasi dengan resource routes)
Route::get('/pembelian', [\App\Http\Controllers\PembelianController::class, 'index']);
Route::get('/penjualan', [\App\Http\Controllers\PenjualanController::class, 'index']);
Route::get('/retur', function () { return view('transaksi.retur.index'); }); // Jika ada ReturController, ganti ini
Route::get('/laporan', function () { return view('laporan.index'); }); // Jika ada LaporanController, ganti ini

// Kasir
Route::get('/kasir/pos', function () { return view('kasir.pos'); });
Route::get('/kasir/riwayat', [\App\Http\Controllers\PenjualanController::class, 'index']); // Menggunakan PenjualanController untuk riwayat

// Form create pembelian
Route::get('/pembelian/create', function () { return view('transaksi.pembelian.create'); })->name('pembelian.create');

// Faktur pembelian
Route::get('/pembelian/faktur', function () { return view('transaksi.pembelian.faktur'); })->name('pembelian.faktur');


// Test routes (bisa dihapus setelah development)
Route::get('/test-barang', function () {
    return \App\Models\Barang::with('supplier')->get();
});

Route::get('/test-pembelian', function () {
    return \App\Models\Pembelian::with(['pembelianDetail.barang.supplier'])->get();
});

