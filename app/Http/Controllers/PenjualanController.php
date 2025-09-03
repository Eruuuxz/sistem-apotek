<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, PenjualanDetail, Obat}; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

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
        if(!$obat) return back()->with('error', 'Obat tidak ditemukan');

        $cart = session('cart', []);
        $key = array_search($obat->kode, array_column($cart, 'kode'));
        if($key !== false) {
            $cart[$key]['qty'] += 1;
        } else {
            $cart[] = [
                'id' => $obat->id,
                'kode' => $obat->kode,
                'nama' => $obat->nama,
                'harga' => $obat->harga_jual,
                'qty' => 1,
                'stok' => $obat->stok
            ];
        }
        session(['cart' => $cart]);
        return back();
    }

    public function updateCart(Request $r)
    {
        $cart = session('cart', []);
        foreach($cart as &$item){
            if($item['kode'] == $r->kode){
                $item['qty'] = min($r->qty, $item['stok']);
            }
        }
        session(['cart' => $cart]);
        return back();
    }

    public function removeCart(Request $r)
    {
        $cart = session('cart', []);
        $cart = array_filter($cart, fn($item) => $item['kode'] != $r->kode);
        session(['cart' => array_values($cart)]);
        return back();
    }

    public function checkout(Request $r)
{
    $cart = session('cart', []);
    if(empty($cart)) return back()->with('error','Keranjang kosong');

    $total = collect($cart)->sum(fn($i) => $i['harga'] * $i['qty']);

    if($r->bayar < $total) return back()->with('error','Pembayaran kurang');

    DB::transaction(function() use ($cart, $r, $total, &$penjualan){
        // Generate no_nota unik
        $no = 'PJ-'.date('Ymd').'-'.str_pad(Penjualan::whereDate('tanggal', date('Y-m-d'))->count()+1,3,'0',STR_PAD_LEFT);
        $kembalian = $r->bayar - $total;

        // Simpan Penjualan
        $penjualan = Penjualan::create([
            'no_nota' => $no,
            'tanggal' => now()->toDateString(),
            'user_id' => Auth::id(),
            'total' => $total,
            'bayar' => $r->bayar,
            'kembalian' => $kembalian
        ]);

        // Simpan detail penjualan & update stok
        foreach($cart as $item){
            // Pastikan id obat ada, kalau tidak ambil dari kode
            $obat_id = $item['id'] ?? Obat::where('kode', $item['kode'])->value('id');

            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'obat_id' => $obat_id,
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'subtotal' => $item['qty'] * $item['harga']
            ]);

            if($obat_id) {
                Obat::find($obat_id)->decrement('stok', $item['qty']);
            }
        }
    });

    session()->forget('cart');

    return redirect()->route('penjualan.struk', $penjualan->id);
}

    public function struk($id)
    {
        $penjualan = Penjualan::with('details.obat','kasir')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('details.obat','kasir')->findOrFail($id);
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'))->setPaper('A6','landscape');
        return $pdf->stream('faktur-'.$penjualan->no_nota.'.pdf');
    }
    public function riwayatKasir()
{
    $data = Penjualan::with('details.obat')
        ->where('user_id', Auth::id()) // hanya penjualan kasir yang login
        ->orderBy('tanggal', 'desc')
        ->paginate(10);

    return view('kasir.riwayat', compact('data'));
}
public function show($id)
{
    // Ambil penjualan beserta detail dan kasir
    $p = Penjualan::with('details.obat', 'kasir')->findOrFail($id);

    return view('kasir.detail', compact('p'));
}
}