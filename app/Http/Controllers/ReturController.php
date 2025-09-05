<?php

namespace App\Http\Controllers;

use App\Models\{Retur, ReturDetail, Pembelian, Penjualan, Obat};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Tambahkan ini

class ReturController extends Controller
{
    public function index(Request $r)
    {
        $q = Retur::query()->latest();

        if ($r->filled('jenis')) {
            $q->where('jenis', $r->jenis);
        }
        if ($r->filled('from')) {
            $q->whereDate('tanggal', '>=', $r->from);
        }
        if ($r->filled('to')) {
            $q->whereDate('tanggal', '<=', $r->to);
        }

        $data = $q->paginate(10)->withQueryString();
        return view('transaksi.retur.index', compact('data'));
    }

    public function create()
    {
        // list transaksi terakhir sebagai referensi (optional bisa ajax select2)
        $pembelian = Pembelian::latest()->take(20)->get();
        $penjualan = Penjualan::latest()->take(20)->get();
        $noRetur = 'RT-' . date('Y') . '-' . str_pad((Retur::count() + 1), 3, '0', STR_PAD_LEFT);
        return view('transaksi.retur.create', compact('pembelian', 'penjualan', 'noRetur'));
    }

    public function sumber($jenis, $id)
    {
        if ($jenis === 'pembelian') {
            $src = Pembelian::with('detail.obat')->findOrFail($id);
            $items = $src->detail->map(fn($d) => [
                'id' => $d->obat_id,
                'kode' => $d->obat->kode,
                'nama' => $d->obat->nama,
                'harga' => $d->harga_beli, // Harga beli dari detail pembelian
                'max_qty' => $d->jumlah,
                'harga_dasar_obat' => $d->obat->harga_dasar, // Tambahkan harga_dasar dari obat
            ]);
        } else { // jenis === 'penjualan'
            $src = Penjualan::with('details.obat')->findOrFail($id);
            $items = $src->details->map(fn($d) => [
                'id' => $d->obat_id,
                'kode' => $d->obat->kode,
                'nama' => $d->obat->nama,
                'harga' => $d->harga, // Harga jual dari detail penjualan
                'max_qty' => $d->qty,
                'hpp_obat' => $d->hpp, // HPP dari detail penjualan
            ]);
        }
        return response()->json(['items' => $items]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'no_retur' => ['required', 'max:50', Rule::unique('retur', 'no_retur')],
            'tanggal' => ['required', 'date'], // Validasi tetap 'date' karena input HTML type="datetime-local" akan divalidasi sebagai date
            'jenis' => ['required', 'in:pembelian,penjualan'],
            'transaksi_id' => ['required', 'integer'],
            'item_id' => ['required', 'array', 'min:1'],
            'item_id.*' => ['required', 'integer'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
            'harga.*' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            // Tambahkan validasi untuk hpp dan harga_beli jika diperlukan dari frontend
            'hpp.*' => ['nullable', 'numeric', 'min:0'],
            'harga_beli_item.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($r) {
            $retur = Retur::create([
                'no_retur' => $r->no_retur,
                'tanggal' => Carbon::parse($r->tanggal)->toDateTimeString(), // Ubah ke datetime string
                'jenis' => $r->jenis,
                'transaksi_id' => $r->transaksi_id,
                'total' => 0, // Akan diupdate setelah detail ditambahkan
                'keterangan' => $r->keterangan ?? null,
            ]);

            $total = 0;
            foreach ($r->item_id as $i => $id) {
                $qty = (int) $r->qty[$i];
                $harga = (float) $r->harga[$i];
                $sub = $qty * $harga;

                $detailData = [
                    'retur_id' => $retur->id,
                    'obat_id' => $id,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $sub
                ];

                // Tambahkan HPP atau Harga Beli berdasarkan jenis retur
                if ($r->jenis === 'pembelian') {
                    // Untuk retur pembelian, harga yang relevan adalah harga_beli
                    // Ambil harga_beli dari input tersembunyi atau dari obat jika tidak ada
                    $detailData['harga_beli'] = (float) ($r->harga_beli_item[$i] ?? Obat::find($id)->harga_dasar);
                } else { // jenis === 'penjualan'
                    // Untuk retur penjualan, harga yang relevan adalah HPP
                    // Ambil HPP dari input tersembunyi atau dari obat jika tidak ada
                    $detailData['hpp'] = (float) ($r->hpp[$i] ?? Obat::find($id)->harga_dasar);
                }

                ReturDetail::create($detailData);

                if ($r->jenis === 'pembelian') {
                    // retur ke supplier: stok OBAT BERKURANG
                    $obat = Obat::find($id);
                    if ($obat) {
                        $obat->decrement('stok', $qty);
                    }
                } else {
                    // retur dari customer: stok OBAT BERTAMBAH
                    $obat = Obat::find($id);
                    if ($obat) {
                        $obat->increment('stok', $qty);
                    }
                }
                $total += $sub;
            }
            $retur->update(['total' => $total]);

            // Hapus bagian ini karena sudah dihandle oleh Carbon::parse di atas
            // if ($r->filled('tanggal')) {
            //     $retur->tanggal = $r->tanggal;
            //     $retur->save();
            // }
        });
        return redirect()->route('retur.index')->with('success', 'Retur tersimpan');
    }

    public function show($id)
    {
    $retur = Retur::with('details.obat')->findOrFail($id);
    return view('transaksi.retur.show', compact('retur'));
    }
}
