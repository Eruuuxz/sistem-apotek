<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockMovementService;
use App\Models\Obat;

class StockMovementController extends Controller
{
    protected $stockMovementService;

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    /**
     * Tampilkan dashboard ringkas stock movement.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '3'); // default 3 bulan
        $summary = $this->stockMovementService->getSummaryCount($period);

        // Note: Pastikan view 'dashboard.stock_movement' ada jika fungsi ini digunakan
        return view('admin.dashboard.stock_movement', compact('summary', 'period'));
    }

    /**
     * Tampilkan detail list obat dengan status.
     */
    public function detail(Request $request)
    {
        $period = $request->get('period', '3');
        $search = $request->get('search', '');

        $data = $this->stockMovementService->getStockMovementData($period);

        // Filter pencarian (nama, kode, kategori)
        if ($search) {
            $data = array_filter($data, function ($item) use ($search) {
                $searchLower = strtolower($search);
                return str_contains(strtolower($item['nama']), $searchLower)
                    || str_contains(strtolower($item['kode']), $searchLower)
                    || str_contains(strtolower($item['kategori']), $searchLower);
            });
        }

        // Pagination manual
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($data, $offset, $perPage);

        $total = count($data);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $total,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        // PERBAIKAN: Menggunakan path view yang konsisten dengan controller lain
        return view('admin.master.obat.stock_movement_detail', [
            'data' => $paginator,
            'period' => $period,
            'search' => $search,
        ]);
    }
}
