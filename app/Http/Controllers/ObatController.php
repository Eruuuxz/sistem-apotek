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
            $query->whereBetween('stok', [1, 9]);
        } elseif ($request->filter === 'habis') {
            $query->where('stok', 0);
        } elseif ($request->filter === 'tersedia') {
            $query->where('stok', '>', 0);
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
}

