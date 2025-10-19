<?php

namespace App\Services;

use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ReportExportService
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export laporan penjualan ke PDF.
     *
     * @param string $periode Format Y-m
     * @return \Illuminate\Http\Response
     */
    public function exportPenjualanPDF(string $periode): \Illuminate\Http\Response
    {
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);
        
        $penjualan = $this->reportService->getPenjualanData($tahun, $bulan);

        $pdf = Pdf::loadView('admin.laporan.exports.penjualan', compact('penjualan', 'periode'));
        return $pdf->download("laporan-penjualan-$periode.pdf");
    }

    /**
     * Export laporan penjualan ke Excel.
     *
     * @param string $periode Format Y-m
     * @return \Illuminate\Http\Response
     */
    public function exportPenjualanExcel(string $periode): Response
    {
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);
        
        $penjualan = $this->reportService->getPenjualanData($tahun, $bulan);

        // Asumsi \App\Exports\PenjualanExport sudah ada dan menggunakan Collection $penjualan
        return Excel::download(new \App\Exports\PenjualanExport($penjualan, $periode), "laporan-penjualan-$periode.xlsx");
    }
    
    // Metode export lain (contoh: Stok) bisa ditambahkan di sini.
    /*
    public function exportStokPDF(): \Illuminate\Http\Response
    {
        // ...
    }
    */
}