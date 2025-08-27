<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, PenjualanDetail, Obat}; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class PenjualanController extends Controller
{
    public function index()
    {
        // Mengambil semua penjualan dengan paginasi
        $data = Penjualan::latest()->paginate(12);
        return view('kasir.riwayat', compact('data'));
    }

    public function checkout(Request $r)
    {
        $r->validate([
            'kasir_nama' => ['required', 'string', 'max:100'],
            'bayar'   => ['required', 'numeric', 'min:0']
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong, tidak ada yang bisa di-checkout.');
        }

        // Total dihitung server-side untuk mencegah manipulasi harga
        $total = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        if ($r->bayar < $total) {
            return back()->with('error', 'Pembayaran kurang dari total belanja.');
        }

        $penjualan = null; // Inisialisasi variabel penjualan

        DB::transaction(function () use ($cart, $total, $r, &$penjualan) {
            // Generate nomor nota unik
            $no = 'PJ-' . date('Ymd') . '-' . str_pad((Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1), 3, '0', STR_PAD_LEFT);

            $penjualan = Penjualan::create([
                'no_nota'   => $no,
                'tanggal'   => now()->toDateString(),
                'kasir_nama' => $r->kasir_nama,
                'total'   => $total,
            ]);

            foreach ($cart as $item) {
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id'   => Obat::where('kode', $item['kode'])->value('id'), 
                    'qty'   => $item['qty'],
                    'harga'   => $item['harga'],
                    'subtotal'   => $item['qty'] * $item['harga'],
                ]);

                // Stok obat berkurang
                Obat::where('kode', $item['kode'])->decrement('stok', $item['qty']); 
            }
        });

        session()->forget('cart'); // Kosongkan keranjang setelah checkout berhasil

        return redirect()->route('penjualan.show', $penjualan->id)->with('success', 'Transaksi berhasil disimpan!');
    }

    public function show($id)
    {
        $p = Penjualan::with('detail.obat')->findOrFail($id); // Ubah detail.barang menjadi detail.obat
        return view('kasir.detail', compact('p'));
    }

    public function struk($id)
    {
        $p = Penjualan::with('detail.obat')->findOrFail($id); // Ubah detail.barang menjadi detail.obat
        return view('kasir.struk', compact('p'));
    }
}
