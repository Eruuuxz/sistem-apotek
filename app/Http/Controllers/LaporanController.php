<?php

namespace App\Http\Controllers;

use App\Services\ReportService; // Import Service
use App\Services\ReportExportService; // Import Export Service
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected ReportService $reportService;
    protected ReportExportService $reportExportService;

    public function __construct(ReportService $reportService, ReportExportService $reportExportService)
    {
        $this->reportService = $reportService;
        $this->reportExportService = $reportExportService;
    }
    
    /**
     * Dashboard Laporan Utama
     */
    public function index(Request $request)
    {
        // Periode default = bulan ini
        $periode = $request->input('periode', now()->format('Y-m'));
        
        try {
            // Delegasikan semua logika bisnis dan pengambilan data ke Service
            $data = $this->reportService->getDashboardReportData($periode);
            
            // Perbaikan: Menggabungkan data menjadi compact untuk View
            return view('admin.laporan.index', $data);
            
        } catch (\Exception $e) {
            // Tangani error jika format periode salah atau query gagal
            return back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
        }
    }
    
    /**
     * Export laporan Penjualan ke PDF/Excel
     */
    public function exportPenjualan($format, Request $request)
    {
        $request->validate(['periode' => 'required|date_format:Y-m']);
        $periode = $request->get('periode');

        if ($format === 'pdf') {
            return $this->reportExportService->exportPenjualanPDF($periode);
        }
        
        if ($format === 'excel') {
            return $this->reportExportService->exportPenjualanExcel($periode);
        }
        
        return back()->with('error', 'Format export tidak didukung.');
    }
}