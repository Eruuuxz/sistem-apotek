<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Supplier; // Import model Supplier
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::with('supplier')->get(); // Ambil obat dengan supplier
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
            'nama' => 'required',
            'kategori' => 'required|string',
            'stok' => 'required|integer',
            'harga_dasar' => 'required|numeric',
            'persen_untung' => 'required|numeric',
            'harga_jual' => 'required|numeric',
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
            'nama' => 'required',
            'kategori' => 'required|string',
            'stok' => 'required|integer',
            'harga_dasar' => 'required|numeric',
            'persen_untung' => 'required|numeric',
            'harga_jual' => 'required|numeric',
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

