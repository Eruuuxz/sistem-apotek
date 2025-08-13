<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with(['penjualanDetail.barang'])->get();

        return view('kasir.riwayat', compact('penjualan'));
    }
}
