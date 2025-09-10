<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Supplier; // Import model Supplier
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with('supplier'); // langsung include supplier di query

        // filter stok
        if ($request->filter === 'menipis') {
            $query->whereBetween('stok', [1, 10]);
        } elseif ($request->filter === 'habis') {
            $query->where('stok', 0);
        } elseif ($request->filter === 'tersedia') {
            $query->where('stok', '>', 0);
            // Filter obat yang kadaluarsa dalam 1 bulan
        } elseif ($request->filter === 'kadaluarsa') {
            $query->whereNotNull('expired_date')
                ->where(function ($q) {
                    $q->where('expired_date', '<', now()) // sudah lewat
                        ->orWhereBetween('expired_date', [now(), now()->addMonth()]); // hampir expired
                });
        }
        $obats = $query->get(); // gunakan hasil query yang difilter

        return view('master.obat.index', compact('obats'));
    }


    public function create()
    {
        $suppliers = Supplier::all(); // Ambil semua supplier untuk dropdown
        return view('master.obat.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:obat,kode',
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'expired_date' => 'nullable|date|after_or_equal:today',
            'harga_dasar' => 'required|numeric|min:0',
            'persen_untung' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:supplier,id' // Validasi supplier_id
        ]);

        Obat::create($request->all());

        return redirect('/obat')->with('success', 'Obat berhasil ditambahkan');
    }

    public function edit(Obat $obat)
    {
        $suppliers = Supplier::all();
        return view('master.obat.edit', compact('obat', 'suppliers'));
    }

    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'kode' => 'required|unique:obat,kode,' . $obat->id,
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'expired_date' => 'nullable|date|after_or_equal:today',
            'harga_dasar' => 'required|numeric|min:0',
            'persen_untung' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:supplier,id'
        ]);

        $obat->update($request->all());

        return redirect('/obat')->with('success', 'Obat berhasil diperbarui');
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
}

