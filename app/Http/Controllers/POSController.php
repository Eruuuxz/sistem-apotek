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
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini sudah terinstal dan dikonfigurasi

class POSController extends Controller
{
    public function index()
    {
        $activeShift = CashierShift::where('user_id', Auth::id())
                                   ->whereNull('end_time')
                                   ->with('shift')
                                   ->first();

        $shifts = Shift::all(); // Ini akan mengambil semua shift yang ada, termasuk Pagi dan Sore

        if ($activeShift) {
            // Logika untuk POS jika shift aktif
            $cart = session()->get('cart', []);
            $totalHarga = collect($cart)->sum(function($item) {
                return $item['qty'] * $item['harga'];
            });

            // Ambil diskon dari session
            $diskonValue = session('diskon_value', 0);
            $diskonType = session('diskon_type', 'nominal');

            $totalAkhir = $totalHarga;
            if ($diskonType === 'persen') {
                $totalAkhir -= $totalHarga * ($diskonValue / 100);
            } else {
                $totalAkhir -= $diskonValue;
            }
            $totalAkhir = max(0, $totalAkhir); // Pastikan total tidak negatif

            $obat = Obat::where('stok', '>', 0)->get(); // Untuk modal list obat

            return view('kasir.pos', compact('cart', 'activeShift', 'totalAkhir', 'diskonValue', 'diskonType', 'obat', 'shifts'));
        } else {
            // Tampilkan form untuk memulai shift
            // akan menangani tampilan form memulai shift jika $activeShift null.
            // Pastikan view 'kasir.pos' bisa menerima $shifts untuk dropdown.
            return view('kasir.pos', compact('activeShift', 'shifts'));
        }
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
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok', 'is_psikotropika']);

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

        $cart[$obat->kode] = [
            'id' => $obat->id,
            'kode' => $obat->kode,
            'nama' => $obat->nama,
            'kategori' => $obat->kategori,
            'harga' => $obat->harga_jual,
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

        $cart[$kode]['qty'] = $newQty;
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

        $totalCart = collect($cart)->sum(fn($i) => (float)$i['harga'] * (int)$i['qty']);

        $diskonType = $r->input('diskon_type', 'nominal');
        $diskonValue = (float)$r->input('diskon_value', 0);
        $diskonAmount = 0;

        if ($diskonType === 'persen') {
            $diskonAmount = $totalCart * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $finalTotal = max($totalCart - $diskonAmount, 0);

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

        $hasPsikotropika = collect($cart)->contains(fn($item) => $item['is_psikotropika']);
        if ($hasPsikotropika && empty($validated['no_ktp'])) {
            return back()->withErrors(['no_ktp' => 'Nomor KTP wajib diisi untuk pembelian obat psikotropika.'])->withInput();
        }

        $bayar = (float)$r->bayar;
        $kembalian = $bayar - $finalTotal;
        $penjualan = null; // Inisialisasi variabel penjualan

        $activeShift = CashierShift::where('user_id', Auth::id())
                                ->where('status', 'open')
                                ->first();

        if (!$activeShift) {
            return back()->with('error', 'Anda tidak memiliki shift yang sedang berjalan. Silakan mulai shift terlebih dahulu.');
        }

        DB::transaction(function () use ($cart, $finalTotal, $bayar, $kembalian, $r, $validated, $diskonType, $diskonValue, $diskonAmount, $hasPsikotropika, $activeShift, &$penjualan) {
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
                'cashier_shift_id' => $activeShift->id,
            ]);

            foreach ($cart as $item) {
                $obat = Obat::find($item['id']);
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

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obat->id,
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'hpp' => $hpp,
                    'subtotal' => (float)$item['qty'] * (float)$item['harga'],
                    'no_ktp' => $hasPsikotropika ? $validated['no_ktp'] : null,
                    'batch_id' => count($batchIdsUsed) === 1 ? $batchIdsUsed[0] : null,
                ]);

                $obat->decrement('stok', $item['qty']);

                foreach ($item['batches_used'] as $batchDetail) {
                    $batch = BatchObat::find($batchDetail['batch_id']);
                    if ($batch) {
                        $batch->decrement('stok_saat_ini', $batchDetail['qty_from_batch']);
                    }
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
        foreach ($cart as $kode => &$item) {
            $obat = Obat::where('kode', $kode)->first();

            if (!$obat || $obat->stok <= 0 || ($obat->expired_date && Carbon::parse($obat->expired_date)->isPast())) {
                unset($cart[$kode]);
                continue;
            }

            $item['harga'] = $obat->harga_jual;
            $item['kategori'] = $obat->kategori;
            $item['is_psikotropika'] = $obat->is_psikotropika;

            $batches = BatchObat::where('obat_id', $obat->id)
                ->where('stok_saat_ini', '>', 0)
                ->where('expired_date', '>', now())
                ->orderBy('expired_date', 'asc')
                ->get();

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

    // ... (Metode untuk mencetak, riwayat, dan pelanggan tetap sama) ...
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
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $data = Penjualan::with('details.obat', 'pelanggan')
            ->where('user_id', Auth::id())
            ->where('cabang_id', $cabangId)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('kasir.riwayat', compact('data'));
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