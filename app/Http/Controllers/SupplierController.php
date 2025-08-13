<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
    $suppliers = Supplier::all();
    return view('master.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('master.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:supplier,kode',
            'nama' => 'required',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string',
            'telepon' => 'nullable|string'
        ]);

        Supplier::create($request->all());

        return redirect('/supplier')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('master.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'kode' => 'required|unique:supplier,kode,' . $supplier->id,
            'nama' => 'required',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string',
            'telepon' => 'nullable|string'
        ]);

        $supplier->update($request->all());

        return redirect('/supplier')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect('/supplier')->with('success', 'Supplier berhasil dihapus');
    }
}

