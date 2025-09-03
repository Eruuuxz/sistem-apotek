<?php

namespace App\Http\Controllers;

use App\Models\{Obat, Supplier, Penjualan};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObat = Obat::where('stok', '>', 0)->count();
        $totalSupplier = Supplier::count();
        $penjualanHariIni = Penjualan::whereDate('tanggal', now()->toDateString())->sum('total');
        $stokMenipis = Obat::whereBetween('stok', [1, 9])->count();
        $stokHabis = Obat::where('stok', 0)->count();

        // data untuk grafik (Day 34/35)
        $penjualanBulanan = Penjualan::selectRaw("DATE_FORMAT(tanggal,'%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->take(12) // Ambil data 12 bulan terakhir
            ->get();

    // Obat terlaris (top 5)
    $obatTerlaris = DB::table('penjualan_detail')
        ->join('obat', 'penjualan_detail.obat_id', '=', 'obat.id')
        ->select('obat.nama', DB::raw('SUM(penjualan_detail.qty) as total_terjual'))
        ->groupBy('obat.nama')
        ->orderByDesc('total_terjual')
        ->limit(5)
        ->get();


        return view('dashboard', compact(
            'totalObat',
            'totalSupplier',
            'penjualanHariIni',
            'stokMenipis',
            'penjualanBulanan',
            'obatTerlaris',
            'stokHabis'
        ));
    }
}
