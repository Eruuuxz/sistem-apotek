<?php

namespace App\Services;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Supplier;
use App\Models\SuratPesanan;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembelianService
{
    /**
     * Membuat draft pembelian baru (baik manual atau dari SP).
     *
     * @param array $data Data request
     * @param string|null $noFaktur Otomatis jika null
     * @return Pembelian
     * @throws \Exception
     */
    public function createDraft(array $data, ?string $noFaktur): Pembelian
    {
        return DB::transaction(function () use ($data, $noFaktur) {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            $totals = $this->calculateTotals($data['items'], $data['diskon'] ?? 0, $data['diskon_type'] ?? 'nominal');

            // 1. Buat Header Pembelian
            $pembelian = Pembelian::create([
                'no_faktur' => $noFaktur ?? $this->generateNoFaktur(),
                'no_faktur_pbf' => $data['no_faktur_pbf'] ?? null,
                'tanggal' => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'surat_pesanan_id' => $data['surat_pesanan_id'] ?? null,
                'cabang_id' => $cabangId,
                'total' => $totals['finalTotal'],
                'diskon' => $data['diskon'] ?? 0,
                'diskon_type' => $data['diskon_type'] ?? 'nominal',
                'ppn_amount' => $totals['totalPpn'],
                'status' => 'draft',
            ]);

            // 2. Buat Detail Pembelian
            $this->processDetails($pembelian, $data['items']);
            
            // 3. Update status SP jika ada
            if ($pembelian->surat_pesanan_id) {
                $pembelian->suratPesanan->update(['status' => 'selesai']);
            }

            return $pembelian;
        });
    }

    /**
     * Memproses Surat Pesanan menjadi draft pembelian.
     *
     * @param SuratPesanan $suratPesanan
     * @return Pembelian
     * @throws \Exception
     */
    public function processFromSuratPesanan(SuratPesanan $suratPesanan): Pembelian
    {
        if ($suratPesanan->pembelian()->exists()) {
            throw new \Exception('Surat Pesanan ini sudah pernah diproses.');
        }

        // Konversi detail SP menjadi struktur item pembelian
        $itemsData = $suratPesanan->details->map(function ($spDetail) use ($suratPesanan) {
            $obat = $suratPesanan->sp_mode == 'dropdown' && $spDetail->obat_id ? Obat::find($spDetail->obat_id) : null;
            if (!$obat) return null; // Abaikan item manual atau yang tidak ada obatnya

            return [
                'obat_id' => $obat->id,
                'jumlah' => $spDetail->qty_pesan,
                'harga_beli' => $spDetail->harga_satuan ?? $obat->harga_dasar, // Gunakan harga satuan SP jika ada, atau harga dasar obat
            ];
        })->filter()->toArray();

        $data = [
            'tanggal' => now(),
            'supplier_id' => $suratPesanan->supplier_id,
            'surat_pesanan_id' => $suratPesanan->id,
            'diskon' => 0,
            'diskon_type' => 'nominal',
            'items' => $itemsData,
        ];

        return $this->createDraft($data, $this->generateNoFaktur());
    }

    /**
     * Memperbarui detail draft pembelian dan memfinalisasi transaksi, termasuk update stok.
     *
     * @param Pembelian $pembelian
     * @param array $data
     * @return Pembelian
     * @throws \Exception
     */
    public function finalizePembelian(Pembelian $pembelian, array $data): Pembelian
    {
        if ($pembelian->status === 'final') {
            throw new \Exception('Pembelian ini sudah difinalisasi.');
        }

        return DB::transaction(function () use ($pembelian, $data) {
            $totalPembelian = 0;
            $totalPpn = 0;

            foreach ($data['items'] as $itemData) {
                $detail = PembelianDetail::find($itemData['pembelian_detail_id']);
                $obat = Obat::find($detail->obat_id);

                if (!$detail || !$obat) continue;

                $jumlahLama = $detail->jumlah;
                $jumlahBaru = (int)$itemData['jumlah'];
                $deltaQty = $jumlahBaru - $jumlahLama; // Perubahan kuantitas

                $hargaBeli = (float)$itemData['harga_beli'];
                
                // Hitung ulang PPN dan Harga Bersih per unit
                $ppnPerUnit = $this->calculatePpnPerUnit($obat, $hargaBeli);
                $hargaDenganPpnPerUnit = $hargaBeli + ($obat->ppn_included ? 0 : $ppnPerUnit);
                $totalPembelian += $hargaDenganPpnPerUnit * $jumlahBaru;
                $totalPpn += $ppnPerUnit * $jumlahBaru;

                // Update detail pembelian
                $detail->update([
                    'jumlah' => $jumlahBaru,
                    'harga_beli' => $hargaBeli,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'ppn_amount' => $ppnPerUnit * $jumlahBaru,
                ]);

                // Update Stok & Batch
                $this->updateStockAndBatches($pembelian, $detail, $jumlahBaru, $jumlahLama, $hargaBeli, $itemData['no_batch'], $itemData['expired_date']);
            }

            // Hitung total akhir dan diskon
            $diskonAmount = $this->calculateDiskonAmount($totalPembelian, $data['diskon'] ?? 0, $data['diskon_type'] ?? 'nominal');
            $finalTotal = max($totalPembelian - $diskonAmount, 0);

            // Update header pembelian
            $pembelian->update([
                'no_faktur_pbf' => $data['no_faktur_pbf'],
                'tanggal' => $data['tanggal'],
                'total' => $finalTotal,
                'diskon' => $data['diskon'] ?? 0,
                'diskon_type' => $data['diskon_type'] ?? 'nominal',
                'ppn_amount' => $totalPpn,
                'status' => 'final',
            ]);

            return $pembelian;
        });
    }

    /**
     * Menghapus pembelian dan membatalkan stok jika sudah final.
     *
     * @param Pembelian $pembelian
     * @return void
     * @throws \Exception
     */
    public function destroyPembelian(Pembelian $pembelian): void
    {
        DB::transaction(function () use ($pembelian) {
            if ($pembelian->status === 'final') {
                $this->rollbackStockAndBatches($pembelian);
            }
            $pembelian->delete();
        });
    }

    /**
     * Mengambil data obat yang tersedia (stok > 0 dan belum kadaluarsa) untuk supplier tertentu.
     *
     * @param int $supplierId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableObatBySupplier(int $supplierId): \Illuminate\Database\Eloquent\Collection
    {
        return Obat::where('supplier_id', $supplierId)
            ->where('stok', '>', 0)
            ->where(fn ($query) => $query->whereNull('expired_date')->orWhere('expired_date', '>', now()))
            ->get(['id', 'kode', 'nama', 'stok', 'harga_dasar', 'ppn_rate', 'ppn_included', 'sediaan', 'satuan_terkecil', 'kemasan_besar', 'rasio_konversi']);
    }

    // --- Private Calculation & DB Helpers ---

    /**
     * Menghitung total transaksi dari daftar item.
     *
     * @param array $items
     * @param float $diskon
     * @param string $diskonType
     * @return array
     */
    private function calculateTotals(array $items, float $diskon, string $diskonType): array
    {
        $totalPembelian = 0; // Total sebelum diskon
        $totalPpn = 0;
        $obatIds = collect($items)->pluck('obat_id')->unique();
        $obats = Obat::whereIn('id', $obatIds)->get()->keyBy('id');

        foreach ($items as $itemData) {
            $obat = $obats->get($itemData['obat_id']);
            if (!$obat) continue;

            $hargaBeli = (float)$itemData['harga_beli'];
            $jumlah = (int)$itemData['jumlah'];

            $ppnPerUnit = $this->calculatePpnPerUnit($obat, $hargaBeli);
            
            // Total pembelian = (Harga Beli + PPN jika PPN tidak termasuk) * Jumlah
            $hargaDenganPpnPerUnit = $hargaBeli + ($obat->ppn_included ? 0 : $ppnPerUnit);
            
            $totalPembelian += $hargaDenganPpnPerUnit * $jumlah;
            $totalPpn += $ppnPerUnit * $jumlah;
        }

        $diskonAmount = $this->calculateDiskonAmount($totalPembelian, $diskon, $diskonType);
        $finalTotal = max($totalPembelian - $diskonAmount, 0);

        return compact('totalPembelian', 'totalPpn', 'diskonAmount', 'finalTotal');
    }

    /**
     * Menghitung PPN per unit.
     *
     * @param Obat $obat
     * @param float $hargaBeli
     * @return float
     */
    private function calculatePpnPerUnit(Obat $obat, float $hargaBeli): float
    {
        if (($obat->ppn_rate ?? 0) <= 0) return 0;
        
        if ($obat->ppn_included) {
            // PPN Included: Harga Jual (termasuk PPN) - Harga Bersih
            $hargaBersihPerUnit = $hargaBeli / (1 + $obat->ppn_rate / 100);
            return $hargaBeli - $hargaBersihPerUnit;
        } else {
            // PPN Exclusive: Harga Beli * PPN Rate
            return $hargaBeli * ($obat->ppn_rate / 100);
        }
    }

    /**
     * Menghitung jumlah diskon.
     *
     * @param float $total
     * @param float $diskonValue
     * @param string $diskonType
     * @return float
     */
    private function calculateDiskonAmount(float $total, float $diskonValue, string $diskonType): float
    {
        if ($diskonType === 'persen') {
            return $total * ($diskonValue / 100);
        }
        return $diskonValue;
    }

    /**
     * Membuat detail pembelian dari data item.
     *
     * @param Pembelian $pembelian
     * @param array $items
     * @return void
     */
    private function processDetails(Pembelian $pembelian, array $items): void
    {
        $obatIds = collect($items)->pluck('obat_id')->unique();
        $obats = Obat::whereIn('id', $obatIds)->get()->keyBy('id');

        foreach ($items as $itemData) {
            $obat = $obats->get($itemData['obat_id']);
            if (!$obat) continue;
            
            $hargaBeli = (float)$itemData['harga_beli'];
            $jumlah = (int)$itemData['jumlah'];
            $ppnPerUnit = $this->calculatePpnPerUnit($obat, $hargaBeli);

            $pembelian->detail()->create([
                'obat_id' => $itemData['obat_id'],
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
                'ppn_amount' => $ppnPerUnit * $jumlah,
            ]);
        }
    }

    /**
     * Update stok Obat dan Batch saat finalisasi pembelian.
     *
     * @param Pembelian $pembelian
     * @param PembelianDetail $detail
     * @param int $jumlahBaru
     * @param int $jumlahLama
     * @param float $hargaBeli
     * @param string $noBatch
     * @param string $expiredDate
     * @return void
     */
    private function updateStockAndBatches(Pembelian $pembelian, PembelianDetail $detail, int $jumlahBaru, int $jumlahLama, float $hargaBeli, string $noBatch, string $expiredDate): void
    {
        $obat = $detail->obat;
        $deltaQty = $jumlahBaru - $jumlahLama;

        // 1. Update Batch (Hapus batch lama jika ada, lalu buat/update batch baru)
        // Dalam konteks ini, kita asumsikan detail pembelian yang di-update
        // hanya mengacu pada satu batch. Karena ini proses finalisasi
        
        $batch = BatchObat::firstOrNew([
            'obat_id' => $obat->id,
            'no_batch' => $noBatch,
            'expired_date' => $expiredDate,
            'supplier_id' => $pembelian->supplier_id,
        ]);

        if ($batch->exists) {
            // Jika batch sudah ada, tambahkan delta (perubahan kuantitas)
            $batch->stok_awal += $deltaQty;
            $batch->stok_saat_ini += $deltaQty;
            $batch->harga_beli_per_unit = $hargaBeli; // Update harga beli terbaru
            $batch->save();
        } else {
            // Batch baru
            $batch->fill([
                'stok_awal' => $jumlahBaru,
                'stok_saat_ini' => $jumlahBaru,
                'harga_beli_per_unit' => $hargaBeli,
            ])->save();
        }

        // 2. Update Stok Obat Total
        $obat->increment('stok', $deltaQty);
    }
    
    /**
     * Membatalkan stok Obat dan Batch saat menghapus pembelian final.
     *
     * @param Pembelian $pembelian
     * @return void
     */
    private function rollbackStockAndBatches(Pembelian $pembelian): void
    {
        foreach ($pembelian->detail as $detail) {
            $obat = $detail->obat;
            if (!$obat || !$detail->no_batch) continue;
            
            // Cari batch yang sesuai dengan detail pembelian
            $batch = BatchObat::where('obat_id', $detail->obat_id)
                            ->where('no_batch', $detail->no_batch)
                            ->where('expired_date', $detail->expired_date)
                            ->first();
                            
            if ($batch) {
                // Kurangi stok batch dan hapus jika stok <= 0
                $batch->decrement('stok_saat_ini', $detail->jumlah);
                if ($batch->stok_saat_ini <= 0) {
                    $batch->delete();
                }
            }
            // Kurangi stok total obat
            $obat->decrement('stok', $detail->jumlah);
        }
    }

    /**
     * Membuat nomor faktur pembelian baru.
     *
     * @return string
     */
    private function generateNoFaktur(): string
    {
        return 'FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
    }
}