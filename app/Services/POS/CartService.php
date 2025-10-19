<?php

namespace App\Services\POS;

use App\Models\Obat;
use App\Models\CashierShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Mengambil keranjang saat ini dari sesi, sekaligus memvalidasinya.
     *
     * @return array
     */
    public function getCart(): array
    {
        $cart = Session::get('cart', []);
        $this->validateCart($cart);
        return $cart;
    }

    /**
     * Menghitung total transaksi (subtotal bersih, PPN, diskon, total akhir).
     *
     * @param array $cart
     * @return array
     */
    public function calculateTotals(array $cart): array
    {
        $totalSubtotalBersih = 0;
        $totalPpn = 0;
        
        // Ambil ID obat yang ada di keranjang untuk query yang efisien
        $obatIdsInCart = collect($cart)->pluck('id')->unique()->toArray();
        $obats = Obat::whereIn('id', $obatIdsInCart)->get()->keyBy('id');

        foreach ($cart as $item) {
            $obat = $obats->get($item['id']);
            if (!$obat) continue;

            $ppnRate = $obat->ppn_rate ?? 0;
            $hargaJual = $obat->harga_jual;

            // Hitung harga jual bersih per unit
            $hargaJualBersihPerUnit = $hargaJual / (1 + $ppnRate / 100);
            $ppnAmountPerUnit = $hargaJual - $hargaJualBersihPerUnit;

            $totalSubtotalBersih += $hargaJualBersihPerUnit * $item['qty'];
            $totalPpn += $ppnAmountPerUnit * $item['qty'];
        }

        $diskonType = Session::get('diskon_type', 'nominal');
        $diskonValue = Session::get('diskon_value', 0);
        $totalSebelumDiskon = $totalSubtotalBersih + $totalPpn;
        
        $diskonAmount = 0;
        if ($diskonType === 'persen') {
            $diskonAmount = $totalSebelumDiskon * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $totalAkhir = max($totalSebelumDiskon - $diskonAmount, 0);

        return [
            'totalSubtotalBersih' => $totalSubtotalBersih,
            'totalPpn' => $totalPpn,
            'totalSebelumDiskon' => $totalSebelumDiskon,
            'diskonType' => $diskonType,
            'diskonValue' => $diskonValue,
            'diskonAmount' => $diskonAmount,
            'totalAkhir' => $totalAkhir,
        ];
    }

    /**
     * Menambahkan item ke keranjang.
     *
     * @param Obat $obat
     * @param int $qtyToAdd
     * @return void
     * @throws \Exception
     */
    public function addItem(Obat $obat, int $qtyToAdd = 1): void
    {
        if ($obat->stok <= 0) {
            throw new \Exception('Stok ' . $obat->nama . ' kosong.');
        }

        if ($obat->expired_date && Carbon::parse($obat->expired_date)->isPast()) {
            throw new \Exception('Obat ' . $obat->nama . ' sudah kadaluarsa.');
        }

        $cart = $this->getCart(); // Mengambil cart yang sudah tervalidasi
        $kode = $obat->kode;
        $currentQty = $cart[$kode]['qty'] ?? 0;
        $newQty = $currentQty + $qtyToAdd;

        if ($newQty > $obat->stok) {
            throw new \Exception('Kuantitas melebihi stok yang tersedia (Maks: ' . $obat->stok . ').');
        }

        $cart[$kode] = [
            'id' => $obat->id,
            'kode' => $obat->kode,
            'nama' => $obat->nama,
            'kategori' => $obat->kategori,
            'harga' => $obat->harga_jual,
            'ppn_rate' => $obat->ppn_rate ?? 0,
            'ppn_included' => $obat->ppn_included ?? false,
            'qty' => $newQty,
            'stok' => $obat->stok,
            'is_psikotropika' => $obat->is_psikotropika,
            'batches_used' => $this->getBatchesForQty($obat, $newQty),
        ];

        Session::put('cart', $cart);
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     *
     * @param Obat $obat
     * @param int $newQty
     * @return void
     * @throws \Exception
     */
    public function updateItemQty(Obat $obat, int $newQty): void
    {
        $cart = $this->getCart();
        $kode = $obat->kode;

        if (!isset($cart[$kode])) {
            throw new \Exception('Obat tidak ada di keranjang.');
        }

        if ($newQty > $obat->stok) {
            throw new \Exception('Kuantitas melebihi stok. Stok maksimal: ' . $obat->stok);
        }

        if ($newQty === 0) {
            $this->removeItem($kode);
            return;
        }

        $cart[$kode]['qty'] = $newQty;
        $cart[$kode]['batches_used'] = $this->getBatchesForQty($obat, $newQty);
        Session::put('cart', $cart);
    }

    /**
     * Menghapus item dari keranjang.
     *
     * @param string $kode
     * @return void
     */
    public function removeItem(string $kode): void
    {
        $cart = $this->getCart();
        unset($cart[$kode]);
        Session::put('cart', $cart);
    }

    /**
     * Mengatur diskon transaksi.
     *
     * @param string $type
     * @param float $value
     * @return void
     */
    public function setDiskon(string $type, float $value): void
    {
        Session::put(['diskon_type' => $type, 'diskon_value' => $value]);
    }
    
    /**
     * Mengosongkan keranjang dan diskon.
     *
     * @return void
     */
    public function clearCart(): void
    {
        Session::forget(['cart', 'diskon_type', 'diskon_value']);
    }

    /**
     * Menentukan batch yang akan digunakan berdasarkan prinsip FEFO.
     *
     * @param Obat $obat
     * @param int $qtyNeeded
     * @return array
     */
    private function getBatchesForQty(Obat $obat, int $qtyNeeded): array
    {
        // Pastikan relasi batches sudah di-load, jika tidak, load ulang.
        if (!$obat->relationLoaded('batches')) {
            $obat->load('batches');
        }
        
        // FEFO: Filter stok > 0, belum kadaluarsa, dan urutkan berdasarkan expired date terdekat
        $batches = $obat->batches->where('stok_saat_ini', '>', 0)
                                 ->where('expired_date', '>', now())
                                 ->sortBy('expired_date');
                                 
        $tempBatchesUsed = [];
        $remainingQty = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;
            $qtyFromThis = min($remainingQty, $batch->stok_saat_ini);
            if ($qtyFromThis > 0) {
                $tempBatchesUsed[] = [
                    'batch_id' => $batch->id,
                    'no_batch' => $batch->no_batch,
                    'expired_date' => $batch->expired_date,
                    'qty_from_batch' => $qtyFromThis,
                    'harga_beli_per_unit' => $batch->harga_beli_per_unit,
                ];
                $remainingQty -= $qtyFromThis;
            }
        }
        return $tempBatchesUsed;
    }
    
    /**
     * Memvalidasi ulang stok di keranjang, menghapus item yang tidak valid, dan menyesuaikan kuantitas.
     *
     * @param array $cart
     * @return void
     */
    private function validateCart(array &$cart): void
    {
        if (empty($cart)) return;

        $obatCodesInCart = array_keys($cart);
        // Load obat beserta batch-nya secara efisien
        $obats = Obat::with('batches')->whereIn('kode', $obatCodesInCart)->get()->keyBy('kode');
        $sessionUpdated = false;
        
        foreach ($cart as $kode => &$item) {
            $obat = $obats->get($kode);

            // 1. Validasi Obat (Tidak ada, Stok kosong, Kadaluarsa)
            if (!$obat || $obat->stok <= 0 || ($obat->expired_date && now()->gt($obat->expired_date))) {
                unset($cart[$kode]);
                $sessionUpdated = true;
                continue;
            }

            // 2. Koreksi Kuantitas dan Data Harga Terbaru
            $item['qty'] = min($item['qty'], $obat->stok);
            $item['harga'] = $obat->harga_jual;
            $item['kategori'] = $obat->kategori;
            $item['is_psikotropika'] = $obat->is_psikotropika;
            $item['ppn_rate'] = $obat->ppn_rate ?? 0;
            $item['ppn_included'] = $obat->ppn_included ?? false;
            $item['stok'] = $obat->stok;
            $item['batches_used'] = $this->getBatchesForQty($obat, $item['qty']);
            $sessionUpdated = true;
            
            if ($item['qty'] === 0) {
                unset($cart[$kode]);
            }
        }
        
        if ($sessionUpdated) {
            Session::put('cart', $cart);
        }
    }
}