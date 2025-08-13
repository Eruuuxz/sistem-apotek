<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        // Ambil semua barang dengan supplier-nya
        $barang = Barang::with('supplier')->get();

        return view('master.barang.index', compact('barang'));
        $barang = Barang::all();
        return view('master.barang.index', compact('barang'));
    }
    
    public function create()
    {
        return view('master.barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:barang,kode',
            'nama' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|integer'
        ]);

        Barang::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga_jual' => $request->harga,
            'stok' => $request->stok
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil ditambahkan');
    }
}
