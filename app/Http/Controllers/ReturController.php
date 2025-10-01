<?php

namespace App\Http\Controllers;

use App\Models\{Retur, ReturDetail, Pembelian, Penjualan, Obat};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
        return view('admin.Transaksi.retur.index', compact('data'));
    }

    public function create()
    {
        // Ambil data yang relevan untuk dropdown
        $pembelian = Pembelian::with('supplier')->latest()->take(30)->get();
        $penjualan = Penjualan::with('pelanggan')->latest()->take(30)->get();
        
        $noRetur = 'RT-' . date('Ym') . '-' . str_pad((Retur::whereYear('created_at', date('Y'))->count() + 1), 4, '0', STR_PAD_LEFT);
        
        return view('admin.Transaksi.retur.create', compact('pembelian', 'penjualan', 'noRetur'));
    }

    public function sumber($jenis, $id)
    {
        try {
            if ($jenis === 'pembelian') {
                $src = Pembelian::with('detail.obat')->findOrFail($id);
                $items = $src->detail->map(fn($d) => [
                    'id' => $d->obat_id,
                    'kode' => $d->obat->kode,
                    'nama' => $d->obat->nama,
                    'harga' => $d->harga_beli,
                    'max_qty' => $d->jumlah,
                ]);
            } else { // jenis === 'penjualan'
                $src = Penjualan::with('details.obat')->findOrFail($id);
                $items = $src->details->map(fn($d) => [
                    'id' => $d->obat_id,
                    'kode' => $d->obat->kode,
                    'nama' => $d->obat->nama,
                    'harga' => $d->harga,
                    'max_qty' => $d->qty,
                    'hpp' => $d->hpp, // Sertakan HPP untuk retur penjualan
                ]);
            }
            return response()->json(['items' => $items]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data transaksi tidak ditemukan.'], 404);
        }
    }

    public function store(Request $r)
    {
        // Menentukan ID transaksi dari input yang relevan
        $transaksiId = $r->jenis === 'pembelian' ? $r->transaksi_pembelian_id : $r->transaksi_penjualan_id;

        // Menambahkan ID transaksi ke dalam request untuk validasi
        $r->merge(['transaksi_id' => $transaksiId]);

        $r->validate([
            'no_retur' => ['required', 'string', 'max:50', Rule::unique('retur', 'no_retur')],
            'tanggal' => ['required', 'date_format:Y-m-d\TH:i'],
            'jenis' => ['required', 'in:pembelian,penjualan'],
            'transaksi_id' => ['required', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:obat,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($r) {
            $totalRetur = 0;
            // Hitung total dari array 'items'
            foreach ($r->items as $item) {
                $totalRetur += (int)$item['qty'] * (float)$item['harga'];
            }

            $retur = Retur::create([
                'no_retur' => $r->no_retur,
                'tanggal' => Carbon::parse($r->tanggal)->toDateTimeString(),
                'jenis' => $r->jenis,
                'transaksi_id' => $r->transaksi_id,
                'total' => $totalRetur,
                'keterangan' => $r->keterangan,
            ]);

            foreach ($r->items as $item) {
                $obat = Obat::find($item['id']);
                if (!$obat) continue;

                $qty = (int)$item['qty'];
                $harga = (float)$item['harga'];

                ReturDetail::create([
                    'retur_id' => $retur->id,
                    'obat_id' => $item['id'],
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $qty * $harga,
                    'hpp' => $r->jenis === 'penjualan' ? ($item['hpp'] ?? $obat->harga_dasar) : null,
                    'harga_beli' => $r->jenis === 'pembelian' ? $harga : null,
                ]);

                if ($r->jenis === 'pembelian') {
                    // Retur ke supplier -> Stok obat berkurang
                    $obat->decrement('stok', $qty);
                } else { // Retur dari pelanggan -> Stok obat bertambah
                    $obat->increment('stok', $qty);
                }
            }
        });

        return redirect()->route('retur.index')->with('success', 'Data retur berhasil disimpan.');
    }

    public function show($id)
    {
        $retur = Retur::with('details.obat')->findOrFail($id);
        return view('admin.Transaksi.retur.show', compact('retur'));
    }
}