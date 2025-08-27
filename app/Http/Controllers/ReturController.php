<?php

namespace App\Http\Controllers;

use App\Models\{Retur, ReturDetail, Pembelian, Penjualan, Obat}; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            $items = $src->detail->map(fn ($d) => [
                'id' => $d->obat_id,
                'kode' => $d->obat->kode,
                'nama' => $d->obat->nama,
                'harga' => $d->harga_beli, // Sesuaikan dengan harga_beli di PembelianDetail
                'max_qty' => $d->jumlah
            ]);
        } else { // jenis === 'penjualan'
            $src = Penjualan::with('detail.obat')->findOrFail($id); 
            $items = $src->detail->map(fn ($d) => [
                'id' => $d->obat_id, 
                'kode' => $d->obat->kode, 
                'nama' => $d->obat->nama, 
                'harga' => $d->harga,
                'max_qty' => $d->qty
            ]);
        }
        return response()->json(['items' => $items]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'no_retur'   => ['required', 'max:50', Rule::unique('retur', 'no_retur')],
            'tanggal'   => ['required', 'date'],
            'jenis'   => ['required', 'in:pembelian,penjualan'],
            'transaksi_id' => ['required', 'integer'],
            'item_id'   => ['required', 'array', 'min:1'],
            'item_id.*' => ['required', 'integer'],
            'qty'   => ['required', 'array', 'min:1'],
            'qty.*'   => ['required', 'integer', 'min:1'],
            'harga'   => ['required', 'array', 'min:1'],
            'harga.*'   => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($r) {
            $retur = Retur::create([
                'no_retur'   => $r->no_retur,
                'tanggal'   => $r->tanggal,
                'jenis'   => $r->jenis,
                'transaksi_id' => $r->transaksi_id,
                'total'   => 0, // Akan diupdate setelah detail ditambahkan
                'keterangan' => $r->keterangan ?? null,
            ]);

            $total = 0;
            foreach ($r->item_id as $i => $id) {
                $qty = (int)$r->qty[$i];
                $harga = (float)$r->harga[$i];
                $sub = $qty * $harga;

                ReturDetail::create([
                    'retur_id' => $retur->id,
                    'obat_id' => $id, 
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $sub
                ]);

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
        });

        return redirect()->route('retur.index')->with('success', 'Retur tersimpan');
    }
}
