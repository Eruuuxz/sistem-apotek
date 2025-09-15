<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stockOpnames = StockOpname::with('user')->latest()->paginate(10);
        return view('stock_opname.index', compact('stockOpnames'));
    }

    public function create()
    {
        $obats = Obat::all();
        return view('stock_opname.create', compact('obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_so' => 'required|date',
            'catatan' => 'nullable|string',
            'obat_id.*' => 'required|exists:obats,id',
            'stok_fisik.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $stockOpname = StockOpname::create([
                'user_id' => Auth::id(),
                'tanggal_so' => $request->tanggal_so,
                'catatan' => $request->catatan,
                'status' => 'pending', // Menunggu persetujuan admin
            ]);

            foreach ($request->obat_id as $index => $obatId) {
                $obat = Obat::find($obatId);
                $stokSistem = $obat->stok;
                $stokFisik = $request->stok_fisik[$index];
                $selisih = $stokFisik - $stokSistem;
                $tipePenyesuaian = 'tidak_ada';
                if ($selisih > 0) {
                    $tipePenyesuaian = 'penambahan';
                } elseif ($selisih < 0) {
                    $tipePenyesuaian = 'pengurangan';
                }

                StockOpnameDetail::create([
                    'stock_opname_id' => $stockOpname->id,
                    'obat_id' => $obatId,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'tipe_penyesuaian' => $tipePenyesuaian,
                    'catatan_detail' => $request->catatan_detail[$index] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('stock_opname.index')->with('success', 'Stock Opname berhasil dibuat dan menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat Stock Opname: ' . $e->getMessage());
        }
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load('user', 'details.obat');
        return view('stock_opname.show', compact('stockOpname'));
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock Opname ini sudah tidak dalam status pending.');
        }

        DB::beginTransaction();
        try {
            foreach ($stockOpname->details as $detail) {
                $obat = $detail->obat;
                $obat->stok = $detail->stok_fisik; // Update stok dengan stok fisik
                $obat->save();
            }
            $stockOpname->status = 'approved';
            $stockOpname->save();

            DB::commit();
            return redirect()->route('stock_opname.index')->with('success', 'Stock Opname berhasil disetujui dan stok diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui Stock Opname: ' . $e->getMessage());
        }
    }

    public function reject(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending') {
            return back()->with('error', 'Stock Opname ini sudah tidak dalam status pending.');
        }

        $stockOpname->status = 'rejected';
        $stockOpname->save();

        return redirect()->route('stock_opname.index')->with('success', 'Stock Opname berhasil ditolak.');
    }

    public function generatePdf(StockOpname $stockOpname)
    {
        $stockOpname->load('user', 'details.obat');
        $pdf = PDF::loadView('stock_opname.pdf', compact('stockOpname'));
        return $pdf->download('laporan_stock_opname_' . $stockOpname->id . '.pdf');
    }
}