<?php

namespace App\Http\Controllers;

use App\Models\SuratPesanan;
use App\Models\Supplier;
use App\Models\Obat;
use App\Services\SuratPesananService; // Import Service CRUD
use App\Services\PDF\SuratPesananPDFService; // Import Service PDF
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratPesananController extends Controller
{
    protected SuratPesananService $spService;
    protected SuratPesananPDFService $pdfService;

    // Dependency Injection
    public function __construct(SuratPesananService $spService, SuratPesananPDFService $pdfService)
    {
        $this->spService = $spService;
        $this->pdfService = $pdfService;
    }

    /**
     * Mengalihkan ke halaman indeks pembelian yang terintegrasi.
     */
    public function index()
    {
        return redirect()->route('pembelian.index');
    }

    /**
     * Menampilkan form untuk membuat Surat Pesanan baru.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $obats = Obat::all(); 
        $noSp = $this->spService->generateNoSp();
        return view('admin.Transaksi.surat_pesanan.create', compact('suppliers', 'obats', 'noSp'));
    }

    /**
     * Menyimpan Surat Pesanan yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp',
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'sp_mode' => 'required|in:dropdown,manual,blank',
            'jenis_sp' => 'required|in:reguler,prekursor',
            'obat_id' => 'required_if:sp_mode,dropdown|array',
            'obat_id.*' => 'exists:obat,id',
            'obat_manual' => 'required_if:sp_mode,manual|array',
            'obat_manual.*' => 'required_if:sp_mode,manual|string',
            'qty_pesan' => 'required_if:sp_mode,dropdown,manual|array',
            'qty_pesan.*' => 'required_if:sp_mode,dropdown,manual|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $this->spService->createSuratPesanan($request->all());

            return redirect()->route('pembelian.index')->with([
                'success' => 'Surat Pesanan berhasil dibuat dan menunggu untuk diproses.',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail dari Surat Pesanan.
     */
    public function show(SuratPesanan $suratPesanan)
    {
        $suratPesanan->load('supplier', 'user', 'details.obat');
        return view('admin.Transaksi.pembelian.surat_pesanan.show', compact('suratPesanan'));
    }

    /**
     * Menampilkan form untuk mengedit Surat Pesanan.
     */
    public function edit(SuratPesanan $suratPesanan)
    {
        $suppliers = Supplier::all();
        $obats = Obat::all();
        $suratPesanan->load('details.obat');
        return view('admin.Transaksi.surat_pesanan.edit', compact('suratPesanan', 'suppliers', 'obats'));
    }

    /**
     * Memperbarui Surat Pesanan di database.
     */
    public function update(Request $request, SuratPesanan $suratPesanan)
    {
        $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp,' . $suratPesanan->id,
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'sp_mode' => 'required|in:dropdown,manual,blank',
            'jenis_sp' => 'required|in:reguler,prekursor',
            'obat_id' => 'required_if:sp_mode,dropdown|array',
            'obat_id.*' => 'exists:obat,id',
            'obat_manual' => 'required_if:sp_mode,manual|array',
            'obat_manual.*' => 'required_if:sp_mode,manual|string',
            'qty_pesan' => 'required_if:sp_mode,dropdown,manual|array',
            'qty_pesan.*' => 'required_if:sp_mode,dropdown,manual|integer|min:1',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,parsial,selesai,dibatalkan',
        ]);

        try {
            $this->spService->updateSuratPesanan($suratPesanan, $request->only([
                'no_sp', 'tanggal_sp', 'supplier_id', 'keterangan', 'sp_mode', 'jenis_sp', 'status',
                // Masukkan juga detail fields agar bisa diproses di service
                'obat_id', 'obat_manual', 'qty_pesan'
            ]));

            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Surat Pesanan dari database.
     */
    public function destroy(SuratPesanan $suratPesanan)
    {
        try {
            $this->spService->destroySuratPesanan($suratPesanan);
            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Membuat file PDF dari Surat Pesanan (Delegate ke PDF Service).
     */
    public function generatePdf($id)
    {
        try {
            return $this->pdfService->generatePDF($id);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat PDF Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil detail SP untuk keperluan internal (misalnya AJAX).
     */
    public function getSpDetails($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($id);
        if (!$suratPesanan) return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        return response()->json($suratPesanan);
    }

    /**
     * Mengambil daftar obat berdasarkan supplier yang dipilih.
     */
    public function getObatBySupplier(Supplier $supplier)
    {
        $obats = $this->spService->getObatBySupplier($supplier);
        return response()->json($obats);
    }
}