<?php

namespace App\Http\Controllers;

use App\Models\Barang; // Menggunakan model Barang untuk POS
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $barang = Barang::orderBy('nama')->get(['id', 'kode', 'nama', 'harga_jual', 'stok']);
        
        $cart = session('cart', []); // ['BRG001'=>['kode'=>..,'nama'=>..,'harga'=>..,'qty'=>..]]
        $this->validateCart($cart); // Validasi keranjang saat dimuat
        
        $total = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);
        
        return view('kasir.pos', compact('barang', 'cart', 'total'));
    }

    public function add(Request $r)
    {
        $r->validate(['kode' => 'required']);
        
        $b = Barang::where('kode', $r->kode)->first();
        
        if (!$b) {
            return back()->with('error', 'Barang tidak ditemukan');
        }

        $cart = session('cart', []);
        
        if (isset($cart[$b->kode])) {
            // Cek stok sebelum menambah
            if ($cart[$b->kode]['qty'] + 1 > $b->stok) {
                return back()->with('error', 'Stok ' . $b->nama . ' tidak cukup.');
            }
            $cart[$b->kode]['qty'] += 1;
        } else {
            // Cek stok untuk item baru
            if ($b->stok < 1) {
                return back()->with('error', 'Stok ' . $b->nama . ' kosong.');
            }
            $cart[$b->kode] = [
                'kode' => $b->kode,
                'nama' => $b->nama,
                'harga' => $b->harga_jual,
                'qty' => 1,
                'stok' => $b->stok // Simpan stok saat ini untuk referensi
            ];
        }
        
        session(['cart' => $cart]);
        return back();
    }

    public function updateQty(Request $r)
    {
        $r->validate(['kode' => 'required', 'qty' => 'required|integer|min:1']);
        
        $cart = session('cart', []);
        
        if (isset($cart[$r->kode])) {
            $b = Barang::where('kode', $r->kode)->first();
            if (!$b) { // Barang tidak ditemukan di DB, hapus dari keranjang
                unset($cart[$r->kode]);
                session(['cart' => $cart]);
                return back()->with('error', 'Barang tidak ditemukan di database.');
            }

            // Batasi qty agar tidak melebihi stok yang tersedia
            $newQty = (int)$r->qty;
            if ($newQty > $b->stok) {
                $newQty = $b->stok; // Set ke stok maksimal
                session(['cart' => $cart]); // Update session jika qty diubah
                return back()->with('error', 'Kuantitas melebihi stok yang tersedia. Stok maksimal: ' . $b->stok);
            }
            
            $cart[$r->kode]['qty'] = $newQty;
            session(['cart' => $cart]);
        }
        return back();
    }

    public function remove(Request $r)
    {
        $r->validate(['kode' => 'required']);
        
        $cart = session('cart', []);
        unset($cart[$r->kode]);
        session(['cart' => $cart]);
        return back();
    }

    // Helper function untuk memvalidasi dan mengunci harga/stok di keranjang
    private function validateCart(&$cart)
    {
        foreach ($cart as $kode => &$item) {
            $b = Barang::where('kode', $kode)->first();
            if (!$b) {
                unset($cart[$kode]); // Hapus item jika barang tidak ditemukan di DB
                continue;
            }
            // Kunci harga ke harga jual terbaru dari DB
            $item['harga'] = $b->harga_jual;
            // Batasi qty hingga stok yang tersedia
            if ($item['qty'] > $b->stok) {
                $item['qty'] = $b->stok;
            }
            if ($item['qty'] < 1) { // Pastikan qty minimal 1
                $item['qty'] = 1;
            }
            // Update stok referensi di keranjang
            $item['stok'] = $b->stok;
        }
        session(['cart' => $cart]); // Simpan kembali keranjang yang sudah divalidasi
    }
}