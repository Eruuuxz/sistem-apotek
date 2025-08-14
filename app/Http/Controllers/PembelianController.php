<?php

namespace App\Http\Controllers;

use App\Models\{Pembelian, PembelianDetail, Supplier, Obat}; // Tambahkan PembelianDetail, Supplier, Obat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Illuminate\Validation\Rule;   // Tambahkan ini

class PembelianController extends Controller
{
    public function index()
    {
        // Ambil semua pembelian dengan supplier & detail barangnya
        // Menggunakan paginasi dan relasi supplier
        $data = Pembelian::with('supplier')->latest()->paginate(10);
        return view('transaksi.pembelian.index', compact('data'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get(); // Ambil semua supplier untuk dropdown
        $obat = Obat::orderBy('nama')->get();         // Ambil semua obat untuk dropdown
        
        // no_faktur auto-suggest
        $noFaktur = 'FPB-' . date('Y') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
        
        return view('transaksi.pembelian.create', compact('suppliers', 'obat', 'noFaktur'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'no_faktur'   => ['required', 'max:50', Rule::unique('pembelian', 'no_faktur')],
            'tanggal'   => ['required', 'date'],
            'supplier_id' => ['required', 'exists:supplier,id'],
            'obat_id'   => ['required', 'array', 'min:1'],
            'obat_id.*'   => ['required', 'exists:obat,id'],
            'jumlah'   => ['required', 'array', 'min:1'],
            'jumlah.*'   => ['required', 'integer', 'min:1'],
            'harga'   => ['required', 'array', 'min:1'],
            'harga.*'   => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($r) {
            $pembelian = Pembelian::create([
                'no_faktur'   => $r->no_faktur,
                'tanggal'   => $r->tanggal,
                'supplier_id' => $r->supplier_id,
                'total'   => 0, // Akan diupdate setelah detail ditambahkan
            ]);

            $total = 0;
            foreach ($r->obat_id as $i => $obatId) {
                $qty   = (int)$r->jumlah[$i];
                $harga = (float)$r->harga[$i];
                $sub   = $qty * $harga;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id'   => $obatId,
                    'jumlah'   => $qty,
                    'harga_beli'   => $harga, // Sesuaikan dengan nama kolom di migration
                ]);

                // stok obat bertambah
                $obat = Obat::find($obatId);
                $obat->increment('stok', $qty);
                $total += $sub;
            }

            $pembelian->update(['total' => $total]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian tersimpan');
    }

    public function faktur($id)
    {
        // Ambil pembelian beserta supplier dan detail obatnya
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        return view('transaksi.pembelian.faktur', compact('p'));
    }

    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        // Pastikan Anda sudah menginstal barryvdh/laravel-dompdf
        // composer require barryvdh/laravel-dompdf
        $pdf = \PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }
}