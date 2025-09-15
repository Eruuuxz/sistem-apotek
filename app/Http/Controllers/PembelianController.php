<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\Supplier; // Import Supplier
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
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

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => ['required', 'string', Rule::unique('pembelian', 'no_faktur')],
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:supplier,id',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|integer|exists:obat,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli_satuan' => 'required|numeric|min:0',
            'items.*.no_batch' => 'nullable|string|max:255',
            'items.*.expired_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, &$pembelian) {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            $pembelian = Pembelian::create([
                'no_faktur' => $request->no_faktur,
                'tanggal' => Carbon::parse($request->tanggal)->toDateTimeString(),
                'supplier_id' => $request->supplier_id,
                'cabang_id' => $cabangId,
                'total' => 0, // update setelah loop
                'ppn_amount' => 0, // Default, akan dihitung
            ]);

            $total = 0;
            $totalPpnPembelian = 0;

            foreach ($request->items as $it) {
                $obat = Obat::findOrFail($it['obat_id']);
                $qty = (int)$it['jumlah'];
                $hargaBeli = (float)$it['harga_beli_satuan'];
                $noBatch = $it['no_batch'] ?? null;
                $expired = !empty($it['expired_date']) ? Carbon::parse($it['expired_date'])->toDateString() : null;

                // Hitung PPN untuk item pembelian
                $ppnRate = $obat->ppn_rate ?? 0;
                $ppnAmountPerItem = 0;
                if ($ppnRate > 0) {
                    if ($obat->ppn_included) {
                        // Jika harga beli sudah termasuk PPN, hitung PPN dari harga beli
                        $ppnAmountPerItem = ($hargaBeli / (1 + $ppnRate / 100)) * ($ppnRate / 100);
                    } else {
                        // Jika harga beli belum termasuk PPN, PPN ditambahkan ke harga beli
                        $ppnAmountPerItem = $hargaBeli * ($ppnRate / 100);
                    }
                }
                $totalPpnPembelian += $ppnAmountPerItem * $qty;

                // Simpan pembelian detail
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $qty,
                    'harga_beli' => $hargaBeli,
                    'no_batch' => $noBatch,
                    'expired_date' => $expired,
                    'harga_beli_satuan' => $hargaBeli,
                    'ppn_amount' => $ppnAmountPerItem, // Simpan PPN per item
                ]);

                // Buat batch_obat entry
                BatchObat::create([
                    'obat_id' => $obat->id,
                    'no_batch' => $noBatch,
                    'expired_date' => $expired,
                    'stok_awal' => $qty,
                    'stok_saat_ini' => $qty,
                    'harga_beli_per_unit' => $hargaBeli,
                    'supplier_id' => $request->supplier_id ?? null,
                ]);

                // Update stok di tabel obat (increment)
                $obat->increment('stok', $qty);
                $total += ($qty * $hargaBeli);
            }

            // Update total pembelian dan total PPN pembelian
            $pembelian->update([
                'total' => $total,
                'ppn_amount' => $totalPpnPembelian,
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian tersimpan dan batch dibuat.');
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
            'harga' => ['required', 'array', 'min:0'], // Harga bisa 0 jika ada diskon besar
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
                'ppn_amount' => 0, // Reset PPN
            ]);

            // 4. Tambahkan detail pembelian baru dan update stok
            $total = 0;
            $totalPpnPembelian = 0;
            foreach ($r->obat_id as $i => $obatId) {
                $qty = (int) $r->jumlah[$i];
                $harga = (float) $r->harga[$i];
                $sub = $qty * $harga;

                $obat = Obat::find($obatId);
                if (!$obat) {
                    throw new \Exception("Obat dengan ID {$obatId} tidak ditemukan.");
                }

                // Hitung PPN untuk item pembelian
                $ppnRate = $obat->ppn_rate ?? 0;
                $ppnAmountPerItem = 0;
                if ($ppnRate > 0) {
                    if ($obat->ppn_included) {
                        $ppnAmountPerItem = ($harga / (1 + $ppnRate / 100)) * ($ppnRate / 100);
                    } else {
                        $ppnAmountPerItem = $harga * ($ppnRate / 100);
                    }
                }
                $totalPpnPembelian += $ppnAmountPerItem * $qty;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obatId,
                    'jumlah' => $qty,
                    'harga_beli' => $harga,
                    'ppn_amount' => $ppnAmountPerItem, // Simpan PPN per item
                ]);

                if ($obat) {
                    $obat->increment('stok', $qty);
                }
                $total += $sub;
            }

            $pembelian->update([
                'total' => $total,
                'ppn_amount' => $totalPpnPembelian,
            ]);
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
