<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\Supplier;
use App\Models\SuratPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        // Mengambil data Surat Pesanan yang masih pending
        $suratPesanans = SuratPesanan::with('supplier', 'user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Mengambil data Pembelian yang sudah ada (draft atau final)
        $query = Pembelian::with('supplier', 'suratPesanan', 'cabang');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembelians = $query->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);
        
        // PATH FIXED: Memastikan path view sesuai dengan struktur file
        return view('admin.Transaksi.pembelian.index', compact('pembelians', 'suratPesanans'));
    }

    /**
     * Membuat data pembelian draft dari Surat Pesanan.
     */
    public function createFromSp(SuratPesanan $suratPesanan)
    {
        // Pastikan SP belum pernah diproses
        if ($suratPesanan->pembelian()->exists()) {
            return redirect()->route('pembelian.index')->with('error', 'Surat Pesanan ini sudah pernah diproses.');
        }

        DB::beginTransaction();
        try {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            // Hitung total dari detail SP
            $totalPembelian = 0;
            foreach ($suratPesanan->details as $detail) {
                // Pastikan obat ada untuk mode dropdown
                if ($suratPesanan->sp_mode == 'dropdown' && !$detail->obat_id) continue;
                $totalPembelian += $detail->qty_pesan * $detail->harga_satuan;
            }

            // 1. Buat record pembelian baru dengan status 'draft'
            $pembelian = Pembelian::create([
                'no_faktur' => 'FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT),
                'tanggal' => now(),
                'supplier_id' => $suratPesanan->supplier_id,
                'surat_pesanan_id' => $suratPesanan->id,
                'cabang_id' => $cabangId,
                'total' => $totalPembelian,
                'diskon' => 0,
                'diskon_type' => 'nominal',
                'ppn_amount' => 0, // PPN akan dihitung ulang saat finalisasi jika perlu
                'status' => 'draft',
            ]);

            // 2. Salin detail dari SP ke detail pembelian
            foreach ($suratPesanan->details as $spDetail) {
                 // Hanya proses detail yang memiliki obat_id (untuk mode dropdown)
                if ($suratPesanan->sp_mode == 'dropdown' && !$spDetail->obat_id) continue;

                $pembelian->detail()->create([
                    'obat_id' => $spDetail->obat_id, // akan null jika mode manual
                    'jumlah' => $spDetail->qty_pesan,
                    'harga_beli' => $spDetail->harga_satuan,
                    // no_batch dan expired_date akan diisi saat finalisasi
                ]);
            }

            // 3. Ubah status Surat Pesanan menjadi 'selesai'
            $suratPesanan->update(['status' => 'selesai']);

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Surat Pesanan berhasil diproses menjadi draft pembelian. Silakan lengkapi No. Faktur PBF dan detail batch.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses Surat Pesanan: ' . $e->getMessage());
        }
    }


    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get();
        $noFaktur = 'FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
        $suratPesanans = SuratPesanan::where('status', 'pending')->get();
        // PATH FIXED: Memastikan path view sesuai dengan struktur file
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

        DB::beginTransaction();
        try {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            $totalPembelian = 0;
            $totalPpn = 0;

            foreach ($request->items as $itemData) {
                $obat = Obat::find($itemData['obat_id']);
                $hargaBeli = (float)$itemData['harga_beli'];
                $jumlah = (int)$itemData['jumlah'];

                $hargaBersihPerUnit = $hargaBeli;
                $ppnPerUnit = 0;

                if ($obat->ppn_rate > 0) {
                    if ($obat->ppn_included) {
                        $hargaBersihPerUnit = $hargaBeli / (1 + $obat->ppn_rate / 100);
                        $ppnPerUnit = $hargaBeli - $hargaBersihPerUnit;
                    } else {
                        $ppnPerUnit = $hargaBeli * ($obat->ppn_rate / 100);
                    }
                }
                $totalPembelian += ($hargaBersihPerUnit + $ppnPerUnit) * $jumlah;
                $totalPpn += $ppnPerUnit * $jumlah;
            }

            $diskonAmount = 0;
            if ($request->diskon_type === 'persen') {
                $diskonAmount = $totalPembelian * ($request->diskon / 100);
            } else {
                $diskonAmount = $request->diskon;
            }
            $finalTotal = max($totalPembelian - $diskonAmount, 0);


            $pembelian = Pembelian::create([
                'no_faktur' => $request->no_faktur ?? ('FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT)),
                'no_faktur_pbf' => $request->no_faktur_pbf,
                'tanggal' => $request->tanggal,
                'supplier_id' => $request->supplier_id,
                'surat_pesanan_id' => $request->surat_pesanan_id,
                'cabang_id' => $cabangId,
                'total' => $finalTotal,
                'diskon' => $request->diskon ?? 0,
                'diskon_type' => $request->diskon_type ?? 'nominal',
                'ppn_amount' => $totalPpn,
                'status' => 'draft',
            ]);

            foreach ($request->items as $itemData) {
                $obat = Obat::find($itemData['obat_id']);
                $hargaBeli = (float)$itemData['harga_beli'];
                $jumlah = (int)$itemData['jumlah'];

                $ppnAmountPerItem = 0;
                if ($obat->ppn_rate > 0 && !$obat->ppn_included) {
                    $ppnAmountPerItem = $hargaBeli * ($obat->ppn_rate / 100);
                } elseif ($obat->ppn_rate > 0 && $obat->ppn_included) {
                    $hargaBersihPerUnit = $hargaBeli / (1 + $obat->ppn_rate / 100);
                    $ppnAmountPerItem = $hargaBeli - $hargaBersihPerUnit;
                }

                $pembelian->detail()->create([
                    'obat_id' => $itemData['obat_id'],
                    'jumlah' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'ppn_amount' => $ppnAmountPerItem * $jumlah,
                ]);
            }

            if ($request->surat_pesanan_id) {
                $suratPesanan = SuratPesanan::find($request->surat_pesanan_id);
                if ($suratPesanan) {
                    $suratPesanan->update(['status' => 'selesai']);
                }
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dibuat sebagai draft. Mohon finalisasi untuk memperbarui stok.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat pembelian: ' . $e->getMessage());
        }
    }

    public function faktur($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat', 'cabang'])->findOrFail($id);
        // PATH FIXED: Memastikan path view sesuai dengan struktur file
        return view('admin.Transaksi.pembelian.faktur', compact('p'));
    }

    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat', 'cabang'])->findOrFail($id);
        // PATH FIXED: Memastikan path view sesuai dengan struktur file
        $pdf = \PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('detail.obat', 'supplier', 'suratPesanan', 'cabang');
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get();
        // PATH FIXED: Memastikan path view sesuai dengan struktur file
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

        if ($pembelian->status === 'final') {
            return back()->with('error', 'Pembelian ini sudah difinalisasi dan stok sudah diperbarui.');
        }

        DB::transaction(function () use ($request, $pembelian) {
            $totalPembelian = 0;
            $totalPpn = 0;

            foreach ($request->items as $itemData) {
                $detail = PembelianDetail::find($itemData['pembelian_detail_id']);
                $obat = Obat::find($detail->obat_id);

                if (!$detail || !$obat) continue;

                $jumlah = (int)$itemData['jumlah'];
                $hargaBeli = (float)$itemData['harga_beli'];

                $hargaBersihPerUnit = $hargaBeli;
                $ppnPerUnit = 0;

                if ($obat->ppn_rate > 0) {
                    if ($obat->ppn_included) {
                        $hargaBersihPerUnit = $hargaBeli / (1 + $obat->ppn_rate / 100);
                        $ppnPerUnit = $hargaBeli - $hargaBersihPerUnit;
                    } else {
                        $ppnPerUnit = $hargaBeli * ($obat->ppn_rate / 100);
                    }
                }
                $totalPembelian += ($hargaBersihPerUnit + $ppnPerUnit) * $jumlah;
                $totalPpn += $ppnPerUnit * $jumlah;

                $detail->update([
                    'jumlah' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'ppn_amount' => $ppnPerUnit * $jumlah,
                ]);

                $batch = BatchObat::firstOrNew([
                    'obat_id' => $obat->id,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'supplier_id' => $pembelian->supplier_id,
                ]);

                if ($batch->exists) {
                    $batch->stok_awal += $jumlah;
                    $batch->stok_saat_ini += $jumlah;
                    $batch->harga_beli_per_unit = $hargaBeli;
                    $batch->save();
                } else {
                    $batch->fill([
                        'stok_awal' => $jumlah,
                        'stok_saat_ini' => $jumlah,
                        'harga_beli_per_unit' => $hargaBeli,
                    ])->save();
                }

                $obat->increment('stok', $jumlah);
            }

            $diskonAmount = 0;
            if ($request->diskon_type === 'persen') {
                $diskonAmount = $totalPembelian * ($request->diskon / 100);
            } else {
                $diskonAmount = $request->diskon;
            }
            $finalTotal = max($totalPembelian - $diskonAmount, 0);

            $pembelian->update([
                'no_faktur_pbf' => $request->no_faktur_pbf,
                'tanggal' => $request->tanggal,
                'total' => $finalTotal,
                'diskon' => $request->diskon ?? 0,
                'diskon_type' => $request->diskon_type ?? 'nominal',
                'ppn_amount' => $totalPpn,
                'status' => 'final',
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil difinalisasi dan stok telah diperbarui.');
    }

    public function destroy(Pembelian $pembelian)
    {
        if ($pembelian->status === 'final') {
            DB::beginTransaction();
            try {
                foreach ($pembelian->detail as $detail) {
                    $obat = $detail->obat;
                    if ($obat) {
                        $batch = BatchObat::where('obat_id', $detail->obat_id)
                                        ->where('no_batch', $detail->no_batch)
                                        ->where('expired_date', $detail->expired_date)
                                        ->first();
                        if ($batch) {
                            $batch->decrement('stok_saat_ini', $detail->jumlah);
                            if ($batch->stok_saat_ini <= 0) {
                                $batch->delete();
                            }
                        }
                        $obat->decrement('stok', $detail->jumlah);
                    }
                }
                $pembelian->delete();
                DB::commit();
                return redirect()->route('pembelian.index')->with('success', 'Pembelian dan stok terkait berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
            }
        } else {
            $pembelian->delete();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian draft berhasil dihapus.');
        }
    }

    public function getObatBySupplier($supplierId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier tidak ditemukan.'], 404);
        }
        $obat = Obat::where('supplier_id', $supplierId)
            ->where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_date')
                      ->orWhere('expired_date', '>', now());
            })
            ->get(['id', 'kode', 'nama', 'stok', 'harga_dasar', 'ppn_rate', 'ppn_included', 'sediaan', 'satuan_terkecil', 'kemasan_besar', 'rasio_konversi']);
        if ($obat->isEmpty()) {
            return response()->json(['message' => 'Tidak ada obat tersedia untuk supplier ini atau stok habis/kadaluarsa.'], 200);
        }
        return response()->json($obat);
    }

    public function getSuratPesananDetails($suratPesananId)
    {
        $suratPesanan = SuratPesanan::with('details.obat')->find($suratPesananId);
        if (!$suratPesanan) {
            return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        }
        $items = $suratPesanan->details->map(function ($detail) {
            $obat = $detail->obat;
            return [
                'obat_id' => $obat->id,
                'kode' => $obat->kode,
                'nama' => $obat->nama,
                'jumlah' => $detail->qty_pesan,
                'harga_beli' => $detail->harga_satuan,
                'ppn_rate' => $obat->ppn_rate,
                'ppn_included' => $obat->ppn_included,
            ];
        });
        return response()->json([
            'supplier_id' => $suratPesanan->supplier_id,
            'items' => $items,
        ]);
    }
}
