<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockMovementService
{
    /**
     * Ambil data stock movement dengan klasifikasi Fast, Slow, Dead.
     *
     * @param string $period Periode filter: '3', '6', '12' bulan
     * @return array
     */
    public function getStockMovementData(string $period = '3'): array
    {
        $cacheKey = "stock_movement_data_v2_{$period}m"; // v2 untuk cache baru

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($period) {
            $months = (int) $period;
            $startDate = Carbon::now()->subMonths($months)->startOfDay();

            // Query total penjualan per obat selama periode
            $salesData = DB::table('penjualan_detail')
                ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
                ->select('penjualan_detail.obat_id', DB::raw('SUM(penjualan_detail.qty) as total_terjual'))
                ->where('penjualan.tanggal', '>=', $startDate)
                ->groupBy('penjualan_detail.obat_id')
                ->orderByDesc('total_terjual')
                ->get();

            // Total penjualan keseluruhan (sum semua obat)
            $totalSales = $salesData->sum('total_terjual');

            // Hitung kontribusi kumulatif dan klasifikasi
            $cumulative = 0;
            $classified = [];

            foreach ($salesData as $item) {
                $contribution = $totalSales > 0 ? ($item->total_terjual / $totalSales) * 100 : 0;
                $cumulative += $contribution;

                // PERBAIKAN: Menggunakan nama status yang konsisten dengan view
                if ($cumulative <= 80) { // Prinsip Pareto 80/20
                    $status = 'Fast-Moving';
                } elseif ($cumulative <= 95) {
                    $status = 'Slow-Moving';
                } else {
                    $status = 'Dead Stock';
                }

                $classified[] = [
                    'obat_id' => $item->obat_id,
                    'total_terjual' => $item->total_terjual,
                    'status' => $status,
                ];
            }

            // Obat yang tidak terjual sama sekali selama periode dianggap Dead Stock
            $allObatIds = DB::table('obat')->pluck('id')->toArray();
            $soldObatIds = $salesData->pluck('obat_id')->toArray();
            $unsoldObatIds = array_diff($allObatIds, $soldObatIds);

            foreach ($unsoldObatIds as $id) {
                $classified[] = [
                    'obat_id' => $id,
                    'total_terjual' => 0,
                    'status' => 'Dead Stock',
                ];
            }

            // Ambil metadata obat (termasuk stok)
            $obatData = DB::table('obat')
                ->whereIn('id', array_column($classified, 'obat_id'))
                ->get()
                ->keyBy('id');

            // Gabungkan data lengkap
            $result = array_map(function ($item) use ($obatData, $months) {
                $meta = $obatData[$item['obat_id']] ?? null;
                return [
                    'id' => $item['obat_id'],
                    'kode' => $meta->kode ?? '-',
                    'nama' => $meta->nama ?? '-',
                    'kategori' => $meta->kategori ?? '-',
                    // PENAMBAHAN: Menyertakan data stok
                    'stok' => $meta->stok ?? 0,
                    'total_terjual' => $item['total_terjual'],
                    // PENAMBAHAN: Menghitung rata-rata penjualan
                    'avg_sales' => $months > 0 ? $item['total_terjual'] / $months : 0,
                    'status' => $item['status'],
                ];
            }, $classified);

            return $result;
        });
    }

    /**
     * Hitung jumlah obat per kategori status.
     *
     * @param string $period
     * @return array
     */
    public function getSummaryCount(string $period = '3'): array
    {
        $data = $this->getStockMovementData($period);

        $summary = [
            'fast_moving' => 0,
            'slow_moving' => 0,
            'dead_stock' => 0,
        ];

        foreach ($data as $item) {
            if ($item['status'] === 'Fast-Moving') {
                $summary['fast_moving']++;
            } elseif ($item['status'] === 'Slow-Moving') {
                $summary['slow_moving']++;
            } else {
                $summary['dead_stock']++;
            }
        }

        return $summary;
    }
}
