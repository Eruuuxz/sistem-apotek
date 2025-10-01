<?php

namespace App\Http\Controllers;

use App\Models\SuratPesanan;
use App\Models\SuratPesananDetail;
use App\Models\Supplier;
use App\Models\Obat;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;

class SuratPesananController extends Controller
{
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
        $obats = Obat::all(); // Dipertahankan untuk fallback jika diperlukan
        $noSp = $this->generateNoSp();
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

        DB::beginTransaction();
        try {
            $suratPesanan = SuratPesanan::create([
                'no_sp' => $request->no_sp,
                'tanggal_sp' => $request->tanggal_sp,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
                'sp_mode' => $request->sp_mode,
                'jenis_sp' => $request->jenis_sp,
                'status' => 'pending',
            ]);

            if ($request->sp_mode === 'dropdown') {
                foreach ($request->obat_id as $key => $obatId) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'obat_id' => $obatId,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'qty_terima' => 0,
                    ]);
                }
            } elseif ($request->sp_mode === 'manual') {
                foreach ($request->obat_manual as $key => $namaManual) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'nama_manual' => $namaManual,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'qty_terima' => 0,
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('pembelian.index')->with([
                'success' => 'Surat Pesanan berhasil dibuat dan menunggu untuk diproses.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

        DB::beginTransaction();
        try {
            $suratPesanan->update($request->only(['no_sp', 'tanggal_sp', 'supplier_id', 'keterangan', 'sp_mode', 'jenis_sp', 'status']));

            $suratPesanan->details()->delete();
            if ($request->sp_mode === 'dropdown') {
                foreach ($request->obat_id as $key => $obatId) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'obat_id' => $obatId,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'qty_terima' => 0,
                    ]);
                }
            } elseif ($request->sp_mode === 'manual') {
                foreach ($request->obat_manual as $key => $namaManual) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'nama_manual' => $namaManual,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'qty_terima' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Surat Pesanan dari database.
     */
    public function destroy(SuratPesanan $suratPesanan)
    {
        DB::beginTransaction();
        try {
            if ($suratPesanan->pembelian()->exists()) {
                DB::rollBack();
                return redirect()->route('pembelian.index')->with('error', 'Surat Pesanan tidak dapat dihapus karena sudah ada pembelian terkait.');
            }

            $suratPesanan->details()->delete();
            $suratPesanan->delete();
            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Surat Pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Membuat file PDF dari Surat Pesanan.
     */
    public function generatePdf($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat', 'supplier', 'user')->findOrFail($id);
        $clinicData = [
            'nama' => 'Apotek Liz Farma 02',
            'alamat' => 'Jl. Raya Batujajar No.321, Batujajar Bar., Kec. Batujajar, Kabupaten Bandung, Jawa Barat 40561',
            'telepon' => '+62 22 86674232',
            'email' => 'kliniksindangsari@gmail.com',
            'sio' => 'SIO: 03022300571070001',
        ];
        $apotekerData = [
            'nama' => 'apt. MINA YULIANTI, S.Farm',
            'sipa' => 'SIPA: 440/0027/SIPA/DPMPTSP/X/2024',
            'jabatan' => 'Apoteker Penanggung Jawab',
        ];
        $containsPrekursor = $suratPesanan->details->contains(fn($detail) => $detail->obat && $detail->obat->is_prekursor);
        
        $viewName = $containsPrekursor
                    ? 'admin.Transaksi.surat_pesanan.pdf_prekursor'
                    : 'admin.Transaksi.surat_pesanan.pdf_regular';

        $pdf = PDF::loadView($viewName, compact('suratPesanan', 'clinicData', 'apotekerData'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('SP_' . $suratPesanan->no_sp . '.pdf');
    }

    /**
     * Membuat nomor Surat Pesanan baru secara otomatis.
     */
    private function generateNoSp()
    {
        $latestSp = SuratPesanan::latest()->first();
        $lastNumber = $latestSp ? (int) Str::afterLast($latestSp->no_sp, '-') : 0;
        return 'SP-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
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
        $obats = Obat::where('supplier_id', $supplier->id)
            ->select('id', 'nama', 'stok')
            ->get();
        return response()->json($obats);
    }
}