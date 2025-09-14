<?php

namespace App\Http\Controllers;

use App\Models\SuratPesanan;
use App\Models\SuratPesananDetail;
use App\Models\Supplier;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratPesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suratPesanans = SuratPesanan::with('supplier', 'user')->latest()->paginate(10);
        return view('transaksi.surat_pesanan.index', compact('suratPesanans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $obats = Obat::all(); // Untuk daftar obat yang bisa dipilih
        $noSp = $this->generateNoSp();
        return view('transaksi.surat_pesanan.create', compact('suppliers', 'obats', 'noSp'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp',
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'obat_id' => 'required|array',
            'obat_id.*' => 'exists:obat,id',
            'qty_pesan' => 'required|array',
            'qty_pesan.*' => 'required|integer|min:1',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'required|numeric|min:0',
            'file_template' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $suratPesanan = SuratPesanan::create([
            'no_sp' => $request->no_sp,
            'tanggal_sp' => $request->tanggal_sp,
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        // Upload file template jika ada
        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('surat_pesanan_templates', 'public');
            $suratPesanan->file_template = $path;
            $suratPesanan->save();
        }

        foreach ($request->obat_id as $key => $obatId) {
            SuratPesananDetail::create([
                'surat_pesanan_id' => $suratPesanan->id,
                'obat_id' => $obatId,
                'qty_pesan' => $request->qty_pesan[$key],
                'harga_satuan' => $request->harga_satuan[$key],
            ]);
        }

        return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratPesanan $suratPesanan)
    {
        $suratPesanan->load('supplier', 'user', 'details.obat');
        return view('transaksi.surat_pesanan.show', compact('suratPesanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratPesanan $suratPesanan)
    {
        $suppliers = Supplier::all();
        $obats = Obat::all();
        $suratPesanan->load('details.obat');
        return view('transaksi.surat_pesanan.edit', compact('suratPesanan', 'suppliers', 'obats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPesanan $suratPesanan)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp,' . $suratPesanan->id,
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'obat_id' => 'required|array',
            'obat_id.*' => 'exists:obat,id',
            'qty_pesan' => 'required|array',
            'qty_pesan.*' => 'required|integer|min:1',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'required|numeric|min:0',
            'file_template' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,parsial,selesai,dibatalkan',
        ]);

        $suratPesanan->update([
            'no_sp' => $request->no_sp,
            'tanggal_sp' => $request->tanggal_sp,
            'supplier_id' => $request->supplier_id,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
        ]);

        // Update file template jika ada
        if ($request->hasFile('file_template')) {
            // Hapus file lama jika ada
            if ($suratPesanan->file_template) {
                Storage::disk('public')->delete($suratPesanan->file_template);
            }
            $path = $request->file('file_template')->store('surat_pesanan_templates', 'public');
            $suratPesanan->file_template = $path;
            $suratPesanan->save();
        }

        // Update details
        $suratPesanan->details()->delete(); // Hapus semua detail lama
        foreach ($request->obat_id as $key => $obatId) {
            SuratPesananDetail::create([
                'surat_pesanan_id' => $suratPesanan->id,
                'obat_id' => $obatId,
                'qty_pesan' => $request->qty_pesan[$key],
                'harga_satuan' => $request->harga_satuan[$key],
                // qty_terima tidak diupdate di sini, hanya saat pembelian
            ]);
        }

        return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratPesanan $suratPesanan)
    {
        // Hapus file template jika ada
        if ($suratPesanan->file_template) {
            Storage::disk('public')->delete($suratPesanan->file_template);
        }
        $suratPesanan->delete();
        return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil dihapus.');
    }

    /**
     * Download the specified file template.
     */
    public function downloadTemplate(SuratPesanan $suratPesanan)
    {
        if ($suratPesanan->file_template && Storage::disk('public')->exists($suratPesanan->file_template)) {
            return Storage::disk('public')->download($suratPesanan->file_template);
        }
        return redirect()->back()->with('error', 'File template tidak ditemukan.');
    }

    /**
     * Generate unique No SP.
     */
    private function generateNoSp()
    {
        $latestSp = SuratPesanan::latest()->first();
        $lastNumber = $latestSp ? (int) Str::substr($latestSp->no_sp, 3) : 0;
        $newNumber = $lastNumber + 1;
        return 'SP-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get SP details by ID for AJAX.
     */
    public function getSpDetails($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($id);
        if (!$suratPesanan) {
            return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        }
        return response()->json($suratPesanan);
    }
}

