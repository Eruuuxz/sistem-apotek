<?php

namespace App\Http\Controllers;

use App\Models\{Retur, ReturDetail, Pembelian, Obat};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReturController extends Controller
{
    public function index(Request $r)
    {
        $q = Retur::where('jenis', 'pembelian')->latest();

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
        
        $noRetur = 'RT-' . date('Ym') . '-' . str_pad((Retur::whereYear('created_at', date('Y'))->count() + 1), 4, '0', STR_PAD_LEFT);
        
        return view('admin.Transaksi.retur.create', compact('pembelian', 'noRetur'));
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
            return response()->json(['items' => $items]); 
        }

        return response()->json(['error' => 'Jenis transaksi tidak valid.'], 400);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Data transaksi tidak ditemukan.'], 404);
    }
}

    public function store(Request $r)
    {
        $r->merge(['transaksi_id' => $r->transaksi_pembelian_id]);

        $r->validate([
            'no_retur' => ['required', 'string', 'max:50', Rule::unique('retur', 'no_retur')],
            'tanggal' => ['required', 'date_format:Y-m-d\TH:i'],
            'jenis' => ['required', 'in:pembelian'],
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