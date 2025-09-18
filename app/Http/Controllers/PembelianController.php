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
    /**
     * Menampilkan daftar pembelian terbaru.
     */
    public function index()
    {
        // Mendapatkan ID cabang dari user yang login, jika tidak ada, gunakan ID cabang pusat
        $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id');

        $data = Pembelian::with('supplier')
            ->where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);

        return view('transaksi.pembelian.index', compact('data'));
    }

    /**
     * Menampilkan form untuk membuat pembelian baru.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obat = Obat::orderBy('nama')->get();

        // no_faktur auto-suggest
        $noFaktur = 'FPB-' . date('Y') . '-' . str_pad((Pembelian::count() + 1), 3, '0', STR_PAD_LEFT);

        return view('transaksi.pembelian.create', compact('suppliers', 'obat', 'noFaktur'));
    }

    /**
     * Menyimpan data pembelian baru beserta detail dan batch obatnya.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => ['required', 'string', Rule::unique('pembelian', 'no_faktur')],
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:supplier,id',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|integer|exists:obat,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli_satuan' => 'required|numeric|min:0',
            'items.*.no_batch' => 'nullable|string|max:255',
            'items.*.expired_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, &$pembelian) {
            // Menentukan ID cabang
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;

            $pembelian = Pembelian::create([
                'no_faktur' => $request->no_faktur,
                'tanggal' => Carbon::parse($request->tanggal)->toDateTimeString(),
                'supplier_id' => $request->supplier_id,
                'cabang_id' => $cabangId,
                'total' => 0, // Akan dihitung ulang
                'ppn_amount' => 0, // Akan dihitung ulang
            ]);

            $totalHargaBeliBersih = 0; // Total harga beli sebelum PPN
            $totalPpnPembelian = 0;

            foreach ($request->items as $item) {
                $obat = Obat::findOrFail($item['obat_id']);
                $qty = (int)$item['jumlah'];
                $hargaBeliInput = (float)$item['harga_beli_satuan']; // Harga yang diinput user

                $hargaBeliBersihPerUnit = $hargaBeliInput;
                $ppnAmountPerUnit = 0;

                // Hitung PPN berdasarkan ppn_rate dan ppn_included
                if ($obat->ppn_rate > 0) {
                    if ($obat->ppn_included) {
                        // Jika harga beli sudah termasuk PPN, ekstrak PPN dan harga bersihnya
                        $hargaBeliBersihPerUnit = $hargaBeliInput / (1 + $obat->ppn_rate / 100);
                        $ppnAmountPerUnit = $hargaBeliInput - $hargaBeliBersihPerUnit;
                    } else {
                        // Jika harga beli belum termasuk PPN, PPN ditambahkan ke harga beli bersih
                        $ppnAmountPerUnit = $hargaBeliInput * ($obat->ppn_rate / 100);
                    }
                }
                
                $subtotalBersih = $hargaBeliBersihPerUnit * $qty;
                $subtotalPpn = $ppnAmountPerUnit * $qty;

                $totalHargaBeliBersih += $subtotalBersih;
                $totalPpnPembelian += $subtotalPpn;

                // Simpan pembelian detail
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $qty,
                    'harga_beli' => $hargaBeliBersihPerUnit, // Simpan harga beli bersih
                    'ppn_amount' => $ppnAmountPerUnit,      // Simpan PPN per unit
                    'no_batch' => $item['no_batch'] ?? null,
                    'expired_date' => !empty($item['expired_date']) ? Carbon::parse($item['expired_date'])->toDateString() : null,
                    'harga_beli_satuan' => $hargaBeliInput, // Simpan harga beli yang diinput
                ]);

                // Buat batch_obat entry
                BatchObat::create([
                    'obat_id' => $obat->id,
                    'no_batch' => $item['no_batch'] ?? null,
                    'expired_date' => !empty($item['expired_date']) ? Carbon::parse($item['expired_date'])->toDateString() : null,
                    'stok_awal' => $qty,
                    'stok_saat_ini' => $qty,
                    'harga_beli_per_unit' => $hargaBeliBersihPerUnit, // Harga beli bersih untuk batch
                    'supplier_id' => $request->supplier_id ?? null,
                ]);

                // Update stok di tabel obat (increment)
                $obat->increment('stok', $qty);
            }

            // Update total pembelian dan total PPN pembelian di tabel utama
            $pembelian->update([
                'total' => $totalHargaBeliBersih, // Total harga beli bersih
                'ppn_amount' => $totalPpnPembelian, // Total PPN
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
    }

    /**
     * Menampilkan faktur pembelian.
     */
    public function faktur($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        return view('transaksi.pembelian.faktur', compact('p'));
    }

    /**
     * Mengunduh faktur dalam format PDF.
     */
    public function pdf($id)
    {
        $p = Pembelian::with(['supplier', 'detail.obat'])->findOrFail($id);
        $pdf = \PDF::loadView('transaksi.pembelian.faktur', compact('p'));
        return $pdf->download('Faktur-' . $p->no_faktur . '.pdf');
    }

    /**
     * Menampilkan form untuk mengedit pembelian.
     */
    public function edit(Pembelian $pembelian)
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $obat = Obat::orderBy('nama')->get();
        // Load detail pembelian bersama dengan obatnya
        $pembelian->load('detail.obat');
        return view('transaksi.pembelian.edit', compact('pembelian', 'suppliers', 'obat'));
    }

    /**
     * Memperbarui data pembelian.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        $request->validate([
            'no_faktur' => ['required', 'string', 'max:50', Rule::unique('pembelian', 'no_faktur')->ignore($pembelian->id)],
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:supplier,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.obat_id' => ['required', 'exists:obat,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli_satuan' => ['required', 'numeric', 'min:0'],
            'items.*.no_batch' => ['nullable', 'string', 'max:255'],
            'items.*.expired_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $pembelian) {
            $cabangId = Auth::user()->cabang_id ?? Cabang::where('is_pusat', true)->value('id') ?? Cabang::first()->id;
            
            // 1. Reversi stok dan hapus detail pembelian lama
            foreach ($pembelian->detail as $detail) {
                // Turunkan stok obat
                $obat = Obat::find($detail->obat_id);
                if ($obat) {
                    $obat->decrement('stok', $detail->jumlah);
                }

                // Turunkan stok pada batch terkait
                // Cari batch yang sesuai dengan detail pembelian
                $batch = BatchObat::where('obat_id', $detail->obat_id)
                                    ->where('no_batch', $detail->no_batch)
                                    ->where('expired_date', $detail->expired_date)
                                    ->where('supplier_id', $pembelian->supplier_id)
                                    ->first();
                if ($batch) {
                    // Cek apakah stok yang akan direversi lebih besar dari stok saat ini di batch
                    if ($detail->jumlah > $batch->stok_saat_ini) {
                        // Jika iya, berarti batch ini sudah terjual sebagian. Hanya turunkan stoknya, jangan hapus.
                        $batch->stok_saat_ini = 0;
                        $batch->save();
                    } else {
                        $batch->decrement('stok_saat_ini', $detail->jumlah);
                    }
                    // Jika stok saat ini di batch menjadi 0, kita bisa menandainya sebagai expired/tidak aktif
                    if ($batch->stok_saat_ini <= 0) {
                        $batch->is_active = false; // Asumsi ada kolom is_active
                        $batch->save();
                    }
                }
            }

            // Hapus semua detail pembelian lama setelah stok direversi
            $pembelian->detail()->delete();

            // 2. Update data pembelian utama
            $pembelian->update([
                'no_faktur' => $request->no_faktur,
                'tanggal' => Carbon::parse($request->tanggal)->toDateTimeString(),
                'supplier_id' => $request->supplier_id,
                'cabang_id' => $cabangId,
                'total' => 0,
                'ppn_amount' => 0,
            ]);

            // 3. Tambahkan detail pembelian baru dan update stok kembali
            $totalHargaBeliBersih = 0;
            $totalPpnPembelian = 0;

            foreach ($request->items as $item) {
                $obat = Obat::findOrFail($item['obat_id']);
                $qty = (int)$item['jumlah'];
                $hargaBeliInput = (float)$item['harga_beli_satuan'];
                $noBatch = $item['no_batch'] ?? null;
                $expired = !empty($item['expired_date']) ? Carbon::parse($item['expired_date'])->toDateString() : null;

                $hargaBeliBersihPerUnit = $hargaBeliInput;
                $ppnAmountPerUnit = 0;
                
                if ($obat->ppn_rate > 0) {
                    if ($obat->ppn_included) {
                        $hargaBeliBersihPerUnit = $hargaBeliInput / (1 + $obat->ppn_rate / 100);
                        $ppnAmountPerUnit = $hargaBeliInput - $hargaBeliBersihPerUnit;
                    } else {
                        $ppnAmountPerUnit = $hargaBeliInput * ($obat->ppn_rate / 100);
                    }
                }
                
                $subtotalBersih = $hargaBeliBersihPerUnit * $qty;
                $subtotalPpn = $ppnAmountPerUnit * $qty;

                $totalHargaBeliBersih += $subtotalBersih;
                $totalPpnPembelian += $subtotalPpn;

                // Simpan detail pembelian baru
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $qty,
                    'harga_beli' => $hargaBeliBersihPerUnit,
                    'ppn_amount' => $ppnAmountPerUnit,
                    'no_batch' => $noBatch,
                    'expired_date' => $expired,
                    'harga_beli_satuan' => $hargaBeliInput,
                ]);

                // Buat batch_obat entry baru
                BatchObat::create([
                    'obat_id' => $obat->id,
                    'no_batch' => $noBatch,
                    'expired_date' => $expired,
                    'stok_awal' => $qty,
                    'stok_saat_ini' => $qty,
                    'harga_beli_per_unit' => $hargaBeliBersihPerUnit,
                    'supplier_id' => $request->supplier_id ?? null,
                ]);

                // Tingkatkan stok obat
                $obat->increment('stok', $qty);
            }

            // Update total pembelian dan PPN pembelian di tabel utama
            $pembelian->update([
                'total' => $totalHargaBeliBersih,
                'ppn_amount' => $totalPpnPembelian,
            ]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui.');
    }

    /**
     * Mendapatkan daftar obat berdasarkan supplier, termasuk data PPN.
     */
    public function getObatBySupplier($supplierId)
    {
        $obat = Obat::where('supplier_id', $supplierId)
            ->get(['id', 'kode', 'nama', 'stok', 'harga_dasar', 'ppn_rate', 'ppn_included']);

        return response()->json($obat);
    }
}
