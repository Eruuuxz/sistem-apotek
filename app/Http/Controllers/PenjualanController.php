<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, PenjualanDetail, Obat, Cabang};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function pos()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['harga'] * $i['qty']);
        return view('kasir.pos', compact('cart', 'total'));
    }

    public function addToCart(Request $r)
    {
        $obat = Obat::where('kode', $r->kode)->first();
        if (!$obat) {
            return back()->with('error', 'Obat tidak ditemukan');
        }

        $cart = session('cart', []);

        if (isset($cart[$obat->kode])) {
            $cart[$obat->kode]['qty'] += 1;
        } else {
            $cart[$obat->kode] = [
                'id'    => $obat->id,
                'kode'  => $obat->kode,
                'nama'  => $obat->nama,
                'harga' => $obat->harga_jual,
                'qty'   => 1,
                'stok'  => $obat->stok,
                'is_psikotropika' => $obat->is_psikotropika, // Tambahkan ini
                'golongan_obat' => $obat->golongan_obat,     // Tambahkan ini
            ];
        }

        session(['cart' => $cart]);
        return back();
    }

    public function updateCart(Request $r)
    {
        $cart = session('cart', []);
        foreach ($cart as &$item) {
            if ($item['kode'] == $r->kode) {
                $item['qty'] = min($r->qty, $item['stok']);
            }
        }
        session(['cart' => $cart]);
        return back();
    }

    public function removeCart(Request $r)
    {
        $cart = session('cart', []);
        unset($cart[$r->kode]);
        session(['cart' => $cart]);
        return back();
    }

        public function checkout(Request $r)
        {
            $validated = $r->validate([
                'nama_pelanggan'    => 'required|string|max:255',
                'alamat_pelanggan'  => 'nullable|string|max:1000',
                'telepon_pelanggan' => 'nullable|string|max:20',
                'bayar'             => 'required|numeric|min:0',
                'items'             => 'required|array', // Pastikan items ini ada dan array
                'items.*.obat_id'   => 'required|exists:obat,id',
                'items.*.qty'       => 'required|integer|min:1',
                'items.*.no_ktp'    => 'nullable|string|max:20', // Validasi no_ktp untuk setiap item
                // Validasi diskon
                'diskon_type'       => 'nullable|in:fixed,percent',
                'diskon_value'      => 'nullable|numeric|min:0',
            ]);

            $cart = session('cart', []);
            if (empty($cart)) {
                return back()->with('error', 'Keranjang kosong');
            }

            // Hitung total sebelum diskon
            $totalItems = collect($cart)->sum(fn($i) => (float)$i['harga'] * (int)$i['qty']);

            // Hitung diskon
            $diskonType = $r->input('diskon_type', null);
            $diskonValue = (float) $r->input('diskon_value', 0);
            $diskonAmount = 0.0;

            if ($diskonType === 'percent') {
                $diskonValue = min(max($diskonValue, 0), 100);
                $diskonAmount = round($totalItems * ($diskonValue / 100), 2);
            } elseif ($diskonType === 'fixed') {
                $diskonAmount = round(max(0, $diskonValue), 2);
            }

            // Pastikan diskon tidak melebihi total
            $diskonAmount = min($diskonAmount, $totalItems);

            // Total akhir setelah diskon
            $totalFinal = round($totalItems - $diskonAmount, 2);

            // Validasi bayar cukup
            if ((float)$r->input('bayar') < $totalFinal) {
                return back()->with('error', 'Pembayaran kurang dari total setelah diskon.');
            }

            // Validasi psikotropika: Iterasi melalui item di keranjang, bukan dari request items
            foreach ($cart as $cartItem) {
                $obat = Obat::find($cartItem['id']); // Gunakan ID obat dari cart
                if ($obat && $obat->is_psikotropika) {
                    // Cari no_ktp yang sesuai dari request berdasarkan obat_id
                    $noKtpForThisItem = null;
                    foreach ($r->items as $reqItem) {
                        if ($reqItem['obat_id'] == $obat->id) {
                            $noKtpForThisItem = $reqItem['no_ktp'] ?? null;
                            break;
                        }
                    }
                    if (empty($noKtpForThisItem)) {
                        return back()->with('error', "Obat {$obat->nama} adalah psikotropika, wajib isi No KTP.");
                    }
                }
            }


            DB::transaction(function () use ($cart, $totalItems, $totalFinal, $diskonType, $diskonValue, $diskonAmount, $r, &$penjualan) {
                $no = 'PJ-' . date('Ymd') . '-' . str_pad(Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1, 3, '0', STR_PAD_LEFT);
                $kembalian = (float)$r->input('bayar') - $totalFinal;
                $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

                $penjualan = Penjualan::create([
                    'no_nota'           => $no,
                    'tanggal'           => now()->toDateTimeString(),
                    'user_id'           => Auth::id(),
                    'cabang_id'         => $cabangId,
                    'total'             => $totalFinal,
                    'bayar'             => (float)$r->input('bayar'),
                    'kembalian'         => $kembalian,
                    'nama_pelanggan'    => $r->nama_pelanggan,
                    'alamat_pelanggan'  => $r->alamat_pelanggan,
                    'telepon_pelanggan' => $r->telepon_pelanggan,
                    'diskon_type'       => $diskonType,
                    'diskon_value'      => $diskonValue,
                    'diskon_amount'     => $diskonAmount,
                ]);

                foreach ($cart as $item) {
                    $obat_id = $item['id']; // Gunakan ID dari item keranjang
                    $obat = Obat::find($obat_id);

                    // Cari no_ktp yang sesuai dari request berdasarkan obat_id
                    $noKtpForThisItem = null;
                    foreach ($r->items as $reqItem) {
                        if ($reqItem['obat_id'] == $obat_id) {
                            $noKtpForThisItem = $reqItem['no_ktp'] ?? null;
                            break;
                        }
                    }

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'obat_id'      => $obat_id,
                        'qty'          => (int)$item['qty'],
                        'harga'        => (float)$item['harga'],
                        'hpp'          => (float)($obat->harga_dasar ?? 0),
                        'subtotal'     => (float)$item['qty'] * (float)$item['harga'],
                        'no_ktp'       => $noKtpForThisItem,
                    ]);

                    if ($obat_id) {
                        Obat::find($obat_id)->decrement('stok', $item['qty']);
                    }
                }
            });

            session()->forget('cart');

            return redirect()->route('pos.print.options', $penjualan->id);
        }
    
    // --- Print Options ---
    public function printOptions($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('kasir.print-options', compact('penjualan'));
    }

    public function printFaktur($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    public function printKwitansi($id)
    {
        $penjualan = Penjualan::with('kasir')->findOrFail($id);
        return view('kasir.kwitansi', compact('penjualan'));
    }

    // --- Struk PDF ---
    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('details.obat', 'kasir')->findOrFail($id);
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'))->setPaper('A6', 'landscape');
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }

    // --- Riwayat & Detail ---
    public function riwayatKasir()
    {
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $data = Penjualan::with('details.obat')
            ->where('user_id', Auth::id())
            ->where('cabang_id', $cabangId)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('kasir.riwayat', compact('data'));
    }


    public function show($id)
    {
        $p = Penjualan::with('details.obat', 'kasir')->findOrFail($id);
        return view('kasir.detail', compact('p'));
    }

    // --- Success Page ---
    public function success($id)
    {
        $penjualan = Penjualan::with('kasir')->findOrFail($id);
        return view('kasir.success', compact('penjualan'));
    }
}