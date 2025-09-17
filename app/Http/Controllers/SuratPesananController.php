<?php

namespace App\Http\Controllers;

use App\Models\SuratPesanan;
use App\Models\SuratPesananDetail;
use App\Models\Supplier;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PDF;

class SuratPesananController extends Controller
{
    public function index()
    {
        $suratPesanans = SuratPesanan::with('supplier', 'user')->latest()->paginate(10);
        return view('transaksi.surat_pesanan.index', compact('suratPesanans'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $obats = Obat::all();
        $noSp = $this->generateNoSp();
        return view('transaksi.surat_pesanan.create', compact('suppliers', 'obats', 'noSp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp',
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'sp_mode' => 'required|in:dropdown,manual,blank',
            'obat_id' => 'required_if:sp_mode,dropdown|array',
            'obat_id.*' => 'exists:obat,id',
            'obat_manual' => 'required_if:sp_mode,manual|array',
            'obat_manual.*' => 'required_if:sp_mode,manual|string',
            'qty_pesan' => 'required_if:sp_mode,dropdown,manual|array',
            'qty_pesan.*' => 'required_if:sp_mode,dropdown,manual|integer|min:1',
            'harga_satuan' => 'required_if:sp_mode,dropdown,manual|array',
            'harga_satuan.*' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $suratPesanan = SuratPesanan::create([
            'no_sp' => $request->no_sp,
            'tanggal_sp' => $request->tanggal_sp,
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan,
            'sp_mode' => $request->sp_mode,
        ]);

        // Simpan detail
        if ($request->sp_mode === 'dropdown') {
            foreach ($request->obat_id as $key => $obatId) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'obat_id' => $obatId,
                    'qty_pesan' => $request->qty_pesan[$key],
                    'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                ]);
            }
        } elseif ($request->sp_mode === 'manual') {
            foreach ($request->obat_manual as $key => $namaManual) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'nama_manual' => $namaManual,
                    'qty_pesan' => $request->qty_pesan[$key],
                    'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                ]);
            }
        }

        return redirect()->back()->with([
            'success' => 'Surat Pesanan berhasil dibuat!',
            'sp_id' => $suratPesanan->id
        ]);
    }

    public function show(SuratPesanan $suratPesanan)
    {
        $suratPesanan->load('supplier', 'user', 'details.obat');
        return view('transaksi.surat_pesanan.show', compact('suratPesanan'));
    }

    public function edit(SuratPesanan $suratPesanan)
    {
        $suppliers = Supplier::all();
        $obats = Obat::all();
        $suratPesanan->load('details.obat');
        return view('transaksi.surat_pesanan.edit', compact('suratPesanan', 'suppliers', 'obats'));
    }

    public function update(Request $request, SuratPesanan $suratPesanan)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp,' . $suratPesanan->id,
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'sp_mode' => 'required|in:dropdown,manual,blank',
            'obat_id' => 'required_if:sp_mode,dropdown|array',
            'obat_id.*' => 'exists:obat,id',
            'obat_manual' => 'required_if:sp_mode,manual|array',
            'obat_manual.*' => 'required_if:sp_mode,manual|string',
            'qty_pesan' => 'required_if:sp_mode,dropdown,manual|array',
            'qty_pesan.*' => 'required_if:sp_mode,dropdown,manual|integer|min:1',
            'harga_satuan' => 'required_if:sp_mode,dropdown,manual|array',
            'harga_satuan.*' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,parsial,selesai,dibatalkan',
        ]);

        $suratPesanan->update([
            'no_sp' => $request->no_sp,
            'tanggal_sp' => $request->tanggal_sp,
            'supplier_id' => $request->supplier_id,
            'keterangan' => $request->keterangan,
            'sp_mode' => $request->sp_mode,
            'status' => $request->status,
        ]);

        $suratPesanan->details()->delete();
        if ($request->sp_mode === 'dropdown') {
            foreach ($request->obat_id as $key => $obatId) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'obat_id' => $obatId,
                    'qty_pesan' => $request->qty_pesan[$key],
                    'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                ]);
            }
        } elseif ($request->sp_mode === 'manual') {
            foreach ($request->obat_manual as $key => $namaManual) {
                SuratPesananDetail::create([
                    'surat_pesanan_id' => $suratPesanan->id,
                    'nama_manual' => $namaManual,
                    'qty_pesan' => $request->qty_pesan[$key],
                    'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                ]);
            }
        }

        return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil diperbarui.');
    }

    public function destroy(SuratPesanan $suratPesanan)
    {
        $suratPesanan->details()->delete();
        $suratPesanan->delete();
        return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil dihapus.');
    }

    public function generatePdf($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat', 'supplier')->findOrFail($id);
        $pdf = PDF::loadView('transaksi.surat_pesanan.pdf', compact('suratPesanan'));
        $pdf->setPaper('A4', 'portrait');
        $filename = 'SP_'.$suratPesanan->no_sp.'.pdf';
        return $pdf->stream($filename);
    }

    private function generateNoSp()
    {
        $latestSp = SuratPesanan::latest()->first();
        $lastNumber = $latestSp ? (int) Str::substr($latestSp->no_sp, 3) : 0;
        return 'SP-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getSpDetails($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($id);
        if (!$suratPesanan) return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        return response()->json($suratPesanan);
    }
}
