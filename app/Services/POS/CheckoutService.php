<?php

namespace App\Services\POS;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\CashierShift;
use App\Models\Pelanggan; // Tambahkan Import Model Pelanggan
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function processCheckout(array $validatedData): Penjualan
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            throw new \Exception('Keranjang kosong. Tidak dapat melakukan checkout.');
        }
        
        $activeShift = CashierShift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$activeShift) {
            throw new \Exception('Sesi kasir tidak aktif. Silakan mulai sesi baru.');
        }

        $totals = $this->cartService->calculateTotals($cart);
        $finalTotal = $totals['totalAkhir'];
        $bayar = (float)$validatedData['bayar'];
        
        if ($bayar < $finalTotal) {
            throw new \Exception('Pembayaran kurang dari total belanja.');
        }
        
        // Ambil data obat dan cek psikotropika
        $obatIdsInCart = collect($cart)->pluck('id')->unique()->toArray();
        $obats = Obat::with('batches')->whereIn('id', $obatIdsInCart)->get()->keyBy('id');
        $hasPsikotropika = $obats->contains(fn($obat) => $obat->is_psikotropika);
        
        if ($hasPsikotropika && empty($validatedData['no_ktp'])) {
             throw new \Exception('Nomor KTP wajib diisi untuk pembelian psikotropika.');
        }

        return DB::transaction(function () use ($cart, $totals, $validatedData, $hasPsikotropika, $activeShift, $obats, $finalTotal) {
            
            // --- PERBAIKAN: LOGIKA SIMPAN PELANGGAN BARU ---
            $pelangganId = $validatedData['pelanggan_id'] ?? null;

            // Jika ID kosong TAPI ada nama pelanggan (input manual), buat data baru/cari yg ada
            if (empty($pelangganId) && !empty($validatedData['nama_pelanggan'])) {
                // Gunakan firstOrCreate agar tidak duplikat jika nama sama persis sudah ada
                $pelangganBaru = Pelanggan::firstOrCreate(
                    ['nama' => $validatedData['nama_pelanggan']], 
                    [
                        'alamat' => $validatedData['alamat_pelanggan'] ?? '-',
                        'telepon' => $validatedData['telepon_pelanggan'] ?? '-',
                        'tipe' => 'umum', // Set tipe default
                        'no_ktp' => $validatedData['no_ktp'] ?? null,
                    ]
                );
                $pelangganId = $pelangganBaru->id;
            }
            // -----------------------------------------------

            // 1. Buat Header Penjualan
            $penjualan = Penjualan::create([
                'no_nota' => $this->generateNoNota(),
                'tanggal' => now(),
                'user_id' => Auth::id(),
                'cabang_id' => Auth::user()->cabang_id ?? Cabang::first()->id,
                'total' => $finalTotal,
                'bayar' => $validatedData['bayar'],
                'kembalian' => $validatedData['bayar'] - $finalTotal,
                'nama_pelanggan' => $validatedData['nama_pelanggan'],
                'alamat_pelanggan' => $validatedData['alamat_pelanggan'] ?? null,
                'telepon_pelanggan' => $validatedData['telepon_pelanggan'] ?? null,
                'pelanggan_id' => $pelangganId, // Gunakan ID yang sudah diproses di atas
                'diskon_type' => $totals['diskonType'],
                'diskon_value' => $totals['diskonValue'],
                'diskon_amount' => $totals['diskonAmount'],
                'ppn_amount' => $totals['totalPpn'],
                'cashier_shift_id' => $activeShift->id,
            ]);

            // 2. Buat Detail Penjualan & Update Stok/Batch
            $this->processDetails($penjualan, $cart, $obats, $hasPsikotropika, $validatedData['no_ktp'] ?? null);
            
            // 3. Bersihkan keranjang
            $this->cartService->clearCart();

            return $penjualan;
        });
    }

    // ... sisa kode (processDetails, generateNoNota) biarkan tetap sama ...
    
    protected function processDetails(Penjualan $penjualan, array $cart, Collection $obats, bool $hasPsikotropika, ?string $noKtp): void
    {
        foreach ($cart as $item) {
            $obat = $obats->get($item['id']);
            if (!$obat) throw new \Exception("Obat ID {$item['id']} tidak valid saat proses detail.");
            
            $totalHPP = collect($item['batches_used'])->sum(fn($b) => $b['harga_beli_per_unit'] * $b['qty_from_batch']);
            $totalQtyUsed = collect($item['batches_used'])->sum('qty_from_batch');
            $hpp = $totalQtyUsed > 0 ? $totalHPP / $totalQtyUsed : $obat->harga_dasar;

            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'obat_id' => $obat->id,
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'hpp' => $hpp,
                'subtotal' => $item['qty'] * $item['harga'],
                'no_ktp' => $hasPsikotropika ? $noKtp : null,
            ]);

            $obat->decrement('stok', $item['qty']);

            foreach ($item['batches_used'] as $batchDetail) {
                $batch = $obat->batches->find($batchDetail['batch_id']);
                if ($batch) {
                    $batch->decrement('stok_saat_ini', $batchDetail['qty_from_batch']);
                }
            }
        }
    }

    protected function generateNoNota(): string
    {
        $countToday = Penjualan::whereDate('tanggal', date('Y-m-d'))->count();
        return 'PJ-' . date('Ymd') . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
    }
}