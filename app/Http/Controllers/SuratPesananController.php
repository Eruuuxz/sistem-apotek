<?php

namespace App\Http\Controllers;

use App\Models\SuratPesanan;
use App\Models\SuratPesananDetail;
use App\Models\Supplier;
use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'jenis_sp' => 'required|in:reguler,prekursor',
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
                'status' => 'pending', // Status awal SP
            ]);

            // Simpan detail
            if ($request->sp_mode === 'dropdown') {
                foreach ($request->obat_id as $key => $obatId) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'obat_id' => $obatId,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                        'qty_terima' => 0, // Awalnya 0
                    ]);
                }
            } elseif ($request->sp_mode === 'manual') {
                foreach ($request->obat_manual as $key => $namaManual) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'nama_manual' => $namaManual,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                        'qty_terima' => 0, // Awalnya 0
                    ]);
                }
            }
            DB::commit();

            return redirect()->back()->with([
                'success' => 'Surat Pesanan berhasil dibuat!',
                'sp_id' => $suratPesanan->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat Surat Pesanan: ' . $e->getMessage());
        }
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
        $validatedData = $request->validate([
            'no_sp' => 'required|unique:surat_pesanan,no_sp,' . $suratPesanan->id,
            'tanggal_sp' => 'required|date_format:Y-m-d\TH:i',
            'supplier_id' => 'required|exists:supplier,id',
            'sp_mode' => 'required|in:dropdown,manual,blank',
            'jenis_sp' => 'required|in:reguler,prekursor', // Tambahkan validasi jenis_sp
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

        DB::beginTransaction();
        try {
            $suratPesanan->update([
                'no_sp' => $request->no_sp,
                'tanggal_sp' => $request->tanggal_sp,
                'supplier_id' => $request->supplier_id,
                'keterangan' => $request->keterangan,
                'sp_mode' => $request->sp_mode,
                'jenis_sp' => $request->jenis_sp, // Update jenis_sp
                'status' => $request->status,
            ]);

            // Hapus detail lama dan buat baru
            $suratPesanan->details()->delete();
            if ($request->sp_mode === 'dropdown') {
                foreach ($request->obat_id as $key => $obatId) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'obat_id' => $obatId,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                        'qty_terima' => 0, // Reset qty_terima saat update
                    ]);
                }
            } elseif ($request->sp_mode === 'manual') {
                foreach ($request->obat_manual as $key => $namaManual) {
                    SuratPesananDetail::create([
                        'surat_pesanan_id' => $suratPesanan->id,
                        'nama_manual' => $namaManual,
                        'qty_pesan' => $request->qty_pesan[$key],
                        'harga_satuan' => $request->harga_satuan[$key] ?? 0,
                        'qty_terima' => 0, // Reset qty_terima saat update
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui Surat Pesanan: ' . $e->getMessage());
        }
    }

    public function destroy(SuratPesanan $suratPesanan)
    {
        DB::beginTransaction();
        try {
            // Cek apakah ada pembelian yang terkait dengan SP ini
            $relatedPembelian = Pembelian::where('surat_pesanan_id', $suratPesanan->id)->first();
            if ($relatedPembelian) {
                // Jika ada, kita tidak bisa langsung menghapus SP
                // Opsi:
                // 1. Batalkan pembelian terkait (jika statusnya masih draft)
                // 2. Hapus relasi SP dari pembelian (set surat_pesanan_id ke null)
                // 3. Beri pesan error bahwa SP tidak bisa dihapus karena sudah ada pembelian terkait
                DB::rollBack();
                return back()->with('error', 'Surat Pesanan tidak dapat dihapus karena sudah ada pembelian terkait.');
            }

            $suratPesanan->details()->delete();
            $suratPesanan->delete();
            DB::commit();
            return redirect()->route('surat_pesanan.index')->with('success', 'Surat Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Surat Pesanan: ' . $e->getMessage());
        }
    }

    public function generatePdf($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat', 'supplier', 'user')->findOrFail($id);

        $clinicData = [
            'nama' => 'Klinik SINDANG SARI',
            'alamat' => 'JL. H. Abdul Halim No. 121, Cigugur, Tangah, Kota Cimahi',
            'telepon' => '+62 811 2044 6611',
            'email' => 'kliniksindangsari@gmail.com',
            'sio' => 'SIO: 03022300571070001',
        ];

        $apotekerData = [
            'nama' => 'apt. MIRA YULIANTI, S.Farm',
            'sipa' => 'SIPA: 440/0027/SIPA/DPMPTSP/X/2024',
            'jabatan' => 'Apoteker Penanggung Jawab',
        ];

        $containsPrekursor = $suratPesanan->details->contains(function ($detail) {
            return $detail->obat && $detail->obat->is_prekursor;
        });

        $viewName = $containsPrekursor
                    ? 'transaksi.surat_pesanan.pdf_prekursor'
                    : 'transaksi.surat_pesanan.pdf_regular';

        $pdf = PDF::loadView($viewName, compact('suratPesanan', 'clinicData', 'apotekerData'));
        $pdf->setPaper('A4', 'portrait');
        $filename = 'SP_' . $suratPesanan->no_sp . '.pdf';

        return $pdf->stream($filename);
    }

    private function generateNoSp()
    {
        $latestSp = SuratPesanan::latest()->first();
        $lastNumber = $latestSp ? (int) Str::afterLast($latestSp->no_sp, '-') : 0; // Menggunakan Str::afterLast
        return 'SP-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getSpDetails($id)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($id);
        if (!$suratPesanan) return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        return response()->json($suratPesanan);
    }
}