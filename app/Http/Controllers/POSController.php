<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\CashierShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class POSController extends Controller
{
    /**
     * Menampilkan halaman POS.
     * Mengatur apakah menampilkan form "Mulai Shift" atau POS itu sendiri.
     */
    public function index()
    {
        // Cari shift kasir yang sedang aktif
        $activeShift = CashierShift::with('shift')
                                   ->where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        // Jika tidak ada shift yang aktif, tampilkan form "Mulai Shift"
        if (!$activeShift) {
            $shifts = Shift::whereIn('name', ['Pagi', 'Sore'])->get();
            return view('kasir.pos', compact('activeShift', 'shifts'));
        }

        // Jika ada shift yang aktif, lanjutkan dengan logika POS normal
        $obat = Obat::where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_date')
                    ->orWhere('expired_date', '>', now());
            })
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok', 'is_psikotropika', 'ppn_rate', 'ppn_included']);

        $cart = session('cart', []);
        $this->validateCart($cart);
        
        $totalSubtotalBersih = 0;
        $totalPpn = 0;
        foreach ($cart as $item) {
            // Menggunakan logika baru: asumsikan harga sudah termasuk PPN, ekstrak PPN dari harga jual
            $ppnRate = $item['ppn_rate'] ?? 0;
            $hargaJualBersihPerUnit = $item['harga'] / (1 + $ppnRate / 100);
            $ppnAmountPerItem = $item['harga'] - $hargaJualBersihPerUnit;

            $totalSubtotalBersih += $hargaJualBersihPerUnit * $item['qty'];
            $totalPpn += $ppnAmountPerItem * $item['qty'];
        }
        
        $diskonType = session('diskon_type', 'nominal');
        $diskonValue = session('diskon_value', 0);

        $diskonAmount = 0;
        $totalSebelumDiskon = $totalSubtotalBersih + $totalPpn;
        if ($diskonType === 'persen') {
            // Perbaikan: Diskon persentase diterapkan pada total keseluruhan
            $diskonAmount = $totalSebelumDiskon * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $totalAkhir = max($totalSebelumDiskon - $diskonAmount, 0);

        $members = Pelanggan::orderBy('nama')->get();
        $totalSales = Penjualan::where('cashier_shift_id', $activeShift->id)->sum('total');

        return view('kasir.pos', compact('obat', 'cart', 'totalSubtotalBersih', 'diskonType', 'diskonValue', 'diskonAmount', 'totalAkhir', 'members', 'activeShift', 'totalPpn'));
    }

    /**
     * Mencari obat untuk fitur autocomplete.
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q');

        $obat = Obat::where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_date')
                    ->orWhere('expired_date', '>', now());
            })
            ->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('kode', 'like', '%' . $keyword . '%');
            })
            ->orderBy('nama')
            ->limit(10)
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok', 'is_psikotropika', 'ppn_rate', 'ppn_included']);

        return response()->json($obat);
    }

    /**
     * Menambahkan item ke keranjang belanja.
     */
    public function add(Request $r)
    {
        $r->validate(['kode' => 'required']);

        $obat = Obat::where('kode', $r->kode)->first();

        if (!$obat) {
            return back()->with('error', 'Obat tidak ditemukan.');
        }

        if ($obat->stok <= 0) {
            return back()->with('error', 'Stok ' . $obat->nama . ' kosong.');
        }

        if ($obat->expired_date && Carbon::parse($obat->expired_date)->isPast()) {
            return back()->with('error', 'Obat ' . $obat->nama . ' sudah kadaluarsa dan tidak bisa ditambahkan.');
        }

        $cart = session('cart', []);
        $qtyToAdd = 1;

        if (isset($cart[$obat->kode])) {
            $qtyToAdd = $cart[$obat->kode]['qty'] + 1;
        }

        $batches = BatchObat::where('obat_id', $obat->id)
            ->where('stok_saat_ini', '>', 0)
            ->where('expired_date', '>', now())
            ->orderBy('expired_date', 'asc')
            ->get();

        $tempBatchesUsed = [];
        $totalQtyFromBatches = 0;
        $remainingQtyNeeded = $qtyToAdd;

        foreach ($batches as $batch) {
            if ($remainingQtyNeeded <= 0) break;

            $qtyFromThisBatch = min($remainingQtyNeeded, $batch->stok_saat_ini);
            if ($qtyFromThisBatch > 0) {
                $tempBatchesUsed[] = [
                    'batch_id' => $batch->id,
                    'no_batch' => $batch->no_batch,
                    'expired_date' => $batch->expired_date,
                    'qty_from_batch' => $qtyFromThisBatch,
                    'harga_beli_per_unit' => $batch->harga_beli_per_unit,
                ];
                $totalQtyFromBatches += $qtyFromThisBatch;
                $remainingQtyNeeded -= $qtyFromThisBatch;
            }
        }

        if ($totalQtyFromBatches < $qtyToAdd) {
            return back()->with('error', 'Stok ' . $obat->nama . ' tidak cukup dari batch yang tersedia. Stok saat ini: ' . $obat->stok);
        }

        // Ambil PPN rate dari objek obat
        $ppnRate = $obat->ppn_rate ?? 0;
        $ppnIncluded = $obat->ppn_included ?? false;

        $cart[$obat->kode] = [
            'id' => $obat->id,
            'kode' => $obat->kode,
            'nama' => $obat->nama,
            'kategori' => $obat->kategori,
            'harga' => $obat->harga_jual,
            'ppn_rate' => $ppnRate,
            'ppn_included' => $ppnIncluded,
            'qty' => $qtyToAdd,
            'stok' => $obat->stok,
            'is_psikotropika' => $obat->is_psikotropika,
            'batches_used' => $tempBatchesUsed,
        ];

        session(['cart' => $cart]);
        return back();
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function updateQty(Request $r)
    {
        $r->validate(['kode' => 'required', 'qty' => 'required|integer|min:0']);

        $cart = session('cart', []);
        $kode = $r->kode;
        $newQty = (int) $r->qty;

        if (!isset($cart[$kode])) {
            return back()->with('error', 'Obat tidak ada di keranjang.');
        }

        if ($newQty === 0) {
            unset($cart[$kode]);
            session(['cart' => $cart]);
            return back();
        }

        $obat = Obat::where('kode', $kode)->first();
        if (!$obat) {
            unset($cart[$kode]);
            session(['cart' => $cart]);
            return back()->with('error', 'Obat tidak ditemukan di database.');
        }

        if ($newQty > $obat->stok) {
            return back()->with('error', 'Kuantitas melebihi stok total yang tersedia. Stok maksimal: ' . $obat->stok);
        }

        $batches = BatchObat::where('obat_id', $obat->id)
            ->where('stok_saat_ini', '>', 0)
            ->where('expired_date', '>', now())
            ->orderBy('expired_date', 'asc')
            ->get();

        $tempBatchesUsed = [];
        $remainingQtyNeeded = $newQty;
        $totalQtyFromBatches = 0;

        foreach ($batches as $batch) {
            if ($remainingQtyNeeded <= 0) break;

            $qtyFromThisBatch = min($remainingQtyNeeded, $batch->stok_saat_ini);
            if ($qtyFromThisBatch > 0) {
                $tempBatchesUsed[] = [
                    'batch_id' => $batch->id,
                    'no_batch' => $batch->no_batch,
                    'expired_date' => $batch->expired_date,
                    'qty_from_batch' => $qtyFromThisBatch,
                    'harga_beli_per_unit' => $batch->harga_beli_per_unit,
                ];
                $totalQtyFromBatches += $qtyFromThisBatch;
                $remainingQtyNeeded -= $qtyFromThisBatch;
            }
        }

        if ($totalQtyFromBatches < $newQty) {
            return back()->with('error', 'Stok ' . $obat->nama . ' tidak cukup dari batch yang tersedia untuk kuantitas ini. Stok saat ini: ' . $obat->stok);
        }

        $ppnRate = $obat->ppn_rate ?? 0;
        $ppnIncluded = $obat->ppn_included ?? false;

        $cart[$kode]['qty'] = $newQty;
        $cart[$kode]['ppn_rate'] = $ppnRate;
        $cart[$kode]['ppn_included'] = $ppnIncluded;
        $cart[$kode]['batches_used'] = $tempBatchesUsed;
        session(['cart' => $cart]);

        return back();
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $r)
    {
        $r->validate(['kode' => 'required']);

        $cart = session('cart', []);
        unset($cart[$r->kode]);
        session(['cart' => $cart]);
        return back();
    }

    /**
     * Mengatur diskon untuk transaksi.
     */
    public function setDiskon(Request $r)
    {
        $r->validate([
            'diskon_type' => 'required|in:nominal,persen',
            'diskon_value' => 'required|numeric|min:0'
        ]);

        session([
            'diskon_type' => $r->diskon_type,
            'diskon_value' => $r->diskon_value,
        ]);

        return back()->with('success', 'Diskon berhasil diterapkan');
    }

    /**
     * Memproses checkout dan menyimpan transaksi.
     */
    public function checkout(Request $r)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        // --- Perbaikan: Eager Loading untuk menghindari N+1 query ---
        $obatIdsInCart = collect($cart)->pluck('id')->unique()->toArray();
        // Ambil semua obat dan batch terkait dalam satu query
        $obats = Obat::with('batches')->whereIn('id', $obatIdsInCart)->get()->keyBy('id');
        // --- Akhir Perbaikan ---
        
        $totalSubtotalBersih = 0;
        $totalPpn = 0;
        $hasPsikotropika = false;
        
        foreach ($cart as $item) {
            $obat = $obats->get($item['id']);
            if (!$obat) {
                return back()->with('error', 'Obat di keranjang tidak ditemukan di database. Keranjang dikosongkan.');
            }
            if ($obat->is_psikotropika) {
                $hasPsikotropika = true;
            }
            
            // Logika baru: asumsikan harga sudah termasuk PPN, ekstrak PPN dari harga jual
            $ppnRate = $obat->ppn_rate ?? 0;
            $hargaJualBersihPerUnit = $item['harga'] / (1 + $ppnRate / 100);
            $ppnAmountPerItem = $item['harga'] - $hargaJualBersihPerUnit;

            $totalSubtotalBersih += $hargaJualBersihPerUnit * $item['qty'];
            $totalPpn += $ppnAmountPerItem * $item['qty'];
        }

        $diskonType = $r->input('diskon_type', 'nominal');
        $diskonValue = (float)$r->input('diskon_value', 0);
        $diskonAmount = 0;
        
        $totalSebelumDiskon = $totalSubtotalBersih + $totalPpn;
        if ($diskonType === 'persen') {
            // Perbaikan: Diskon persentase diterapkan pada total keseluruhan
            $diskonAmount = $totalSebelumDiskon * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $finalTotal = max($totalSebelumDiskon - $diskonAmount, 0);

        $validated = $r->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat_pelanggan' => 'nullable|string|max:1000',
            'telepon_pelanggan' => 'nullable|string|max:20',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'bayar' => 'required|numeric|min:' . $finalTotal,
            'no_ktp' => 'nullable|string|max:20',
        ], [
            'bayar.min' => 'Pembayaran kurang dari total belanja setelah diskon.'
        ]);

        if ($hasPsikotropika && empty($validated['no_ktp'])) {
            return back()->withErrors(['no_ktp' => 'Nomor KTP wajib diisi untuk pembelian obat psikotropika.'])->withInput();
        }

        $bayar = (float)$r->bayar;
        $kembalian = $bayar - $finalTotal;
        $penjualan = null;

        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        if (!$activeShift) {
            return back()->with('error', 'Anda tidak memiliki shift yang sedang berjalan. Silakan mulai shift terlebih dahulu.');
        }

        DB::transaction(function () use ($cart, $totalSubtotalBersih, $totalPpn, $finalTotal, $bayar, $kembalian, $r, $validated, $diskonType, $diskonValue, $diskonAmount, $hasPsikotropika, $activeShift, &$penjualan, $obats) {
            $no = 'PJ-' . date('Ymd') . '-' . str_pad(Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1, 3, '0', STR_PAD_LEFT);
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            $penjualan = Penjualan::create([
                'no_nota' => $no,
                'tanggal' => Carbon::now()->toDateTimeString(),
                'user_id' => Auth::id(),
                'cabang_id' => $cabangId,
                'total' => $finalTotal,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'alamat_pelanggan' => $validated['alamat_pelanggan'],
                'telepon_pelanggan' => $validated['telepon_pelanggan'],
                'pelanggan_id' => $validated['pelanggan_id'],
                'diskon_type' => $diskonType,
                'diskon_value' => $diskonValue,
                'diskon_amount' => $diskonAmount,
                'ppn_percent' => $totalSubtotalBersih > 0 ? ($totalPpn / $totalSubtotalBersih) * 100 : 0,
                'ppn_amount' => $totalPpn,
                'cashier_shift_id' => $activeShift->id,
            ]);

            foreach ($cart as $item) {
                // --- Perbaikan: Gunakan koleksi obat yang sudah di eager load ---
                $obat = $obats->get($item['id']);
                // --- Akhir Perbaikan ---
                
                if (!$obat) {
                    throw new \Exception("Obat dengan ID {$item['id']} tidak ditemukan.");
                }

                $totalHPP = 0;
                $totalQtyUsed = 0;
                $batchIdsUsed = [];

                foreach ($item['batches_used'] as $batchDetail) {
                    $totalHPP += $batchDetail['harga_beli_per_unit'] * $batchDetail['qty_from_batch'];
                    $totalQtyUsed += $batchDetail['qty_from_batch'];
                    $batchIdsUsed[] = $batchDetail['batch_id'];
                }
                $hpp = $totalQtyUsed > 0 ? $totalHPP / $totalQtyUsed : $obat->harga_dasar;
                
                // Logika baru: asumsikan harga sudah termasuk PPN, ekstrak PPN dari harga jual
                $ppnRate = $obat->ppn_rate ?? 0;
                $hargaJualBersihPerItem = $item['harga'] / (1 + $ppnRate / 100);
                $ppnAmountPerItem = $item['harga'] - $hargaJualBersihPerItem;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obat->id,
                    'qty' => $item['qty'],
                    'harga' => $item['harga'], // Harga jual yang diinput (termasuk PPN jika ppn_included)
                    'hpp' => $hpp,
                    'subtotal' => (float)$item['qty'] * (float)$item['harga'],
                    'ppn_amount_per_item' => $ppnAmountPerItem * $item['qty'],
                    'no_ktp' => $hasPsikotropika ? $validated['no_ktp'] : null,
                    'batch_id' => count($batchIdsUsed) === 1 ? $batchIdsUsed[0] : null,
                ]);

                $obat->decrement('stok', $item['qty']);

                foreach ($item['batches_used'] as $batchDetail) {
                    // --- Perbaikan: Gunakan eager loading batches ---
                    $batch = $obat->batches->find($batchDetail['batch_id']);
                    if ($batch) {
                        $batch->decrement('stok_saat_ini', $batchDetail['qty_from_batch']);
                    }
                    // --- Akhir Perbaikan ---
                }
            }

            if ($validated['pelanggan_id']) {
                $pelanggan = Pelanggan::find($validated['pelanggan_id']);
                if ($pelanggan) {
                    $pointsEarned = floor($finalTotal / 1000);
                    $pelanggan->increment('point', $pointsEarned);
                }
            }
        });

        session()->forget('cart');
        session()->forget('diskon_type');
        session()->forget('diskon_value');

        return redirect()->route('pos.print.options', $penjualan->id);
    }

    /**
     * Memvalidasi ulang stok di keranjang.
     */
    private function validateCart(&$cart)
    {
        if (empty($cart)) {
            return;
        }

        // --- Perbaikan: Eager Loading untuk menghindari N+1 query ---
        $obatCodesInCart = array_keys($cart);
        $obats = Obat::with('batches')->whereIn('kode', $obatCodesInCart)->get()->keyBy('kode');
        // --- Akhir Perbaikan ---
        
        foreach ($cart as $kode => &$item) {
            // --- Perbaikan: Gunakan koleksi obat yang sudah di eager load ---
            $obat = $obats->get($kode);
            // --- Akhir Perbaikan ---

            if (!$obat || $obat->stok <= 0 || ($obat->expired_date && Carbon::parse($obat->expired_date)->isPast())) {
                unset($cart[$kode]);
                continue;
            }

            $item['harga'] = $obat->harga_jual;
            $item['kategori'] = $obat->kategori;
            $item['is_psikotropika'] = $obat->is_psikotropika;
            // --- Perbaikan: Ambil PPN rate dan ppn_included dari model Obat ---
            $item['ppn_rate'] = $obat->ppn_rate ?? 0;
            $item['ppn_included'] = $obat->ppn_included ?? false;
            // --- Akhir Perbaikan ---

            $batches = $obat->batches->where('stok_saat_ini', '>', 0)
                                     ->where('expired_date', '>', now())
                                     ->sortBy('expired_date');

            $tempBatchesUsed = [];
            $remainingQtyNeeded = $item['qty'];
            $totalQtyFromBatches = 0;

            foreach ($batches as $batch) {
                if ($remainingQtyNeeded <= 0) break;

                $qtyFromThisBatch = min($remainingQtyNeeded, $batch->stok_saat_ini);
                if ($qtyFromThisBatch > 0) {
                    $tempBatchesUsed[] = [
                        'batch_id' => $batch->id,
                        'no_batch' => $batch->no_batch,
                        'expired_date' => $batch->expired_date,
                        'qty_from_batch' => $qtyFromThisBatch,
                        'harga_beli_per_unit' => $batch->harga_beli_per_unit,
                    ];
                    $totalQtyFromBatches += $qtyFromThisBatch;
                    $remainingQtyNeeded -= $qtyFromThisBatch;
                }
            }

            if ($totalQtyFromBatches < $item['qty']) {
                $item['qty'] = $totalQtyFromBatches;
                $item['batches_used'] = $tempBatchesUsed;
                if ($item['qty'] === 0) {
                    unset($cart[$kode]);
                    continue;
                }
            } else {
                $item['batches_used'] = $tempBatchesUsed;
            }

            $item['stok'] = $obat->stok;
        }
        session(['cart' => $cart]);
    }

    public function printOptions($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('kasir.print-options', compact('penjualan'));
    }

    public function printFaktur($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan', 'details.batchObat')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    public function printKwitansi($id)
    {
        $penjualan = Penjualan::with('kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.kwitansi', compact('penjualan'));
    }

    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan', 'details.batchObat')->findOrFail($id);
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'))->setPaper('A6', 'landscape');
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }

    public function riwayatKasir()
    {
        $activeShift = CashierShift::with('shift')
                                   ->where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $data = Penjualan::with('details.obat', 'pelanggan')
            ->where('user_id', Auth::id())
            ->where('cabang_id', $cabangId)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('kasir.riwayat', compact('data', 'activeShift'));
    }

    public function show($id)
    {
        $p = Penjualan::with('details.obat', 'kasir', 'pelanggan', 'details.batchObat')->findOrFail($id);
        return view('kasir.detail', compact('p'));
    }

    public function success($id)
    {
        $penjualan = Penjualan::with('kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.success', compact('penjualan'));
    }

    public function searchPelanggan(Request $request)
    {
        $query = $request->input('q');
        $pelanggans = Pelanggan::where('nama', 'like', "%{$query}%")
            ->orWhere('telepon', 'like', "%{$query}%")
            ->orWhere('no_ktp', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($pelanggans);
    }

    public function addPelangganCepat(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'status_member' => 'member',
            'point' => 0,  
        ]);

        return response()->json($pelanggan);
    }
}
