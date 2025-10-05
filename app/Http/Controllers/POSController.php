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
            $diskonAmount = $totalSebelumDiskon * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $totalAkhir = max($totalSebelumDiskon - $diskonAmount, 0);

        $members = Pelanggan::where('tipe', 'tetap')->orderBy('nama')->get();
        $totalSalesToday = Penjualan::where('cashier_shift_id', $activeShift->id)->sum('total');
        $initialCash = $activeShift->initial_cash;

        return view('kasir.pos', compact('obat', 'cart', 'totalSubtotalBersih', 'diskonType', 'diskonValue', 'diskonAmount', 'totalAkhir', 'members', 'activeShift', 'totalSalesToday', 'totalPpn', 'initialCash'));
    }

    public function setInitialCash(Request $request)
    {
        $request->validate([
            'initial_cash' => 'required|numeric|min:0',
        ]);

        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        if ($activeShift) {
            return redirect()->route('pos.index')->with('error', 'Anda sudah memiliki sesi kasir yang aktif.');
        }

        $currentTime = Carbon::now();
        $shiftName = ($currentTime->hour >= 7 && $currentTime->hour < 15) ? 'Pagi' : 'Sore';
        $shift = Shift::where('name', $shiftName)->first();

        if (!$shift) {
            return back()->with('error', 'Shift saat ini tidak ditemukan. Silakan hubungi administrator.');
        }

        CashierShift::create([
            'user_id' => Auth::id(),
            'shift_id' => $shift->id,
            'initial_cash' => $request->initial_cash,
            'start_time' => $currentTime,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Sesi kasir berhasil dimulai.');
    }

    public function shiftSummary(Request $request)
    {
        $query = CashierShift::with('shift')
                         ->withSum('penjualan as total_sales', 'total')
                         ->where('user_id', Auth::id());

        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $cashierShifts = $query->orderBy('start_time', 'desc')->paginate(10);

        return view('kasir.summary', compact('cashierShifts'));
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
            return back()->with('error', 'Obat ' . $obat->nama . ' sudah kadaluarsa.');
        }

        $cart = session('cart', []);
        $qtyToAdd = isset($cart[$obat->kode]) ? $cart[$obat->kode]['qty'] + 1 : 1;

        if ($qtyToAdd > $obat->stok) {
            return back()->with('error', 'Kuantitas melebihi stok yang tersedia.');
        }

        // ... (Logika batch tetap sama)

        $cart[$obat->kode] = [
            'id' => $obat->id,
            'kode' => $obat->kode,
            'nama' => $obat->nama,
            'kategori' => $obat->kategori,
            'harga' => $obat->harga_jual,
            'ppn_rate' => $obat->ppn_rate ?? 0,
            'ppn_included' => $obat->ppn_included ?? false,
            'qty' => $qtyToAdd,
            'stok' => $obat->stok,
            'is_psikotropika' => $obat->is_psikotropika,
            'batches_used' => $this->getBatchesForQty($obat, $qtyToAdd),
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
            return back()->with('error', 'Kuantitas melebihi stok. Stok maksimal: ' . $obat->stok);
        }
        
        $cart[$kode]['qty'] = $newQty;
        $cart[$kode]['batches_used'] = $this->getBatchesForQty($obat, $newQty);
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
        session(['diskon_type' => $r->diskon_type, 'diskon_value' => $r->diskon_value]);
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

        $obatIdsInCart = collect($cart)->pluck('id')->unique()->toArray();
        $obats = Obat::with('batches')->whereIn('id', $obatIdsInCart)->get()->keyBy('id');
        
        $totalSubtotalBersih = 0;
        $totalPpn = 0;
        $hasPsikotropika = false;
        
        foreach ($cart as $item) {
            $obat = $obats->get($item['id']);
            if (!$obat) {
                session()->forget('cart');
                return back()->with('error', 'Obat di keranjang tidak valid. Keranjang dikosongkan.');
            }
            if ($obat->is_psikotropika) $hasPsikotropika = true;
            
            $ppnRate = $obat->ppn_rate ?? 0;
            $hargaJualBersihPerUnit = $item['harga'] / (1 + $ppnRate / 100);
            $ppnAmountPerItem = $item['harga'] - $hargaJualBersihPerUnit;

            $totalSubtotalBersih += $hargaJualBersihPerUnit * $item['qty'];
            $totalPpn += $ppnAmountPerItem * $item['qty'];
        }

        $diskonType = $r->input('diskon_type', 'nominal');
        $diskonValue = (float)$r->input('diskon_value', 0);
        
        $totalSebelumDiskon = $totalSubtotalBersih + $totalPpn;
        $diskonAmount = $diskonType === 'persen' ? $totalSebelumDiskon * ($diskonValue / 100) : $diskonValue;
        $finalTotal = max($totalSebelumDiskon - $diskonAmount, 0);

        $validated = $r->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat_pelanggan' => 'nullable|string|max:1000',
            'telepon_pelanggan' => 'nullable|string|max:20',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'bayar' => 'required|numeric|min:' . $finalTotal,
            'no_ktp' => 'nullable|string|max:20',
        ], ['bayar.min' => 'Pembayaran kurang dari total belanja.']);

        if ($hasPsikotropika && empty($validated['no_ktp'])) {
            return back()->withErrors(['no_ktp' => 'Nomor KTP wajib diisi untuk pembelian psikotropika.'])->withInput();
        }

        $activeShift = CashierShift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$activeShift) {
            return back()->with('error', 'Sesi kasir tidak aktif. Silakan mulai sesi baru.');
        }

        $penjualan = null;
        DB::transaction(function () use ($cart, $totalSubtotalBersih, $totalPpn, $finalTotal, $r, $validated, $diskonType, $diskonValue, $diskonAmount, $hasPsikotropika, $activeShift, &$penjualan, $obats) {
            $penjualan = Penjualan::create([
                'no_nota' => 'PJ-' . date('Ymd') . '-' . str_pad(Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1, 3, '0', STR_PAD_LEFT),
                'tanggal' => now(),
                'user_id' => Auth::id(),
                'cabang_id' => Auth::user()->cabang_id ?? Cabang::first()->id,
                'total' => $finalTotal,
                'bayar' => (float)$r->bayar,
                'kembalian' => (float)$r->bayar - $finalTotal,
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'alamat_pelanggan' => $validated['alamat_pelanggan'],
                'telepon_pelanggan' => $validated['telepon_pelanggan'],
                'pelanggan_id' => $validated['pelanggan_id'],
                'diskon_type' => $diskonType,
                'diskon_value' => $diskonValue,
                'diskon_amount' => $diskonAmount,
                'ppn_amount' => $totalPpn,
                'cashier_shift_id' => $activeShift->id,
            ]);

            foreach ($cart as $item) {
                $obat = $obats->get($item['id']);
                if (!$obat) throw new \Exception("Obat ID {$item['id']} tidak valid.");

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
                    'no_ktp' => $hasPsikotropika ? $validated['no_ktp'] : null,
                ]);

                $obat->decrement('stok', $item['qty']);

                foreach ($item['batches_used'] as $batchDetail) {
                    $batch = $obat->batches->find($batchDetail['batch_id']);
                    if ($batch) $batch->decrement('stok_saat_ini', $batchDetail['qty_from_batch']);
                }
            }
        });

        session()->forget(['cart', 'diskon_type', 'diskon_value']);
        return redirect()->route('pos.print.options', $penjualan->id);
    }

    /**
     * Memvalidasi ulang stok di keranjang.
     */
    private function validateCart(&$cart)
    {
        if (empty($cart)) return;

        $obatCodesInCart = array_keys($cart);
        $obats = Obat::with('batches')->whereIn('kode', $obatCodesInCart)->get()->keyBy('kode');
        
        foreach ($cart as $kode => &$item) {
            $obat = $obats->get($kode);

            if (!$obat || $obat->stok <= 0 || ($obat->expired_date && now()->gt($obat->expired_date))) {
                unset($cart[$kode]);
                continue;
            }

            $item['qty'] = min($item['qty'], $obat->stok);
            $item['harga'] = $obat->harga_jual;
            $item['kategori'] = $obat->kategori;
            $item['is_psikotropika'] = $obat->is_psikotropika;
            $item['ppn_rate'] = $obat->ppn_rate ?? 0;
            $item['ppn_included'] = $obat->ppn_included ?? false;
            $item['stok'] = $obat->stok;
            $item['batches_used'] = $this->getBatchesForQty($obat, $item['qty']);
            
            if ($item['qty'] === 0) unset($cart[$kode]);
        }
        session(['cart' => $cart]);
    }

    private function getBatchesForQty(Obat $obat, $qtyNeeded)
    {
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

    public function printOptions($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('kasir.print-options', compact('penjualan'));
    }

    public function printFaktur($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    public function printKwitansi($id)
    {
        $penjualan = Penjualan::with('kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.kwitansi', compact('penjualan'));
    }

    public function printInvoice($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.invoice', compact('penjualan'));
    }

    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'))->setPaper('A6', 'landscape');
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }

    public function riwayatKasir(Request $request)
    {
        $cabangId = Auth::user()->cabang_id ?? Cabang::first()->id;
        $selectedDate = $request->input('date', now()->toDateString());

        $query = Penjualan::with('pelanggan')
            ->where('user_id', Auth::id())
            ->where('cabang_id', $cabangId)
            ->whereDate('tanggal', $selectedDate)
            ->orderBy('tanggal', 'desc');

        $data = $query->paginate(10);
        $totalHarian = (clone $query)->sum('total');

        return view('kasir.riwayat', compact('data', 'selectedDate', 'totalHarian'));
    }

    public function show($id)
    {
        $p = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.detail', compact('p'));
    }

    public function success($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('kasir.success', compact('penjualan'));
    }

    public function searchPelanggan(Request $request)
    {
        $query = $request->input('q');
        $pelanggans = Pelanggan::where('tipe', 'tetap')
            ->where(function($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                  ->orWhere('telepon', 'like', "%{$query}%")
                  ->orWhere('no_ktp', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
        return response()->json($pelanggans);
    }

    public function addPelangganCepat(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ]);
        
        $data['tipe'] = 'tetap'; // Pelanggan baru otomatis 'tetap'
        $pelanggan = Pelanggan::create($data);
        return response()->json($pelanggan);
    }

    public function clearInitialCash(Request $request)
    {
        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        if ($activeShift) {
            $activeShift->status = 'closed';
            $activeShift->end_time = now();
            $activeShift->save();
            $request->session()->forget(['cart', 'diskon_type', 'diskon_value']);
            return redirect()->route('pos.index')->with('success', 'Sesi kasir berhasil diakhiri.');
        }

        return redirect()->route('pos.index')->with('error', 'Tidak ada sesi kasir aktif ditemukan.');
    }
}