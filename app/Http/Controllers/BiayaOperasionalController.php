<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BiayaOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $biayaOperasionals = BiayaOperasional::latest()->paginate(10);
        return view('biaya-operasional.index', compact('biayaOperasionals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('biaya-operasional.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable'
        ]);

        BiayaOperasional::create($request->all());
        
        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Biaya operasional berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $biayaOperasional = BiayaOperasional::findOrFail($id);
        return view('biaya-operasional.edit', compact('biayaOperasional'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable'
        ]);

        $biayaOperasional = BiayaOperasional::findOrFail($id);
        $biayaOperasional->update($request->all());

        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Biaya operasional berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $biayaOperasional = BiayaOperasional::findOrFail($id);
        $biayaOperasional->delete();

        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Biaya operasional berhasil dihapus.');
    }
}
