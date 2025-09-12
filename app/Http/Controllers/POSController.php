<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Carbon\Carbon;

class POSController extends Controller
{
    public function index()
    {
        $obat = Obat::orderBy('nama')->get(['id', 'kode', 'nama','kategori', 'expired_date', 'harga_jual', 'stok']);

        $cart = session('cart', []);
        $this->validateCart($cart);

        $total = collect($cart)->sum(fn($i) => $i['harga'] * $i['qty']);

        // Ambil diskon dari session (jika ada)
        $diskonType = session('diskon_type', 'nominal'); // default nominal
        $diskonValue = session('diskon_value', 0);

        if ($diskonType === 'persen') {
            $diskonAmount = $total * ($diskonValue / 100);
        } else {
            $diskonAmount = $diskonValue;
        }

        $totalAkhir = max($total - $diskonAmount, 0);

        $members = Pelanggan::orderBy('nama')->get();

        return view('kasir.pos', compact('obat', 'cart', 'total', 'diskonType', 'diskonValue', 'diskonAmount', 'totalAkhir', 'members'));
    }


    public function search(Request $request)
    {
        $keyword = $request->get('q');

        $obat = Obat::where('nama', 'like', '%'. $keyword . '%')
            ->orWhere('kode', 'like', '%'. $keyword . '%')
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok']);

        return response()->json($obat);
    }

    public function add(Request $r)
    {
        $r->validate(['kode' => 'required']);

        $o = Obat::where('kode', $r->kode)->where('stok', '>', 0)->orderBy('expired_date', 'asc')->first();

        // Cek jika obat tidak ditemukan atau sudah kadaluarsa
        if (!$o) {
            return back()->with('error', 'Obat tidak ditemukan atau stok kosong.');
        }

        if (Carbon::parse($o->expired_date)->isPast()) {
            return back()->with('error', 'Obat sudah kadaluarsa dan tidak bisa ditambahkan.');
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
                'kategori' => $o->kategori,
                'harga' => $o->harga_jual,
                'qty' => 1,
                'stok' => $o->stok, // Simpan stok saat ini untuk referensi 
                'expired_date' => $o->expired_date // Tambahkan tanggal kadaluarsa ke keranjang
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
            $newQty = (int) $r->qty;
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
    

    // Helper function untuk memvalidasi dan mengunci harga/stok di keranjang
    private function validateCart(&$cart)
    {
        foreach ($cart as $kode => &$item) {
            $o = Obat::where('kode', $kode)->first();
            if (!$o || Carbon::parse($o->expired_date)->isPast()) {
                unset($cart[$kode]); // Hapus item jika obat tidak ditemukan atau sudah kadaluarsa
                continue;
            }
            // Kunci harga dan kategori ke harga jual/kategori terbaru dari DB
            $item['harga'] = $o->harga_jual;
            $item['kategori'] = $o->kategori; // Perbaikan: Pastikan kategori selalu sinkron
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
