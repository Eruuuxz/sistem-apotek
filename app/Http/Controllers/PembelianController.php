<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\SuratPesanan;
use App\Models\Cabang;
use App\Services\PembelianService; // Import Service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;

class PembelianController extends Controller
{
    protected PembelianService $pembelianService;

    // Dependency Injection
    public function __construct(PembelianService $pembelianService)
    {
        $this->pembelianService = $pembelianService;
    }

    public function index(Request $request)
    {
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $suratPesanans = SuratPesanan::with('supplier', 'user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $query = Pembelian::with('supplier', 'suratPesanan', 'cabang');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembelians = $query->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);
        
        return view('admin.Transaksi.pembelian.index', compact('pembelians', 'suratPesanans'));
    }

    /**
     * Membuat data pembelian draft dari Surat Pesanan.
     */
    public function createFromSp(SuratPesanan $suratPesanan)
    {
        try {
            $this->pembelianService->processFromSuratPesanan($suratPesanan);
            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil diproses menjadi draft pembelian. Silakan lengkapi No. Faktur PBF dan detail batch.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses Surat Pesanan: ' . $e->getMessage());
        }
    }


    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get();
        // Generate No. Faktur tetap di Controller (atau Helper) karena ini lebih ke tampilan awal
        $noFaktur = 'FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
        $suratPesanans = SuratPesanan::where('status', 'pending')->get();
        return view('admin.pembelian.create', compact('suppliers', 'obats', 'noFaktur', 'suratPesanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'tanggal' => 'required|date',
            'surat_pesanan_id' => 'nullable|exists:surat_pesanan,id',
            'no_faktur_pbf' => 'nullable|string|max:255',
            'diskon' => 'nullable|numeric|min:0',
            'diskon_type' => 'nullable|in:nominal,persen',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            $pembelian = $this->pembelianService->createDraft($request->all(), $request->no_faktur);
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dibuat sebagai draft. Mohon finalisasi untuk memperbarui stok.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat pembelian: ' . $e->getMessage());
        }
    }

    public function faktur($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat', 'cabang'])->findOrFail($id);
        return view('admin.Transaksi.pembelian.faktur', compact('p'));
    }

    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat', 'cabang'])->findOrFail($id);
        // Menggunakan DomPDF
        $pdf = PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('detail.obat', 'supplier', 'suratPesanan', 'cabang');
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get();
        return view('admin.Transaksi.pembelian.edit', compact('pembelian', 'suppliers', 'obats'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'no_faktur_pbf' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'diskon' => 'nullable|numeric|min:0',
            'diskon_type' => 'nullable|in:nominal,persen',
            'items' => 'required|array|min:1',
            'items.*.pembelian_detail_id' => 'required|exists:pembelian_detail,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.no_batch' => 'required|string|max:255',
            'items.*.expired_date' => 'required|date|after:today',
        ]);

        try {
            $this->pembelianService->finalizePembelian($pembelian, $request->all());
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil difinalisasi dan stok telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembelian: ' . $e->getMessage());
        }
    }

    public function destroy(Pembelian $pembelian)
    {
        try {
            $this->pembelianService->destroyPembelian($pembelian);
            $message = $pembelian->status === 'final' ? 'Pembelian dan stok terkait berhasil dihapus.' : 'Pembelian draft berhasil dihapus.';
            return redirect()->route('pembelian.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }

    public function getObatBySupplier($supplierId)
    {
        try {
            $obat = $this->pembelianService->getAvailableObatBySupplier((int) $supplierId);
            if ($obat->isEmpty()) {
                // Sesuai dengan response sebelumnya, berikan pesan jika kosong
                return response()->json(['message' => 'Tidak ada obat tersedia untuk supplier ini atau stok habis/kadaluarsa.'], 200);
            }
            return response()->json($obat);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data obat.'], 500);
        }
    }

    public function getSuratPesananDetails($suratPesananId)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($suratPesananId);
        if (!$suratPesanan) {
            return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        }
        
        $items = $suratPesanan->details->map(function ($detail) {
            $obat = $detail->obat;
            // Handle jika mode SP manual dan obat_id null
            if (!$obat && $detail->nama_manual) {
                // Jika manual, tidak bisa mendapatkan data obat (kode, ppn_rate)
                return [
                    'obat_id' => null,
                    'kode' => $detail->nama_manual,
                    'nama' => $detail->nama_manual,
                    'jumlah' => $detail->qty_pesan,
                    'harga_beli' => $detail->harga_satuan,
                    'ppn_rate' => 0,
                    'ppn_included' => false,
                ];
            } elseif (!$obat) {
                return null;
            }

            return [
                'obat_id' => $obat->id,
                'kode' => $obat->kode,
                'nama' => $obat->nama,
                'jumlah' => $detail->qty_pesan,
                'harga_beli' => $detail->harga_satuan,
                'ppn_rate' => $obat->ppn_rate,
                'ppn_included' => $obat->ppn_included,
            ];
        })->filter()->values(); // Filter nulls dan reset keys

        return response()->json([
            'supplier_id' => $suratPesanan->supplier_id,
            'items' => $items,
        ]);
    }
}