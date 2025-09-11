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
            'total_hidden'      => 'required|numeric|min:0',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        $total = collect($cart)->sum(fn($i) => (float)$i['harga'] * (int)$i['qty']);
        $bayar = (float)$r->bayar;

        if ($bayar < $total) {
            $kekurangan = $total - $bayar;
            return back()->with('error', 'Pembayaran kurang Rp ' . number_format($kekurangan, 0, ',', '.'));
        }

        DB::transaction(function () use ($cart, $total, $bayar, $r, &$penjualan) {
            $no = 'PJ-' . date('Ymd') . '-' . str_pad(Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1, 3, '0', STR_PAD_LEFT);
            $kembalian = $bayar - $total;
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;
        
            $penjualan = Penjualan::create([
                'no_nota'           => $no,
                'tanggal'           => \Carbon\Carbon::now()->toDateTimeString(),
                'user_id'           => Auth::id(),
                'cabang_id'         => $cabangId,
                'total'             => $total,
                'bayar'             => $bayar,
                'kembalian'         => $kembalian,
                'nama_pelanggan'    => $r->nama_pelanggan,
                'alamat_pelanggan'  => $r->alamat_pelanggan,
                'telepon_pelanggan' => $r->telepon_pelanggan,
            ]);

            foreach ($cart as $item) {
                $obat_id = $item['id'] ?? Obat::where('kode', $item['kode'])->value('id');
                $obat    = Obat::find($obat_id);

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id'      => $obat_id,
                    'qty'          => (int)$item['qty'],
                    'harga'        => (float)$item['harga'],
                    'hpp'          => (float)($obat->harga_dasar ?? 0),
                    'subtotal'     => (float)$item['qty'] * (float)$item['harga'],
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