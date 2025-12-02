<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Exports\ObatExport;
use App\Imports\ObatImport;
use Maatwebsite\Excel\Facades\Excel;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with('supplier', 'batches');

        if ($request->filter === 'menipis') {
            $query->whereBetween('stok', [1, 10]);
        } elseif ($request->filter === 'habis') {
            $query->where('stok', 0);
        } elseif ($request->filter === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($request->filter === 'kadaluarsa') {
            $query->whereNotNull('expired_date')
                ->where(function ($q) {
                    $q->where('expired_date', '<', now())
                        ->orWhereBetween('expired_date', [now(), now()->addMonth()]);
                });
        }

        $obats = $query->latest()->paginate(20)->withQueryString();

        return view('admin.master.obat.index', compact('obats'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.master.obat.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:obat,kode',
            'nama' => 'required',
            'kategori' => 'required',
            'sediaan' => 'nullable|string',
            'kemasan_besar' => 'nullable|string',
            'satuan_terkecil' => 'required|string',
            'rasio_konversi' => 'required_if:kemasan_besar,!=,null|integer|min:1',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'nullable|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'persen_untung' => 'nullable|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:supplier,id',
            'expired_date' => 'nullable|date|after_or_equal:today',
        ]);

        Obat::create($request->all());

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    // Method show (kosong/redirect agar tidak error resource route)
    public function show(Obat $obat)
    {
        return redirect()->route('obat.edit', $obat->id);
    }

    public function edit(Obat $obat)
    {
        $suppliers = Supplier::all();
        return view('admin.master.obat.edit', compact('obat', 'suppliers'));
    }

    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'kode' => 'required|unique:obat,kode,' . $obat->id,
            'nama' => 'required',
            'kategori' => 'required',
            'sediaan' => 'nullable|string',
            'kemasan_besar' => 'nullable|string',
            'satuan_terkecil' => 'required|string',
            'rasio_konversi' => 'required_if:kemasan_besar,!=,null|integer|min:1',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'nullable|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'persen_untung' => 'nullable|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:supplier,id',
            'expired_date' => 'nullable|date|after_or_equal:today',
        ]);

        $obat->update($request->all());

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        $obat->delete();
        return redirect('/obat')->with('success', 'Obat berhasil dihapus');
    }

    public function search(Request $r)
    {
        $q = trim($r->input('q', ''));

        $results = \App\Models\Obat::query()
            ->select(['id', 'kode', 'nama', 'stok', 'harga_jual', 'is_psikotropika'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode', 'like', "%{$q}%");
            })
            ->limit(12)
            ->get();

        return response()->json($results);
    }

    // FITUR EXPORT
    public function export()
    {
        return Excel::download(new ObatExport, 'data_obat_' . date('Y-m-d_His') . '.xlsx');
    }

    // FITUR IMPORT
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ObatImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data obat berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}