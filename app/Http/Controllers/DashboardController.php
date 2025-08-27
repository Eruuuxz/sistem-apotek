<?php

namespace App\Http\Controllers;

use App\Models\{Obat,Supplier,Penjualan};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObat   = Obat::count();
        $totalSupplier = Supplier::count();
        $penjualanHariIni = Penjualan::whereDate('tanggal', now()->toDateString())->sum('total');

        // stok menipis
        // Pastikan kolom 'min_stok' ada di tabel 'obat'
        $stokMenipis = Obat::whereColumn('stok','<','min_stok')->count(); 

        // data untuk grafik (Day 34/35)
        $penjualanBulanan = Penjualan::selectRaw("DATE_FORMAT(tanggal,'%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->take(12) // Ambil data 12 bulan terakhir
            ->get();

        // 5 obat stok terendah
        $stokLowList = Obat::orderBy('stok','asc')->take(5)->get(['nama','stok']); 

        return view('dashboard', compact(
            'totalObat','totalSupplier','penjualanHariIni','stokMenipis','penjualanBulanan','stokLowList' 
        ));
    }
}
