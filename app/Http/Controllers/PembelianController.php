<?php

namespace App\Http\Controllers;

use App\Models\{Pembelian, PembelianDetail, Supplier, Obat, Cabang};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Tambahkan ini
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
public function index()
    {
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $data = Pembelian::with('supplier')
            ->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);

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
            'no_faktur' => ['required', 'max:50', Rule::unique('pembelian', 'no_faktur')],
            'tanggal' => ['required', 'date'], // Validasi tetap 'date' karena input HTML type="date"
            'supplier_id' => ['required', 'exists:supplier,id'],
            'obat_id' => ['required', 'array', 'min:1'],
            'obat_id.*' => ['required', 'exists:obat,id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
            'harga.*' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($r, &$pembelian) {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;
            
            $pembelian = Pembelian::create([
            'no_faktur' => $r->no_faktur,
            'tanggal' => Carbon::parse($r->tanggal)->toDateTimeString(),
            'supplier_id' => $r->supplier_id,
            'cabang_id' => $cabangId,
            'total' => 0,
        ]);

            $total = 0;
            foreach ($r->obat_id as $i => $obatId) {
                $qty = (int) $r->jumlah[$i];
                $harga = (float) $r->harga[$i];
                $sub = $qty * $harga;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obatId,
                    'jumlah' => $qty,
                    'harga_beli' => $harga, // Sesuaikan dengan nama kolom di migration
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

    // app/Http/Controllers/PembelianController.php

    // ... (bagian lain dari kode)

    public function edit(Pembelian $pembelian)
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obat = Obat::orderBy('nama')->get();
        // Load detail pembelian bersama dengan obatnya
        $pembelian->load('detail.obat');
        return view('transaksi.pembelian.edit', compact('pembelian', 'suppliers', 'obat'));
    }

    public function update(Request $r, Pembelian $pembelian)
    {
        $r->validate([
            'no_faktur' => ['required', 'max:50', Rule::unique('pembelian', 'no_faktur')->ignore($pembelian->id)], // Ignore current ID
            'tanggal' => ['required', 'date'], // Validasi tetap 'date'
            'supplier_id' => ['required', 'exists:supplier,id'],
            'obat_id' => ['required', 'array', 'min:1'],
            'obat_id.*' => ['required', 'exists:obat,id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
            'harga.*' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($r, $pembelian) {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;
            
            // 1. Kembalikan stok obat dari detail pembelian lama
            foreach ($pembelian->detail as $detail) {
                $obat = Obat::find($detail->obat_id);
                if ($obat) {
                    $obat->decrement('stok', $detail->jumlah);
                }
            }

            // 2. Hapus detail pembelian lama
            $pembelian->detail()->delete();

            // 3. Update data pembelian utama
            $pembelian->update([
                'no_faktur' => $r->no_faktur,
                'tanggal' => Carbon::parse($r->tanggal)->toDateTimeString(),
                'supplier_id' => $r->supplier_id,
                'cabang_id' => $cabangId,
                'total' => 0,
            ]);

            // 4. Tambahkan detail pembelian baru dan update stok
            $total = 0;
            foreach ($r->obat_id as $i => $obatId) {
                $qty = (int) $r->jumlah[$i];
                $harga = (float) $r->harga[$i];
                $sub = $qty * $harga;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obatId,
                    'jumlah' => $qty,
                    'harga_beli' => $harga,
                ]);

                $obat = Obat::find($obatId);
                if ($obat) {
                    $obat->increment('stok', $qty);
                }
                $total += $sub;
            }

            $pembelian->update(['total' => $total]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui');
    }
    public function getObatBySupplier($supplierId)
    {
        // Ambil obat yang supplier_id nya sama
        $obat = Obat::where('supplier_id', $supplierId)
            ->where('stok', '>', 0) // hanya yang masih ada stok
            ->get(['id', 'kode', 'nama', 'stok', 'harga_dasar']);

        return response()->json($obat);
    }
}