<?php

namespace App\Http\Controllers;

use App\Models\{Obat, Supplier, Penjualan};
use App\Services\StockMovementService; // 1. Import service
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * @var StockMovementService
     */
    protected $stockMovementService;

    // 2. Gunakan constructor untuk dependency injection
    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    public function index()
    {
        $totalObat = Obat::where('stok', '>', 0)->count();
        $totalSupplier = Supplier::count();
        $penjualanHariIni = Penjualan::whereDate('tanggal', now()->toDateString())->sum('total');
        $stokMenipis = Obat::whereBetween('stok', [1, 10])->count();
        $stokHabis = Obat::where('stok', 0)->count();

        // 3. Tambah query untuk obat yang akan expired dalam 1 bulan
        $obatHampirExpired = Obat::whereNotNull('expired_date')
                                 ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonth()])
                                 ->count();

        //penjualan 7 hari terakhir
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $penjualanHarian = Penjualan::selectRaw('DATE(tanggal) as tgl, SUM(total) as total')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('tgl')
            ->orderBy('tgl', 'asc')
            ->get();

        //obat terlaris bulan ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $obatTerlaris = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->join('obat', 'penjualan_detail.obat_id', '=', 'obat.id')
            ->whereYear('penjualan.tanggal', $tahunIni)
            ->whereMonth('penjualan.tanggal', $bulanIni)
            ->select('obat.nama', DB::raw('SUM(penjualan_detail.qty) as total_terjual'))
            ->groupBy('obat.nama')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
        
        // 4. Panggil service untuk mendapatkan ringkasan stock movement (periode 3 bulan)
        $stockMovementSummary = $this->stockMovementService->getSummaryCount(3);


        return view('dashboard', compact(
            'totalObat',
            'totalSupplier',
            'penjualanHariIni',
            'stokMenipis',
            'penjualanHarian',
            'obatTerlaris',
            'stokHabis',
            'obatHampirExpired', // 5. Kirim data baru ke view
            'stockMovementSummary'
        ));
    }
}
