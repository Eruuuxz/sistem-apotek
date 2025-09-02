<?php

namespace App\Http\Controllers;

use App\Models\{Penjualan, PenjualanDetail, Obat}; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class PenjualanController extends Controller
{
    public function index()
    {
        // Mengambil semua penjualan dengan paginasi (untuk admin atau tampilan umum)
        $data = Penjualan::latest()->paginate(12);
        return view('kasir.riwayat', compact('data'));
    }

    public function checkout(Request $r)
    {
        $r->validate([
            'bayar'   => ['required', 'numeric', 'min:0'] 
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong, tidak ada yang bisa di-checkout.');
        }

        $total = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        if ($r->bayar < $total) {
            return back()->with('error', 'Pembayaran kurang dari total belanja.');
        }

        $penjualan = null;

        DB::transaction(function () use ($cart, $total, $r, &$penjualan) {
            $no = 'PJ-' . date('Ymd') . '-' . str_pad((Penjualan::whereDate('tanggal', date('Y-m-d'))->count() + 1), 3, '0', STR_PAD_LEFT);
            $kembalian = $r->bayar - $total; 

            $penjualan = Penjualan::create([
                'no_nota'   => $no,
                'tanggal'   => now()->toDateString(),
                // Gunakan user_id jika Anda mengubah migrasi
                'user_id'   => Auth::id(), // Simpan ID pengguna yang sedang login
                // 'kasir_nama' => Auth::user()->name, // Hapus atau komentari ini jika menggunakan user_id
                'total'   => $total,
                'bayar'   => $r->bayar, 
                'kembalian' => $kembalian, 
            ]);

            foreach ($cart as $item) {
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id'   => Obat::where('kode', $item['kode'])->value('id'), 
                    'qty'   => $item['qty'],
                    'harga'   => $item['harga'],
                    'subtotal'   => $item['qty'] * $item['harga'],
                ]);

                Obat::where('kode', $item['kode'])->decrement('stok', $item['qty']); 
            }
        });

        session()->forget('cart');

        return redirect()->route('penjualan.success', $penjualan->id);
    }

    public function show($id)
    {
        $p = Penjualan::with('detail.obat', 'kasir')->findOrFail($id); // Tambahkan 'kasir' untuk memuat relasi
        return view('kasir.detail', compact('p'));
    }

    public function riwayatKasir()
    {
        // Filter berdasarkan user_id yang sedang login
        $data = Penjualan::where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('kasir.riwayat', compact('data'));
    }

    public function success($id)
    {
        $penjualan = Penjualan::with('detail.obat', 'kasir')->findOrFail($id); // Tambahkan 'kasir'
        return view('kasir.success', compact('penjualan'));
    }

    public function struk($id)
    {
        $penjualan = Penjualan::with('detail.obat', 'kasir')->findOrFail($id); // Tambahkan 'kasir'
        return view('kasir.struk', compact('penjualan'));
    }

    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('detail.obat', 'kasir')->findOrFail($id); // Tambahkan 'kasir'
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'));
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }
}
