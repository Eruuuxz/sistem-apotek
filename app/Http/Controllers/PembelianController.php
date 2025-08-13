<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;

class PembelianController extends Controller
{
    public function index()
    {
        // Ambil semua pembelian dengan supplier & detail barangnya
        $pembelian = Pembelian::with(['pembelianDetail.barang.supplier'])->get();

        return view('transaksi.pembelian.index', compact('pembelian'));
    }
}
