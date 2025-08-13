<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier; // Import model Supplier
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        // Ambil semua barang dengan supplier-nya
        $barang = Barang::with('supplier')->get();
        return view('master.barang.index', compact('barang'));
    }
    
    public function create()
    {
        $suppliers = Supplier::all(); // Ambil semua supplier untuk dropdown
        return view('master.barang.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:barang,kode',
            'nama' => 'required',
            'harga_jual' => 'required|numeric', // Sesuaikan dengan nama field di DB
            'stok' => 'required|integer',
            'supplier_id' => 'nullable|exists:supplier,id' // Validasi supplier_id
        ]);

        Barang::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga_jual' => $request->harga_jual, // Sesuaikan dengan nama field di DB
            'stok' => $request->stok,
            'supplier_id' => $request->supplier_id
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        $suppliers = Supplier::all();
        return view('master.barang.edit', compact('barang', 'suppliers'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode' => 'required|unique:barang,kode,' . $barang->id,
            'nama' => 'required',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'supplier_id' => 'nullable|exists:supplier,id'
        ]);

        $barang->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'supplier_id' => $request->supplier_id
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect('/barang')->with('success', 'Barang berhasil dihapus');
    }
}

