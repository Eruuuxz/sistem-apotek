<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanController extends Controller
{
    
    /**
     * Dashboard Laporan Utama
     */
    public function index(Request $request)
    {
        // Periode default = bulan ini
        $periode = $request->input('periode', now()->format('Y-m'));
        $tanggalPeriode = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
        $tahun = $tanggalPeriode->year;
        $bulan = $tanggalPeriode->month;

        // --- Data Penjualan ---
        // PERBAIKAN 1: Menggunakan relasi 'kasir' bukan 'user'
        $penjualan = Penjualan::with(['details.obat', 'pelanggan', 'kasir']) 
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        // Data Ringkasan Harian (untuk tabel utama)
        $penjualanHarian = $penjualan->groupBy(fn($p) => $p->tanggal->format('Y-m-d'))
            ->map(fn($rows) => [
                'jumlah_transaksi' => $rows->count(),
                'total' => $rows->sum('total'),
                'total_qty' => $rows->flatMap->details->sum('qty'),
            ])->sortBy(function ($value, $key) {
                return $key;
            });

        // --- TAMBAHAN: Data Detail Harian untuk Modal ---
        $detailPenjualanHarian = $penjualan->groupBy(fn($p) => $p->tanggal->format('Y-m-d'));

        $totalPenjualan = $penjualan->sum('total');

        // --- Data Perbandingan Penjualan Bulan Lalu ---
        $periodeBulanLalu = $tanggalPeriode->copy()->subMonth();
        $penjualanBulanLalu = Penjualan::whereYear('tanggal', $periodeBulanLalu->year)
            ->whereMonth('tanggal', $periodeBulanLalu->month)
            ->sum('total');
        
        $persentasePerubahan = 0;
        if ($penjualanBulanLalu > 0) {
            $persentasePerubahan = (($totalPenjualan - $penjualanBulanLalu) / $penjualanBulanLalu) * 100;
        } elseif ($totalPenjualan > 0) {
            $persentasePerubahan = 100;
        }

        // --- Data Profit ---
        $biayaOperasional = BiayaOperasional::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
        $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->hpp);
        $totalBiayaOperasional = $biayaOperasional->sum('jumlah');
        $labaKotor = $totalPenjualan - $totalModal;
        $labaBersih = $labaKotor - $totalBiayaOperasional;

        // --- Data Pergerakan Stok ---
        $pembelian = Pembelian::with('detail.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
        $pergerakanStok = collect();
        foreach ($penjualan as $pj) {
            foreach ($pj->details as $detail) {
                $pergerakanStok->push(['tanggal' => $pj->tanggal, 'obat_nama' => $detail->obat->nama, 'jenis' => 'Penjualan', 'qty' => -$detail->qty, 'no_referensi' => $pj->no_nota]);
            }
        }
        foreach ($pembelian as $pb) {
            foreach ($pb->detail as $detail) {
                $pergerakanStok->push(['tanggal' => $pb->tanggal, 'obat_nama' => $detail->obat->nama, 'jenis' => 'Pembelian', 'qty' => $detail->jumlah, 'no_referensi' => $pb->no_faktur]);
            }
        }
        $pergerakanStok = $pergerakanStok->sortBy('tanggal');
        
        // --- Data Customer (Lengkap) ---
        $totalPelanggan = Pelanggan::count();
        $pelangganTerbaik = Penjualan::whereNotNull('pelanggan_id')
            ->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
            ->groupBy('pelanggan_id')
            ->select('pelanggan_id', DB::raw('count(*) as total_transaksi'), DB::raw('SUM(total) as total_belanja'))
            ->with('pelanggan')
            ->orderBy('total_belanja', 'desc')
            ->limit(10)
            ->get();
        $pelangganPeriodeIni = $penjualan->where('pelanggan_id', '!=', null)->pluck('pelanggan_id')->unique();
        $pelangganBaru = 0;
        if ($pelangganPeriodeIni->isNotEmpty()) {
            $pelangganBaru = Pelanggan::whereIn('id', $pelangganPeriodeIni)->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
        }
        $pelangganBulanLalu = Penjualan::whereNotNull('pelanggan_id')->whereYear('tanggal', $periodeBulanLalu->year)->whereMonth('tanggal', $periodeBulanLalu->month)->pluck('pelanggan_id')->unique();
        $jumlahPelangganLalu = $pelangganBulanLalu->count();
        $tingkatRetensi = 0;
        if($jumlahPelangganLalu > 0) {
            $pelangganKembali = $pelangganPeriodeIni->intersect($pelangganBulanLalu)->count();
            $tingkatRetensi = ($pelangganKembali / $jumlahPelangganLalu) * 100;
        }
        $stok = Obat::whereColumn('stok', '<=', 'min_stok')->orderBy('stok', 'asc')->take(10)->get();

        // PERBAIKAN 2: Menggunakan relasi 'kasir' di sini juga untuk data modal
        $dataForJs = $detailPenjualanHarian->map(function ($rows, $tanggal) {
            return $rows->map(function ($row) {
                return [
                    'no_nota' => $row->no_nota,
                    'kasir' => $row->kasir->name ?? 'N/A',
                    'pelanggan' => $row->pelanggan->nama ?? 'Umum',
                    'waktu' => \Carbon\Carbon::parse($row->tanggal)->format('H:i:s'),
                    'total' => $row->total,
                    'items' => $row->details->map(fn($d) => $d->obat->nama . ' (' . $d->qty . ')')->implode(', '),
                ];
            });
        });

        return view('admin.laporan.index', compact(
            'periode', 'penjualanHarian', 'totalPenjualan', 'penjualanBulanLalu',
            'persentasePerubahan', 'totalModal', 'totalBiayaOperasional', 'labaKotor',
            'labaBersih', 'pergerakanStok', 'stok', 'totalPelanggan', 'pelangganTerbaik',
            'pelangganBaru', 'tingkatRetensi', 'detailPenjualanHarian', 'dataForJs'
        ));
    }
    
    /**
     * Export laporan Penjualan ke PDF/Excel
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
}