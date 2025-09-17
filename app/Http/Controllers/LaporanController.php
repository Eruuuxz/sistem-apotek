<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, Obat, Pembelian, BiayaOperasional, Pelanggan, PenjualanDetail, BatchObat, CashierShift};
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
            ->whereDate('tanggal', $tanggal)
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

        $penjualan = Penjualan::with('details.obat')
            ->whereDate('tanggal', $tanggal)
            ->get();

        $filename = "laporan-penjualan-{$tanggal}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($penjualan, $tanggal) {
            $file = fopen('php://output', 'w');
            $delimiter = ';';

            fputcsv($file, ["Laporan Penjualan - {$tanggal}"], $delimiter);
            fputcsv($file, [], $delimiter);

            fputcsv($file, ['No', 'No Nota', 'Tanggal', 'Nama Obat (Qty)', 'Total Qty', 'Subtotal'], $delimiter);

            foreach ($penjualan as $i => $row) {
                $obatList = $row->details
                    ->map(fn($d) => ($d->obat->nama ?? '-') . " ({$d->qty})")
                    ->join(', ');

                $totalQty = $row->details->sum('qty');
                $subtotal = $row->details->sum('subtotal');

                fputcsv($file, [
                    $i + 1,
                    $row->no_nota,
                    \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i:s'),
                    $obatList,
                    $totalQty,
                    $subtotal
                ], $delimiter);
            }

            $totalAll = $penjualan->sum(fn($r) => $r->details->sum('subtotal'));
            $totalQtyAll = $penjualan->sum(fn($r) => $r->details->sum('qty'));
            fputcsv($file, [], $delimiter);
            fputcsv($file, ['TOTAL', '', '', '', $totalQtyAll, $totalAll], $delimiter);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function penjualanBulanan(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

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

        $totalAll = $rows->sum(fn($p) => $p->details->sum('subtotal'));

        $pdf = \PDF::loadView('laporan.penjualan_pdf', compact('rows', 'totalAll', 'bulan', 'tahun'));

        return $pdf->download("Laporan-Penjualan-Bulanan-{$bulan}-{$tahun}.pdf");
    }

    public function penjualanBulananExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $penjualan = Penjualan::with('details.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $filename = "laporan-penjualan-bulanan-{$bulan}-{$tahun}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($penjualan) {
            $file = fopen('php://output', 'w');
            $delimiter = ';';

            fputcsv($file, ["Laporan Penjualan Bulanan"], $delimiter);
            fputcsv($file, [], $delimiter);

            fputcsv($file, ['No', 'No Nota', 'Tanggal', 'Nama Obat (Qty)', 'Total Qty', 'Subtotal'], $delimiter);

            $no = 1;
            foreach ($penjualan as $row) {
                $obatList = $row->details->map(fn($d) => ($d->obat->nama ?? '-') . " ({$d->qty})")->join(', ');
                $totalQty = $row->details->sum('qty');
                $subtotal = $row->details->sum('subtotal');

                fputcsv($file, [
                    $no++,
                    $row->no_nota,
                    $row->tanggal,
                    $obatList,
                    $totalQty,
                    $subtotal
                ], $delimiter);
            }

            fputcsv($file, [], $delimiter);
            fputcsv($file, ['TOTAL', '', '', '', $penjualan->sum(fn($p) => $p->details->sum('qty')), $penjualan->sum(fn($p) => $p->details->sum('subtotal'))], $delimiter);

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

    // ✅ Laporan Laba & Rugi Bulanan
    public function profitBulanan(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        $penjualan = Penjualan::with('details.obat')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->get();
        $biayaOperasional = BiayaOperasional::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->get();
        
        $totalPenjualan = $penjualan->sum(fn($p) => $p->details->sum('subtotal'));
        $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->hpp);
        $totalPpnPenjualan = $penjualan->sum('ppn');
        $totalBiayaOperasional = $biayaOperasional->sum('jumlah');

        $labaKotor = $totalPenjualan - $totalModal;
        $labaBersih = $labaKotor - $totalBiayaOperasional;

        $current = Carbon::create($tahun, $bulan, 1);
        $prevMonth = ['bulan' => $current->copy()->subMonth()->month, 'tahun' => $current->copy()->subMonth()->year];
        $nextMonth = ['bulan' => $current->copy()->addMonth()->month, 'tahun' => $current->copy()->addMonth()->year];

        return view('laporan.profit', compact(
            'penjualan', 
            'bulan', 
            'tahun',
            'totalPenjualan', 
            'totalModal', 
            'totalPpnPenjualan',
            'totalBiayaOperasional', 
            'labaKotor', 
            'labaBersih',
            'prevMonth', 
            'nextMonth'
        ));
    }
    
    // ✅ Laporan Analisis Pelanggan
    public function customerAnalytics(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $totalPelanggan = Pelanggan::count();

        $pelangganBaru = Pelanggan::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $pelangganTerbaik = Penjualan::select('pelanggan_id', DB::raw('count(*) as total_transaksi'))
            ->with('pelanggan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('pelanggan_id')
            ->groupBy('pelanggan_id')
            ->orderBy('total_transaksi', 'desc')
            ->limit(10)
            ->get();
        
        $totalPenjualan = Penjualan::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $jumlahTransaksi = Penjualan::whereBetween('tanggal', [$startDate, $endDate])->count();
        $rataRataTransaksi = $jumlahTransaksi > 0 ? $totalPenjualan / $jumlahTransaksi : 0;

        return view('laporan.customer_analytics', compact('totalPelanggan', 'pelangganBaru', 'pelangganTerbaik', 'rataRataTransaksi', 'periode'));
    }

    // ✅ Laporan Rekap Penjualan Harian
    public function dailySalesRecap(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);
        
        $penjualanHarian = Penjualan::select(
                DB::raw('DATE(tanggal) as tanggal_jual'),
                DB::raw('count(*) as jumlah_transaksi'),
                DB::raw('sum(total) as total_penjualan')
            )
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tanggal_jual', 'asc')
            ->get();

        return view('laporan.daily_sales_recap', compact('penjualanHarian', 'periode'));
    }
    
    // ✅ Laporan Pergerakan Stok
    public function stockMovementAnalysis(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        [$tahun, $bulan] = explode('-', $periode);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $outflows = PenjualanDetail::with('obat', 'penjualan')
            ->whereHas('penjualan', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->penjualan->tanggal,
                    'obat_nama' => $item->obat->nama,
                    'jenis' => 'Penjualan',
                    'qty' => -$item->qty,
                    'no_referensi' => $item->penjualan->no_nota
                ];
            });

        $inflows = Pembelian::with('details.obat')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->flatMap(function($pembelian) {
                return $pembelian->details->map(function($detail) use ($pembelian) {
                    return [
                        'tanggal' => $pembelian->tanggal,
                        'obat_nama' => $detail->obat->nama,
                        'jenis' => 'Pembelian',
                        'qty' => $detail->qty,
                        'no_referensi' => $pembelian->no_faktur
                    ];
                });
            });
        
        $pergerakanStok = $inflows->merge($outflows)->sortBy('tanggal');

        return view('laporan.stock_movement_analysis', compact('pergerakanStok', 'periode'));
    }
    
    // ✅ Laporan Perputaran Stok
    public function perputaranStok()
    {
        $semuaObat = \App\Models\Obat::all();

        $deadStock = [];
        $slowMoving = [];
        $fastMoving = [];

        $semuaObat->each(function ($obat) use (&$deadStock, &$slowMoving, &$fastMoving) {
            $lastSaleDetail = \App\Models\PenjualanDetail::where('obat_id', $obat->id)
                ->latest('created_at')
                ->first();

            if (!$lastSaleDetail) {
                $deadStock[] = $obat;
                return;
            }

            $lastSaleDate = \Carbon\Carbon::parse($lastSaleDetail->created_at);
            $diffInDays = $lastSaleDate->diffInDays(\Carbon\Carbon::now());

            if ($diffInDays > 180) {
                $deadStock[] = $obat;
            } elseif ($diffInDays >= 30) {
                $slowMoving[] = $obat;
            } else {
                $fastMoving[] = $obat;
            }
        });

        return view('laporan.perputaran-stok', compact('deadStock', 'slowMoving', 'fastMoving'));
    }
}