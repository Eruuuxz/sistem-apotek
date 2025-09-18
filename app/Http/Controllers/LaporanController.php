<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    
    /**
     * Dashboard Laporan Utama
     */
    public function index(Request $request)
    {
        // Periode default = bulan ini
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        // Penjualan harian
        $penjualan = Penjualan::with('details.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $penjualanHarian = $penjualan->groupBy(fn($p) => $p->tanggal->format('Y-m-d'))
            ->map(fn($rows) => [
                'jumlah_transaksi' => $rows->count(),
                'total' => $rows->sum('total'),
                'total_qty' => $rows->flatMap->details->sum('qty'),
            ]);

        // Profit
        $biayaOperasional = BiayaOperasional::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $totalPenjualan = $penjualan->sum(fn($p) => $p->details->sum('subtotal'));
        $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->hpp);
        $totalBiayaOperasional = $biayaOperasional->sum('jumlah');
        $labaKotor = $totalPenjualan - $totalModal;
        $labaBersih = $labaKotor - $totalBiayaOperasional;

        // Stok menipis
        $stok = Obat::whereColumn('stok', '<', 'min_stok')
            ->orderBy('stok', 'asc')
            ->take(10)
            ->get();

        // Customer analytics
        $totalPelanggan = Pelanggan::count();
        $pelangganTerbaik = Penjualan::select('pelanggan_id', DB::raw('count(*) as total_transaksi'))
            ->with('pelanggan')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->groupBy('pelanggan_id')
            ->orderBy('total_transaksi', 'desc')
            ->limit(10)
            ->get();
setlocale(LC_TIME, 'id_ID.UTF-8');
\Carbon\Carbon::setLocale('id');

        return view('laporan.index', compact(
            'periode',
            'penjualanHarian',
            'totalPenjualan',
            'totalModal',
            'totalBiayaOperasional',
            'labaKotor',
            'labaBersih',
            'stok',
            'totalPelanggan',
            'pelangganTerbaik'
        ));
    }

    /**
     * Export laporan ke PDF/Excel
     */
public function exportPenjualan($format, Request $request)
{
    $periode = $request->get('periode', now()->format('Y-m'));
    $penjualan = Penjualan::with('details.obat')
        ->whereYear('tanggal', substr($periode, 0, 4))
        ->whereMonth('tanggal', substr($periode, 5, 2))
        ->get();

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('laporan.exports.penjualan', compact('penjualan','periode'));
        return $pdf->download("laporan-penjualan-$periode.pdf");
    }

    if ($format === 'excel') {
        return Excel::download(new \App\Exports\PenjualanExport($penjualan, $periode), "laporan-penjualan-$periode.xlsx");
    }
}

public function exportStok($format, Request $request)
{
    $periode = $request->get('periode', now()->format('Y-m'));
    $stok = Obat::whereColumn('stok', '<', 'min_stok')->orderBy('stok')->get();

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('laporan.exports.stok', compact('stok','periode'));
        return $pdf->download("laporan-stok-$periode.pdf");
    }

    if ($format === 'excel') {
        return Excel::download(new \App\Exports\StokExport($stok, $periode), "laporan-stok-$periode.xlsx");
    }
}

public function exportPelanggan($format, Request $request)
{
    $periode = $request->get('periode', now()->format('Y-m'));
    $pelangganTerbaik = Penjualan::select('pelanggan_id', DB::raw('count(*) as total_transaksi'))
        ->with('pelanggan')
        ->whereYear('tanggal', substr($periode,0,4))
        ->whereMonth('tanggal', substr($periode,5,2))
        ->groupBy('pelanggan_id')
        ->orderBy('total_transaksi','desc')
        ->get();

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('laporan.exports.pelanggan', compact('pelangganTerbaik','periode'));
        return $pdf->download("laporan-pelanggan-$periode.pdf");
    }

    if ($format === 'excel') {
        return Excel::download(new \App\Exports\PelangganExport($pelangganTerbaik, $periode), "laporan-pelanggan-$periode.xlsx");
    }
}

public function exportLaba($format, Request $request)
{
    $periode = $request->get('periode', now()->format('Y-m'));
    [$tahun, $bulan] = explode('-', $periode);

    $penjualan = Penjualan::with('details')->whereYear('tanggal',$tahun)->whereMonth('tanggal',$bulan)->get();
    $biayaOperasional = BiayaOperasional::whereYear('tanggal',$tahun)->whereMonth('tanggal',$bulan)->get();

    $totalPenjualan = $penjualan->sum(fn($p) => $p->details->sum('subtotal'));
    $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->hpp);
    $totalBiayaOperasional = $biayaOperasional->sum('jumlah');
    $labaKotor = $totalPenjualan - $totalModal;
    $labaBersih = $labaKotor - $totalBiayaOperasional;

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('laporan.exports.laba', compact(
            'periode','totalPenjualan','totalModal','labaKotor','totalBiayaOperasional','labaBersih'
        ));
        return $pdf->download("laporan-laba-$periode.pdf");
    }

    if ($format === 'excel') {
        return Excel::download(new \App\Exports\LabaExport(
            $totalPenjualan,$totalModal,$labaKotor,$totalBiayaOperasional,$labaBersih,$periode
        ), "laporan-laba-$periode.xlsx");
    }
}
}