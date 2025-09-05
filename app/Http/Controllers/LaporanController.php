<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, Obat, Pembelian}; // Perbaiki 'pembelian' menjadi 'Pembelian'
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Spatie\SimpleExcel\SimpleExcelWriter;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    // 📊 Laporan Penjualan hari ini
    public function penjualan(Request $request)
    {
        if ($request->has('tanggal')) {
            $tanggal = $request->get('tanggal');
            $offset = null;
        } else {
            $offset = (int) $request->get('day', 0);
            $tanggal = now()->subDays($offset)->toDateString();
        }

        $data = Penjualan::with(['kasir'])
            ->withCount('details')
            ->withSum('details as total_qty', 'qty')
            ->whereDate('tanggal', $tanggal) // Tetap gunakan whereDate untuk filter harian
            ->orderBy('tanggal', 'desc')
            ->paginate(10);


        $totalAll = Penjualan::whereDate('tanggal', $tanggal)->sum('total');
        $jumlahTransaksi = $data->total();

        return view('laporan.penjualan', compact(
            'data',
            'tanggal',
            'offset',
            'totalAll',
            'jumlahTransaksi'
        ));
    }
    // 📑 Export PDF
    public function penjualanPdf(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        $rows = Penjualan::with('details.obat')
            ->whereDate('tanggal', $tanggal)
            ->latest()
            ->get();

        $totalAll = $rows->sum('total');

        $pdf = PDF::loadView('laporan.penjualan_pdf', compact('rows', 'tanggal', 'totalAll'));
        return $pdf->download("Laporan-Penjualan-{$tanggal}.pdf");
    }

    // 📊 Export Excel
    public function penjualanExcel(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        $penjualan = Penjualan::withCount('details')
            ->whereDate('tanggal', $tanggal)
            ->get();

        $filename = "laporan-penjualan-{$tanggal}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($penjualan, $tanggal) {
            $file = fopen('php://output', 'w');

            // Set semicolon sebagai pemisah
            $delimiter = ';';

            // Judul laporan
            fputcsv($file, ["Laporan Penjualan - {$tanggal}"], $delimiter);
            fputcsv($file, [], $delimiter); // baris kosong

            // Header tabel
            fputcsv($file, ['No Nota', 'Tanggal', 'Total', 'Item'], $delimiter);

            // Data
            foreach ($penjualan as $row) {
                fputcsv($file, [
                    $row->no_nota,
                    Carbon::parse($row->tanggal)->format('Y-m-d H:i:s'), // Format tanggal dengan jam
                    $row->total,
                    $row->details_count,
                ], $delimiter);
            }

            // Total summary
            fputcsv($file, [], $delimiter);
            fputcsv($file, ['TOTAL', '', $penjualan->sum('total'), $penjualan->sum('details_count')], $delimiter);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function penjualanBulanan(Request $request)
    {
        // Ambil bulan & tahun dari request atau default bulan ini
        $periode = $request->get('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        // Data penjualan bulan ini
        $data = Penjualan::with(['kasir'])
            ->withCount('details')
            ->withSum('details as total_qty', 'qty')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);


        $totalAll = Penjualan::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('total');
        $jumlahTransaksi = $data->total();


        // Hitung bulan sebelumnya & berikutnya
        $current = Carbon::create($tahun, $bulan, 1);
        $prevMonth = [
            'bulan' => $current->copy()->subMonth()->month,
            'tahun' => $current->copy()->subMonth()->year,
        ];
        $nextMonth = [
            'bulan' => $current->copy()->addMonth()->month,
            'tahun' => $current->copy()->addMonth()->year,
        ];
        $totalObatTerjual = $data->flatMap(fn($r) => $r->details)->sum('qty');


        return view('laporan.penjualan_bulanan', [
            'data' => $data,
            'bulan' => (int) $bulan,
            'tahun' => (int) $tahun,
            'totalAll' => $totalAll,
            'jumlahTransaksi' => $jumlahTransaksi,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'totalObatTerjual' => $totalObatTerjual,
        ]);
    }

    public function profitDetailJson($tanggal)
    {
        $data = Penjualan::with('details.obat', 'kasir')
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->map(function ($row) {
                $row->total_qty = $row->details->sum('qty');
                return $row;
            });

        return response()->json($data);
    }

    public function penjualanBulananPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $rows = Penjualan::with('details.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $totalAll = $rows->sum('total');

        $pdf = \PDF::loadView('laporan.penjualan_pdf', compact('rows', 'bulan', 'tahun', 'totalAll'));
        return $pdf->download("Laporan-Penjualan-Bulanan-{$bulan}-{$tahun}.pdf");
    }

    public function penjualanBulananExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $penjualan = Penjualan::withCount('details')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $filename = "laporan-penjualan-bulanan-{$bulan}-{$tahun}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($penjualan, $bulan, $tahun) {
            $file = fopen('php://output', 'w');
            $delimiter = ';';

            fputcsv($file, ["Laporan Penjualan Bulanan - {$bulan}/{$tahun}"], $delimiter);
            fputcsv($file, [], $delimiter);
            fputcsv($file, ['No Nota', 'Tanggal', 'Total', 'Item'], $delimiter);

            foreach ($penjualan as $row) {
                fputcsv($file, [
                    $row->no_nota,
                    Carbon::parse($row->tanggal)->format('Y-m-d H:i:s'), // Format tanggal dengan jam
                    $row->total,
                    $row->details_count,
                ], $delimiter);
            }

            fputcsv($file, [], $delimiter);
            fputcsv($file, ['TOTAL', '', $penjualan->sum('total'), $penjualan->sum('details_count')], $delimiter);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 📉 Laporan stok
    public function stok(Request $request)
    {
        $threshold = $request->filled('threshold') ? (int) $request->threshold : null;

        $q = Obat::query();

        if ($threshold !== null) {
            $q->where('stok', '<', $threshold);
        } else {
            $q->whereColumn('stok', '<', 'min_stok');
        }

        $data = $q->orderBy('stok', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('laporan.stok', compact('data', 'threshold'));
    }

    public function profitBulanan(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        // Penjualan
        $penjualan = Penjualan::with('details.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $totalPenjualan = $penjualan->sum('total');
        $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->obat->harga_dasar);
        $totalPengeluaran = Pembelian::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('total');
        $keuntungan = $totalPenjualan - $totalModal - $totalPengeluaran;
        return view('laporan.profit', compact(
            'penjualan', 'bulan', 'tahun',
            'totalPenjualan', 'totalModal', 'keuntungan',
            'totalPengeluaran'
        ));
    }

}
