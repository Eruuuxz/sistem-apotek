<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\Supplier;
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
        $data = Pembelian::with('supplier', 'suratPesanan')
            ->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);
        return view('transaksi.pembelian.index', compact('data'));
    }

    public function create()
    {
        // This method might not be used frequently with the new flow, but keep it for manual entries.
        $suppliers = Supplier::orderBy('nama')->get();
        $obat = Obat::orderBy('nama')->get();
        $noFaktur = 'FPB-' . date('Y') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
        return view('transaksi.pembelian.create', compact('suppliers', 'obat', 'noFaktur'));
    }

    public function store(Request $request)
    {
        // Logic for manual purchase creation
        // ... (existing store logic)
    }

    public function faktur($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        return view('transaksi.pembelian.faktur', compact('p'));
    }

    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        $pdf = \PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('detail.obat', 'supplier', 'suratPesanan');
        return view('transaksi.pembelian.edit', compact('pembelian'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'no_faktur_pbf' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.pembelian_detail_id' => 'required|exists:pembelian_detail,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.no_batch' => 'required|string|max:255',
            'items.*.expired_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $pembelian) {
            $totalPembelian = 0;

            foreach ($request->items as $itemData) {
                $detail = PembelianDetail::find($itemData['pembelian_detail_id']);
                $obat = Obat::find($detail->obat_id);

                if (!$detail || !$obat) continue;

                $jumlah = (int)$itemData['jumlah'];
                $harga_beli = (float)$itemData['harga_beli'];
                $totalPembelian += $jumlah * $harga_beli;

                $detail->update([
                    'jumlah' => $jumlah,
                    'harga_beli' => $harga_beli,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date']
                ]);

                BatchObat::create([
                    'obat_id' => $obat->id,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'stok_awal' => $jumlah,
                    'stok_saat_ini' => $jumlah,
                    'harga_beli_per_unit' => $harga_beli,
                    'supplier_id' => $pembelian->supplier_id,
                ]);

                $obat->increment('stok', $jumlah);
            }

            $pembelian->update([
                'no_faktur_pbf' => $request->no_faktur_pbf,
                'tanggal' => $request->tanggal,
                'total' => $totalPembelian,
                'status' => 'final',
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil difinalisasi dan stok telah diperbarui.');
    }

    public function getObatBySupplier($supplierId)
    {
        $obat = Obat::where('supplier_id', $supplierId)
            ->get(['id', 'kode', 'nama', 'stok', 'harga_dasar', 'ppn_rate', 'ppn_included', 'sediaan', 'satuan_terkecil']);
        return response()->json($obat);
    }
}