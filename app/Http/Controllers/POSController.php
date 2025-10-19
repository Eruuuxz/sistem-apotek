<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Shift;
use App\Models\CashierShift;
use App\Services\POS\CartService;
use App\Services\POS\CheckoutService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * Menampilkan halaman POS.
     * Menampilkan form "Mulai Shift" atau POS itu sendiri.
     */
    public function index()
    {
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
        $initialCash = $activeShift->initial_cash ?? 0; 
        $cart = $this->cartService->getCart();
        $totals = $this->cartService->calculateTotals($cart);
        
        $obat = Obat::where('stok', '>', 0)
            ->where(fn ($query) => $query->whereNull('expired_date')->orWhere('expired_date', '>', now()))
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok', 'is_psikotropika', 'ppn_rate', 'ppn_included']);

        $members = Pelanggan::where('tipe', 'tetap')->orderBy('nama')->get();
        $totalSalesToday = Penjualan::where('cashier_shift_id', $activeShift->id)->sum('total');

        return view('kasir.pos', array_merge(compact('obat', 'cart', 'members', 'activeShift', 'totalSalesToday', 'initialCash'), $totals));
    }

    /**
     * Mengatur modal awal saat memulai sesi kasir.
     */
    public function setInitialCash(Request $request)
    {
        $request->validate(['initial_cash' => 'required|numeric|min:0']);

        if (CashierShift::where('user_id', Auth::id())->where('status', 'open')->exists()) {
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

    /**
     * Menghapus item dari keranjang.
     */
    public function add(Request $request)
    {
        $request->validate(['kode' => 'required|string|exists:obat,kode']);
        
        try {
            $obat = Obat::where('kode', $request->kode)->firstOrFail();
            $this->cartService->addItem($obat);
            return back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function updateQty(Request $request)
    {
        $request->validate(['kode' => 'required|string|exists:obat,kode', 'qty' => 'required|integer|min:0']);

        try {
            $obat = Obat::where('kode', $request->kode)->firstOrFail();
            $this->cartService->updateItemQty($obat, (int) $request->qty);
            return back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $request)
    {
        $request->validate(['kode' => 'required']);
        $this->cartService->removeItem($request->kode);
        return back();
    }

    /**
     * Mengatur diskon untuk transaksi.
     */
    public function setDiskon(Request $request)
    {
        $request->validate([
            'diskon_type' => 'required|in:nominal,persen',
            'diskon_value' => 'required|numeric|min:0'
        ]);
        $this->cartService->setDiskon($request->diskon_type, (float) $request->diskon_value);
        return back()->with('success', 'Diskon berhasil diterapkan');
    }

    /**
     * Memproses checkout dan menyimpan transaksi.
     */
    public function checkout(Request $request)
    {
        $totals = $this->cartService->calculateTotals($this->cartService->getCart());
        $finalTotal = $totals['totalAkhir'];

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat_pelanggan' => 'nullable|string|max:1000',
            'telepon_pelanggan' => 'nullable|string|max:20',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'bayar' => 'required|numeric|min:' . $finalTotal, // Validasi Min di sini membantu UX
            'no_ktp' => 'nullable|string|max:20',
            'diskon_type' => 'required|in:nominal,persen', // Diperlukan untuk CheckoutService
            'diskon_value' => 'required|numeric|min:0', // Diperlukan untuk CheckoutService
        ], ['bayar.min' => 'Pembayaran kurang dari total belanja.']);
        
        try {
            $penjualan = $this->checkoutService->processCheckout($validated);
            return redirect()->route('pos.print.options', $penjualan->id);
        } catch (\Exception $e) {
            // Jika ada error bisnis (misal: psikotropika tanpa KTP, atau shift tidak aktif)
            if (str_contains($e->getMessage(), 'psikotropika')) {
                return back()->withErrors(['no_ktp' => $e->getMessage()])->withInput();
            }
            if (str_contains($e->getMessage(), 'shift')) {
                return back()->with('error', $e->getMessage());
            }
            // Error umum lainnya
            return back()->withInput()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }

    /**
     * Rute untuk riwayat penjualan kasir.
     */
    public function riwayatKasir(Request $request)
    {
        $cabangId = Auth::user()->cabang_id ?? \App\Models\Cabang::first()->id;
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

    /**
     * Menampilkan detail penjualan.
     */
    public function show($id)
    {
        $p = Penjualan::with('details.obat', 'kasir', 'pelanggan')->findOrFail($id);
        return view('kasir.detail', compact('p'));
    }

    /**
     * Mengakhiri sesi kasir (clear initial cash / close shift).
     */
    public function clearInitialCash(Request $request)
    {
        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->where('status', 'open')
                                   ->first();

        if ($activeShift) {
            $activeShift->status = 'closed';
            $activeShift->end_time = now();
            $activeShift->save();
            $this->cartService->clearCart();
            return redirect()->route('pos.index')->with('success', 'Sesi kasir berhasil diakhiri.');
        }

        return redirect()->route('pos.index')->with('error', 'Tidak ada sesi kasir aktif ditemukan.');
    }
}