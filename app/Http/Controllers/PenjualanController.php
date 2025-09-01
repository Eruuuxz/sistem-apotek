<?php
// File: /app/Http/Controllers/PenjualanController.php

namespace App\Http\Controllers;

use App\Models\{Penjualan, PenjualanDetail, Obat}; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Pdf;

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
            // Hapus validasi 'kasir_nama' karena akan diisi otomatis
            // 'kasir_nama' => ['required', 'string', 'max:100'], 
            'bayar'   => ['required', 'numeric', 'min:0'] 
            // Hapus validasi 'kasir_nama' karena akan diisi otomatis
            // 'kasir_nama' => ['required', 'string', 'max:100'], 
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

            // Hitung kembalian
            $kembalian = $r->bayar - $total; 

            // Hitung kembalian
            $kembalian = $r->bayar - $total; 

            $penjualan = Penjualan::create([
                'no_nota'   => $no,
                'tanggal'   => now()->toDateString(),
                'kasir_nama' => auth()->user()->name, // Ambil nama kasir dari user yang sedang login
                'kasir_nama' => auth()->user()->name, // Ambil nama kasir dari user yang sedang login
                'total'   => $total,
                'bayar'   => $r->bayar, 
                'kembalian' => $kembalian, 
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

                // Stok obat berkurang
                Obat::where('kode', $item['kode'])->decrement('stok', $item['qty']); 
            }
        });

        session()->forget('cart'); // Kosongkan keranjang setelah checkout berhasil

        return redirect()->route('penjualan.struk.pdf', $penjualan->id);


    }

    public function show($id)
    {
        $p = Penjualan::with('detail.obat')->findOrFail($id); 
        $p = Penjualan::with('detail.obat')->findOrFail($id); 
        return view('kasir.detail', compact('p'));
    }

    public function struk($id)
    {
        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);
        return view('kasir.struk', compact('penjualan'));
    }

    public function strukPdf($id)
    {
        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);
    {
        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);

        // Load view yang sama dengan struk.blade.php
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'));
        // Load view yang sama dengan struk.blade.php
        $pdf = Pdf::loadView('kasir.struk', compact('penjualan'));

        // Stream ke browser
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }
        // Stream ke browser
        return $pdf->stream('faktur-' . $penjualan->no_nota . '.pdf');
    }
}
