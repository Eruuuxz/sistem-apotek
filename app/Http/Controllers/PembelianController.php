<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Obat;
use App\Models\BatchObat;
use App\Models\Cabang;
use App\Models\Supplier;
use App\Models\SuratPesanan; // Import SuratPesanan
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
        $query = Pembelian::with('supplier', 'suratPesanan', 'cabang'); // Load cabang juga

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);

        return view('transaksi.pembelian.index', compact('data'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get(); // Digunakan jika membuat pembelian manual tanpa SP
        $noFaktur = 'FPB-' . date('Ymd') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);
        $suratPesanans = SuratPesanan::where('status', 'pending')->get(); // SP yang belum diproses
        return view('transaksi.pembelian.create', compact('suppliers', 'obats', 'noFaktur', 'suratPesanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'tanggal' => 'required|date',
            'surat_pesanan_id' => 'nullable|exists:surat_pesanan,id',
            'no_faktur_pbf' => 'nullable|string|max:255', // Bisa diisi nanti saat finalisasi
            'diskon' => 'nullable|numeric|min:0',
            'diskon_type' => 'nullable|in:nominal,persen',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            // Detail batch tidak divalidasi di sini karena ini untuk draft
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

            // Hitung diskon
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
                'status' => 'draft', // Status awal adalah draft
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
                    // no_batch dan expired_date akan diisi saat finalisasi
                ]);
            }

            // Update status Surat Pesanan jika ada
            if ($request->surat_pesanan_id) {
                $suratPesanan = SuratPesanan::find($request->surat_pesanan_id);
                if ($suratPesanan) {
                    // Logika untuk update status SP, misal 'parsial' atau 'selesai'
                    // Untuk sederhana, kita set ke 'selesai' jika semua item SP masuk ke pembelian
                    // Atau bisa juga 'parsial' jika hanya sebagian
                    $suratPesanan->update(['status' => 'selesai']); // Atau 'parsial'
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
        return view('transaksi.pembelian.faktur', compact('p'));
    }

    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat', 'cabang'])->findOrFail($id);
        $pdf = \PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }

    public function edit(Pembelian $pembelian)
    {
        $pembelian->load('detail.obat', 'supplier', 'suratPesanan', 'cabang');
        $suppliers = Supplier::orderBy('nama')->get();
        $obats = Obat::orderBy('nama')->get();
        return view('transaksi.pembelian.edit', compact('pembelian', 'suppliers', 'obats'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        // Validasi untuk finalisasi pembelian
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
            'items.*.expired_date' => 'required|date|after:today', // Expired date harus setelah hari ini
        ]);

        // Pastikan pembelian belum difinalisasi sebelumnya
        if ($pembelian->status === 'final') {
            return back()->with('error', 'Pembelian ini sudah difinalisasi dan stok sudah diperbarui.');
        }

        DB::transaction(function () use ($request, $pembelian) {
            $totalPembelian = 0;
            $totalPpn = 0;

            // Pertama, kembalikan stok dari batch lama jika ada (untuk kasus edit pembelian final)
            // Namun, karena kita hanya mengizinkan finalisasi sekali, ini tidak terlalu relevan
            // Jika ingin mengizinkan edit setelah final, perlu logika rollback stok batch

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

                // Update detail pembelian dengan info batch
                $detail->update([
                    'jumlah' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'ppn_amount' => $ppnPerUnit * $jumlah, // Update PPN amount per item
                ]);

                // Buat atau perbarui BatchObat
                // Cek apakah batch dengan no_batch dan expired_date yang sama sudah ada untuk obat ini
                $batch = BatchObat::firstOrNew([
                    'obat_id' => $obat->id,
                    'no_batch' => $itemData['no_batch'],
                    'expired_date' => $itemData['expired_date'],
                    'supplier_id' => $pembelian->supplier_id, // Asumsi supplier sama untuk batch ini
                ]);

                if ($batch->exists) {
                    // Jika batch sudah ada, tambahkan stok
                    $batch->stok_awal += $jumlah;
                    $batch->stok_saat_ini += $jumlah;
                    // Jika harga beli berubah, bisa diupdate atau dirata-rata
                    $batch->harga_beli_per_unit = $hargaBeli; // Update dengan harga terbaru
                    $batch->save();
                } else {
                    // Jika batch baru, buat baru
                    $batch->fill([
                        'stok_awal' => $jumlah,
                        'stok_saat_ini' => $jumlah,
                        'harga_beli_per_unit' => $hargaBeli,
                    ])->save();
                }

                // Perbarui stok total di tabel Obat
                $obat->increment('stok', $jumlah);
            }

            // Hitung diskon untuk total pembelian
            $diskonAmount = 0;
            if ($request->diskon_type === 'persen') {
                $diskonAmount = $totalPembelian * ($request->diskon / 100);
            } else {
                $diskonAmount = $request->diskon;
            }
            $finalTotal = max($totalPembelian - $diskonAmount, 0);

            // Update pembelian menjadi final
            $pembelian->update([
                'no_faktur_pbf' => $request->no_faktur_pbf,
                'tanggal' => $request->tanggal,
                'total' => $finalTotal,
                'diskon' => $request->diskon ?? 0,
                'diskon_type' => $request->diskon_type ?? 'nominal',
                'ppn_amount' => $totalPpn,
                'status' => 'final', // Set status menjadi final
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil difinalisasi dan stok telah diperbarui.');
    }

    public function destroy(Pembelian $pembelian)
    {
        // Jika pembelian sudah final, kita harus mengembalikan stok dari batch
        if ($pembelian->status === 'final') {
            DB::beginTransaction();
            try {
                foreach ($pembelian->detail as $detail) {
                    $obat = $detail->obat;
                    if ($obat) {
                        // Cari batch yang sesuai dan kurangi stok
                        $batch = BatchObat::where('obat_id', $detail->obat_id)
                                        ->where('no_batch', $detail->no_batch)
                                        ->where('expired_date', $detail->expired_date)
                                        ->first();
                        if ($batch) {
                            $batch->decrement('stok_saat_ini', $detail->jumlah);
                            // Jika stok batch menjadi 0, bisa dihapus atau ditandai tidak aktif
                            if ($batch->stok_saat_ini <= 0) {
                                $batch->delete(); // Atau set status inactive
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
            // Jika masih draft, langsung hapus
            $pembelian->delete();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian draft berhasil dihapus.');
        }
    }

    public function getObatBySupplier($supplierId)
    {
        // Pastikan supplierId valid dan ada di database
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier tidak ditemukan.'], 404);
        }

        // Ambil obat yang terkait dengan supplier ini
        // Filter obat yang stoknya > 0 dan belum expired
        $obat = Obat::where('supplier_id', $supplierId)
            ->where('stok', '>', 0) // Hanya tampilkan obat yang memiliki stok
            ->where(function ($query) {
                $query->whereNull('expired_date') // Obat tanpa expired_date dianggap tidak expired
                      ->orWhere('expired_date', '>', now()); // Obat yang expired_date-nya di masa depan
            })
            ->get([
                'id',
                'kode',
                'nama',
                'stok',
                'harga_dasar', // Ini akan menjadi harga beli default
                'ppn_rate',
                'ppn_included',
                'sediaan',
                'satuan_terkecil',
                'kemasan_besar', // Tambahkan kemasan_besar
                'rasio_konversi' // Tambahkan rasio_konversi
            ]);

        // Jika tidak ada obat ditemukan, kembalikan array kosong
        if ($obat->isEmpty()) {
            return response()->json(['message' => 'Tidak ada obat tersedia untuk supplier ini atau stok habis/kadaluarsa.'], 200);
        }

        return response()->json($obat);
    }

    // Fungsi untuk mendapatkan detail SP untuk form pembelian
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