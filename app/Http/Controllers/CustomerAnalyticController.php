<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Obat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // Total Penjualan Bulanan
        $totalPenjualanBulanan = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total');

        // Jumlah Transaksi Unik (proxy untuk orang datang)
        $uniqueTransactions = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();

        // Rata-rata Orang Datang Per Hari
        $daysInMonth = $startOfMonth->daysInMonth;
        $averageCustomersPerDay = $daysInMonth > 0 ? $uniqueTransactions / $daysInMonth : 0;

        // Rata-rata Pembelian Per Orang
        $averagePurchasePerCustomer = $uniqueTransactions > 0 ? $totalPenjualanBulanan / $uniqueTransactions : 0;

        return view('laporan.customer_analytics', compact(
            'month',
            'totalPenjualanBulanan',
            'uniqueTransactions',
            'averageCustomersPerDay',
            'averagePurchasePerCustomer'
        ));
    }

    public function dailySalesRecap(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $dailySales = Penjualan::selectRaw('DATE(tanggal) as sale_date, SUM(total) as total_sales, COUNT(id) as total_transactions')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'asc')
            ->get();

        return view('laporan.daily_sales_recap', compact('month', 'dailySales'));
    }

    public function stockMovementAnalysis(Request $request)
    {
        $period = $request->input('period', 3); // Default 3 bulan
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths($period);

        $obatSales = PenjualanDetail::selectRaw('obat_id, SUM(qty) as total_qty_sold')
            ->whereHas('penjualan', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('obat_id')
            ->orderByDesc('total_qty_sold')
            ->get();

        $totalObat = Obat::count();
        $fastMovingCount = ceil($totalObat * 0.20); // Top 20%
        $slowMovingCount = ceil($totalObat * 0.30); // Next 30%

        $fastMoving = collect();
        $slowMoving = collect();
        $deadStock = collect();

        $currentRank = 0;
        foreach ($obatSales as $sale) {
            $currentRank++;
            $obat = Obat::find($sale->obat_id);
            if (!$obat) continue;

            $obat->total_qty_sold = $sale->total_qty_sold;

            if ($currentRank <= $fastMovingCount) {
                $fastMoving->push($obat);
            } elseif ($currentRank <= ($fastMovingCount + $slowMovingCount)) {
                $slowMoving->push($obat);
            } else {
                $deadStock->push($obat);
            }
        }

        // Obat yang tidak terjual sama sekali dalam periode ini dianggap dead stock
        $soldObatIds = $obatSales->pluck('obat_id')->toArray();
        $unsoldObat = Obat::whereNotIn('id', $soldObatIds)->get();
        foreach ($unsoldObat as $obat) {
            $obat->total_qty_sold = 0;
            $deadStock->push($obat);
        }

        return view('laporan.stock_movement_analysis', compact('period', 'fastMoving', 'slowMoving', 'deadStock'));
    }

    public function generatePdf(Request $request, $reportType)
    {
        $data = [];
        $view = '';
        $filename = '';

        if ($reportType == 'customer_analytics') {
            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $totalPenjualanBulanan = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total');
            $uniqueTransactions = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();
            $daysInMonth = $startOfMonth->daysInMonth;
            $averageCustomersPerDay = $daysInMonth > 0 ? $uniqueTransactions / $daysInMonth : 0;
            $averagePurchasePerCustomer = $uniqueTransactions > 0 ? $totalPenjualanBulanan / $uniqueTransactions : 0;

            $data = compact('month', 'totalPenjualanBulanan', 'uniqueTransactions', 'averageCustomersPerDay', 'averagePurchasePerCustomer');
            $view = 'laporan.pdf.customer_analytics';
            $filename = 'laporan_customer_analytics_' . $month . '.pdf';
        } elseif ($reportType == 'daily_sales_recap') {
            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $dailySales = Penjualan::selectRaw('DATE(tanggal) as sale_date, SUM(total) as total_sales, COUNT(id) as total_transactions')
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->groupBy('sale_date')
                ->orderBy('sale_date', 'asc')
                ->get();
            $data = compact('month', 'dailySales');
            $view = 'laporan.pdf.daily_sales_recap';
            $filename = 'rekap_penjualan_harian_' . $month . '.pdf';
        } elseif ($reportType == 'stock_movement_analysis') {
            $period = $request->input('period', 3);
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMonths($period);

            $obatSales = PenjualanDetail::selectRaw('obat_id, SUM(qty) as total_qty_sold')
                ->whereHas('penjualan', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->groupBy('obat_id')
                ->orderByDesc('total_qty_sold')
                ->get();

            $totalObat = Obat::count();
            $fastMovingCount = ceil($totalObat * 0.20);
            $slowMovingCount = ceil($totalObat * 0.30);

            $fastMoving = collect();
            $slowMoving = collect();
            $deadStock = collect();

            $currentRank = 0;
            foreach ($obatSales as $sale) {
                $currentRank++;
                $obat = Obat::find($sale->obat_id);
                if (!$obat) continue;

                $obat->total_qty_sold = $sale->total_qty_sold;

                if ($currentRank <= $fastMovingCount) {
                    $fastMoving->push($obat);
                } elseif ($currentRank <= ($fastMovingCount + $slowMovingCount)) {
                    $slowMoving->push($obat);
                } else {
                    $deadStock->push($obat);
                }
            }
            $soldObatIds = $obatSales->pluck('obat_id')->toArray();
            $unsoldObat = Obat::whereNotIn('id', $soldObatIds)->get();
            foreach ($unsoldObat as $obat) {
                $obat->total_qty_sold = 0;
                $deadStock->push($obat);
            }

            $data = compact('period', 'fastMoving', 'slowMoving', 'deadStock');
            $view = 'laporan.pdf.stock_movement_analysis';
            $filename = 'analisis_perputaran_stok_' . $period . 'bulan.pdf';
        }

        if (empty($view)) {
            abort(404, 'Report type not found.');
        }

        $pdf = PDF::loadView($view, $data);
        return $pdf->download($filename);
    }

    public function generateExcel(Request $request, $reportType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $filename = '';

        if ($reportType == 'customer_analytics') {
            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $totalPenjualanBulanan = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('total');
            $uniqueTransactions = Penjualan::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();
            $daysInMonth = $startOfMonth->daysInMonth;
            $averageCustomersPerDay = $daysInMonth > 0 ? $uniqueTransactions / $daysInMonth : 0;
            $averagePurchasePerCustomer = $uniqueTransactions > 0 ? $totalPenjualanBulanan / $uniqueTransactions : 0;

            $sheet->setCellValue('A1', 'Laporan Customer Analytics');
            $sheet->setCellValue('A2', 'Bulan: ' . Carbon::parse($month)->translatedFormat('F Y'));
            $sheet->setCellValue('A4', 'Total Pendapatan Penjualan Bulanan');
            $sheet->setCellValue('B4', $totalPenjualanBulanan);
            $sheet->setCellValue('A5', 'Jumlah Transaksi Unik');
            $sheet->setCellValue('B5', $uniqueTransactions);
            $sheet->setCellValue('A6', 'Rata-rata Orang Datang Per Hari');
            $sheet->setCellValue('B6', round($averageCustomersPerDay, 2));
            $sheet->setCellValue('A7', 'Rata-rata Pembelian Per Orang');
            $sheet->setCellValue('B7', round($averagePurchasePerCustomer, 2));

            $filename = 'laporan_customer_analytics_' . $month . '.xlsx';
        } elseif ($reportType == 'daily_sales_recap') {
            $month = $request->input('month', Carbon::now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $dailySales = Penjualan::selectRaw('DATE(tanggal) as sale_date, SUM(total) as total_sales, COUNT(id) as total_transactions')
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->groupBy('sale_date')
                ->orderBy('sale_date', 'asc')
                ->get();

            $sheet->setCellValue('A1', 'Rekapitulasi Penjualan Harian');
            $sheet->setCellValue('A2', 'Bulan: ' . Carbon::parse($month)->translatedFormat('F Y'));
            $sheet->setCellValue('A4', 'Tanggal');
            $sheet->setCellValue('B4', 'Total Penjualan');
            $sheet->setCellValue('C4', 'Jumlah Transaksi');

            $row = 5;
            foreach ($dailySales as $sale) {
                $sheet->setCellValue('A' . $row, $sale->sale_date);
                $sheet->setCellValue('B' . $row, $sale->total_sales);
                $sheet->setCellValue('C' . $row, $sale->total_transactions);
                $row++;
            }
            $filename = 'rekap_penjualan_harian_' . $month . '.xlsx';
        } elseif ($reportType == 'stock_movement_analysis') {
            $period = $request->input('period', 3);
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMonths($period);

            $obatSales = PenjualanDetail::selectRaw('obat_id, SUM(qty) as total_qty_sold')
                ->whereHas('penjualan', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->groupBy('obat_id')
                ->orderByDesc('total_qty_sold')
                ->get();

            $totalObat = Obat::count();
            $fastMovingCount = ceil($totalObat * 0.20);
            $slowMovingCount = ceil($totalObat * 0.30);

            $fastMoving = collect();
            $slowMoving = collect();
            $deadStock = collect();

            $currentRank = 0;
            foreach ($obatSales as $sale) {
                $currentRank++;
                $obat = Obat::find($sale->obat_id);
                if (!$obat) continue;

                $obat->total_qty_sold = $sale->total_qty_sold;

                if ($currentRank <= $fastMovingCount) {
                    $fastMoving->push($obat);
                } elseif ($currentRank <= ($fastMovingCount + $slowMovingCount)) {
                    $slowMoving->push($obat);
                } else {
                    $deadStock->push($obat);
                }
            }
            $soldObatIds = $obatSales->pluck('obat_id')->toArray();
            $unsoldObat = Obat::whereNotIn('id', $soldObatIds)->get();
            foreach ($unsoldObat as $obat) {
                $obat->total_qty_sold = 0;
                $deadStock->push($obat);
            }

            $sheet->setCellValue('A1', 'Analisis Perputaran Stok');
            $sheet->setCellValue('A2', 'Periode: ' . $period . ' Bulan');
            $sheet->setCellValue('A4', 'Kategori');
            $sheet->setCellValue('B4', 'Nama Obat');
            $sheet->setCellValue('C4', 'Total Terjual');

            $row = 5;
            foreach ($fastMoving as $obat) {
                $sheet->setCellValue('A' . $row, 'Fast Moving');
                $sheet->setCellValue('B' . $row, $obat->nama);
                $sheet->setCellValue('C' . $row, $obat->total_qty_sold);
                $row++;
            }
            foreach ($slowMoving as $obat) {
                $sheet->setCellValue('A' . $row, 'Slow Moving');
                $sheet->setCellValue('B' . $row, $obat->nama);
                $sheet->setCellValue('C' . $row, $obat->total_qty_sold);
                $row++;
            }
            foreach ($deadStock as $obat) {
                $sheet->setCellValue('A' . $row, 'Dead Stock');
                $sheet->setCellValue('B' . $row, $obat->nama);
                $sheet->setCellValue('C' . $row, $obat->total_qty_sold);
                $row++;
            }
            $filename = 'analisis_perputaran_stok_' . $period . 'bulan.xlsx';
        }

        if (empty($filename)) {
            abort(404, 'Report type not found.');
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        $writer->save('php://output');
        exit;
    }
}