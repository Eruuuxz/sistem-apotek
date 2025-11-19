<?php

namespace App\Http\Controllers;

// MODIFIKASI: Hapus 'use App\Models\Penjualan' dan 'StockMovementService'
use App\Models\{Obat, Supplier};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // MODIFIKASI: Hapus constructor __construct() karena StockMovementService tidak lagi dipakai
    
    public function index()
    {
        // Variabel ini OK (Sesuai Scope)
        $totalObat = Obat::where('stok', '>', 0)->count();
        $totalSupplier = Supplier::count();
        $stokMenipis = Obat::whereBetween('stok', [1, 10])->count(); // Asumsi min stok 10
        $stokHabis = Obat::where('stok', 0)->count();

        $obatHampirExpired = Obat::whereNotNull('expired_date')
                                 ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonth()])
                                 ->count();


        return view('admin.dashboard', compact(
            'totalObat',
            'totalSupplier',
            'stokMenipis',
            'stokHabis',
            'obatHampirExpired'
        ));
    }
}