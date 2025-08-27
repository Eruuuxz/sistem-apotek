<?php

namespace App\Http\Controllers;

use App\Models\Obat; 
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $obat = Obat::orderBy('nama')->get(['id', 'kode', 'nama', 'harga_jual', 'stok']); 
        
        $cart = session('cart', []); // ['BRG001'=>['kode'=>..,'nama'=>..,'harga'=>..,'qty'=>..]]
        $this->validateCart($cart); // Validasi keranjang saat dimuat
        
        $total = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);
        
        return view('kasir.pos', compact('obat', 'cart', 'total')); 
    }

    public function add(Request $r)
    {
        $r->validate(['kode' => 'required']);
        
        $o = Obat::where('kode', $r->kode)->first(); 
        
        if (!$o) { 
            return back()->with('error', 'Obat tidak ditemukan'); 
        }

        $cart = session('cart', []);
        
        if (isset($cart[$o->kode])) { 
            // Cek stok sebelum menambah
            if ($cart[$o->kode]['qty'] + 1 > $o->stok) { 
                return back()->with('error', 'Stok ' . $o->nama . ' tidak cukup.'); 
            }
            $cart[$o->kode]['qty'] += 1; 
        } else {
            // Cek stok untuk item baru
            if ($o->stok < 1) { 
                return back()->with('error', 'Stok ' . $o->nama . ' kosong.'); 
            }
            $cart[$o->kode] = [ 
                'kode' => $o->kode, 
                'nama' => $o->nama, 
                'harga' => $o->harga_jual, 
                'qty' => 1,
                'stok' => $o->stok // Simpan stok saat ini untuk referensi 
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
            $o = Obat::where('kode', $r->kode)->first(); 
            if (!$o) { 
                unset($cart[$r->kode]);
                session(['cart' => $cart]);
                return back()->with('error', 'Obat tidak ditemukan di database.'); 
            }

            // Batasi qty agar tidak melebihi stok yang tersedia
            $newQty = (int)$r->qty;
            if ($newQty > $o->stok) { 
                $newQty = $o->stok; // Set ke stok maksimal 
                session(['cart' => $cart]); // Update session jika qty diubah
                return back()->with('error', 'Kuantitas melebihi stok yang tersedia. Stok maksimal: ' . $o->stok); 
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
            $o = Obat::where('kode', $kode)->first();
            if (!$o) { 
                unset($cart[$kode]); // Hapus item jika obat tidak ditemukan di DB
                continue;
            }
            // Kunci harga ke harga jual terbaru dari DB
            $item['harga'] = $o->harga_jual; 
            // Batasi qty hingga stok yang tersedia
            if ($item['qty'] > $o->stok) { 
                $item['qty'] = $o->stok; 
            }
            if ($item['qty'] < 1) { // Pastikan qty minimal 1
                $item['qty'] = 1;
            }
            // Update stok referensi di keranjang
            $item['stok'] = $o->stok; 
        }
        session(['cart' => $cart]); // Simpan kembali keranjang yang sudah divalidasi
    }
}
