<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\BiayaOperasional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Mengambil dan menghitung semua data laporan untuk dashboard berdasarkan periode.
     *
     * @param string $periode Format Y-m
     * @return array
     */
    public function getDashboardReportData(string $periode): array
    {
        $tanggalPeriode = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
        $tahun = $tanggalPeriode->year;
        $bulan = $tanggalPeriode->month;
        $periodeBulanLalu = $tanggalPeriode->copy()->subMonth();

        // 1. Data Penjualan
        $penjualan = $this->getPenjualanData($tahun, $bulan);
        $totalPenjualan = $penjualan->sum('total');
        $penjualanHarian = $this->groupPenjualanHarian($penjualan);
        $detailPenjualanHarian = $penjualan->groupBy(fn($p) => $p->tanggal->format('Y-m-d'));

        // 2. Perbandingan Bulan Lalu
        $penjualanBulanLalu = $this->getTotalPenjualan($periodeBulanLalu->year, $periodeBulanLalu->month);
        $persentasePerubahan = $this->calculatePercentageChange($totalPenjualan, $penjualanBulanLalu);

        // 3. Data Profit
        $biayaOperasional = $this->getBiayaOperasionalData($tahun, $bulan);
        $profit = $this->calculateProfitMetrics($penjualan, $biayaOperasional, $totalPenjualan);

        // 4. Pergerakan Stok
        $pembelian = $this->getPembelianData($tahun, $bulan);
        $pergerakanStok = $this->calculateStockMovement($penjualan, $pembelian);
        $stokMinim = $this->getObatStokMinim();

        // 5. Data Customer
        $customerMetrics = $this->calculateCustomerMetrics($penjualan, $tahun, $bulan, $periodeBulanLalu);

        // 6. Data untuk Modal JS
        $dataForJs = $this->prepareDataForModalJs($detailPenjualanHarian);


        return array_merge(
            compact('periode', 'penjualanHarian', 'totalPenjualan', 'penjualanBulanLalu', 'persentasePerubahan', 'pergerakanStok', 'stokMinim', 'detailPenjualanHarian', 'dataForJs'),
            $profit,
            $customerMetrics
        );
    }

    /**
     * Mengambil data penjualan lengkap (untuk detail laporan).
     *
     * @param int $tahun
     * @param int $bulan
     * @return \Illuminate\Support\Collection
     */
    public function getPenjualanData(int $tahun, int $bulan): Collection
    {
        return Penjualan::with(['details.obat', 'pelanggan', 'kasir']) 
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
    }
    
    /**
     * Mengambil total penjualan (untuk perbandingan).
     *
     * @param int $tahun
     * @param int $bulan
     * @return float
     */
    public function getTotalPenjualan(int $tahun, int $bulan): float
    {
        return Penjualan::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('total');
    }

    // --- Private Calculation & Query Helpers ---

    private function groupPenjualanHarian(Collection $penjualan): Collection
    {
        return $penjualan->groupBy(fn($p) => $p->tanggal->format('Y-m-d'))
            ->map(fn($rows) => [
                'jumlah_transaksi' => $rows->count(),
                'total' => $rows->sum('total'),
                'total_qty' => $rows->flatMap->details->sum('qty'),
            ])->sortBy(fn ($value, $key) => $key);
    }

    private function calculatePercentageChange(float $currentTotal, float $previousTotal): float
    {
        if ($previousTotal > 0) {
            return (($currentTotal - $previousTotal) / $previousTotal) * 100;
        } elseif ($currentTotal > 0) {
            return 100;
        }
        return 0;
    }

    private function getBiayaOperasionalData(int $tahun, int $bulan): Collection
    {
        return BiayaOperasional::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
    }

    private function calculateProfitMetrics(Collection $penjualan, Collection $biayaOperasional, float $totalPenjualan): array
    {
        $totalModal = $penjualan->flatMap->details->sum(fn($d) => $d->qty * $d->hpp);
        $totalBiayaOperasional = $biayaOperasional->sum('jumlah');
        $labaKotor = $totalPenjualan - $totalModal;
        $labaBersih = $labaKotor - $totalBiayaOperasional;
        
        return compact('totalModal', 'totalBiayaOperasional', 'labaKotor', 'labaBersih');
    }

    private function getPembelianData(int $tahun, int $bulan): Collection
    {
        return Pembelian::with('detail.obat')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
    }
    
    private function calculateStockMovement(Collection $penjualan, Collection $pembelian): Collection
    {
        $pergerakanStok = collect();
        
        foreach ($penjualan as $pj) {
            foreach ($pj->details as $detail) {
                // Pastikan detail memiliki relasi obat yang valid sebelum diakses
                if ($detail->obat) {
                    $pergerakanStok->push(['tanggal' => $pj->tanggal, 'obat_nama' => $detail->obat->nama, 'jenis' => 'Penjualan', 'qty' => -$detail->qty, 'no_referensi' => $pj->no_nota]);
                }
            }
        }
        
        foreach ($pembelian as $pb) {
            foreach ($pb->detail as $detail) {
                // Pastikan detail memiliki relasi obat yang valid sebelum diakses
                if ($detail->obat) {
                    $pergerakanStok->push(['tanggal' => $pb->tanggal, 'obat_nama' => $detail->obat->nama, 'jenis' => 'Pembelian', 'qty' => $detail->jumlah, 'no_referensi' => $pb->no_faktur]);
                }
            }
        }
        
        return $pergerakanStok->sortBy('tanggal');
    }

    private function getObatStokMinim(): Collection
    {
        return Obat::whereColumn('stok', '<=', 'min_stok')->orderBy('stok', 'asc')->take(10)->get();
    }

    private function calculateCustomerMetrics(Collection $penjualan, int $tahun, int $bulan, Carbon $periodeBulanLalu): array
    {
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

        $pelangganBulanLalu = Penjualan::whereNotNull('pelanggan_id')
            ->whereYear('tanggal', $periodeBulanLalu->year)
            ->whereMonth('tanggal', $periodeBulanLalu->month)
            ->pluck('pelanggan_id')
            ->unique();
            
        $jumlahPelangganLalu = $pelangganBulanLalu->count();
        $tingkatRetensi = 0;
        
        if($jumlahPelangganLalu > 0) {
            $pelangganKembali = $pelangganPeriodeIni->intersect($pelangganBulanLalu)->count();
            $tingkatRetensi = ($pelangganKembali / $jumlahPelangganLalu) * 100;
        }
        
        return compact('totalPelanggan', 'pelangganTerbaik', 'pelangganBaru', 'tingkatRetensi');
    }
    
    private function prepareDataForModalJs(Collection $detailPenjualanHarian): Collection
    {
        return $detailPenjualanHarian->map(function ($rows, $tanggal) {
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
    }
}