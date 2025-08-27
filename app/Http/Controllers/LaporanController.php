<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, Obat}; // Tambahkan Barang
use Illuminate\Http\Request;
use Carbon\Carbon; // Digunakan untuk filter tanggal
use PDF; // Untuk export PDF
use App\Exports\PenjualanExport; // Untuk export Excel
use Maatwebsite\Excel\Facades\Excel; // Untuk export Excel

class LaporanController extends Controller
{
    // Method untuk menampilkan halaman index laporan (opsional, bisa langsung redirect ke penjualan)
    public function index()
    {
        return view('laporan.index');
    }

    public function penjualan(Request $r)
    {
        $from = $r->input('from');
        $to   = $r->input('to');

        $q = Penjualan::withCount('detail')->withSum('detail as total_detail','subtotal')->latest();

        if ($from) $q->whereDate('tanggal','>=',$from);
        if ($to)   $q->whereDate('tanggal','<=',$to);

        $data = $q->paginate(15)->withQueryString();

        // total keseluruhan sesuai filter
        $totalAll = (clone $q)->get()->sum('total');

        return view('laporan.penjualan',[
            'data'=>$data,
            'from'=>$from,
            'to'=>$to,
            'totalAll'=>$totalAll,
        ]);
    }

    public function penjualanPdf(Request $r)
    {
        $from = $r->input('from');
        $to = $r->input('to');

        $q = \App\Models\Penjualan::with('detail.obat')->latest();

        if ($from) $q->whereDate('tanggal','>=',$from);
        if ($to)   $q->whereDate('tanggal','<=',$to);

        $rows = $q->get();
        $totalAll = $rows->sum('total');

        $pdf = PDF::loadView('laporan.penjualan_pdf', compact('rows','from','to','totalAll'));
        return $pdf->download('Laporan-Penjualan.pdf');
    }

    public function penjualanExcel(Request $r)
    {
        return Excel::download(new PenjualanExport($r->from,$r->to), 'Laporan-Penjualan.xlsx');
    }

    public function stok(Request $r)
    {
        $threshold = $r->filled('threshold') ? (int)$r->threshold : null;
        $q = Obat::query();

        if ($threshold !== null) {
            $q->where('stok','<',$threshold);
        } else {
            // jika ada min_stok, pakai itu
            $q->whereColumn('stok','<','min_stok');
        }
        $data = $q->orderBy('stok','asc')->paginate(20)->withQueryString();

        return view('laporan.stok', compact('data','threshold'));
    }
}
