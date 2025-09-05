<?php

namespace App\Http\Controllers;

use App\Models\{Obat, Supplier, Penjualan};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObat = Obat::where('stok', '>', 0)->count();
        $totalSupplier = Supplier::count();
        $penjualanHariIni = Penjualan::whereDate('tanggal', now()->toDateString())->sum('total');
        $stokMenipis = Obat::whereBetween('stok', [1, 9])->count();
        $stokHabis = Obat::where('stok', 0)->count();



            $penjualanHarian = Penjualan::selectRaw("DATE(tanggal) as tgl, SUM(total) as total")
    ->groupBy('tgl')
    ->orderBy('tgl', 'asc')
    ->take(7) // Ambil 7 hari terakhir
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
            'penjualanHarian',
            'obatTerlaris',
            'stokHabis'
        ));
    }
}
