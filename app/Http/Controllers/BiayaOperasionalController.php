<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use Illuminate\Http\Request;

class BiayaOperasionalController extends Controller
{
    /**
     * Menampilkan daftar semua biaya operasional.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $data = BiayaOperasional::where('tanggal', 'like', $bulan . '%')
                                ->orderBy('tanggal', 'desc')
                                ->paginate(10);

        return view('biaya-operasional.index', compact('data', 'bulan'));
    }

    /**
     * Menampilkan formulir untuk membuat biaya operasional baru.
     */
    public function create()
    {
        return view('biaya-operasional.create');
    }

    /**
     * Menyimpan biaya operasional baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        BiayaOperasional::create($request->all());

        return redirect()->route('biaya-operasional.index')->with('success', 'Biaya operasional berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit biaya operasional.
     */
    public function edit(BiayaOperasional $biayaOperasional)
    {
        return view('biaya-operasional.edit', compact('biayaOperasional'));
    }

    /**
     * Memperbarui data biaya operasional di database.
     */
    public function update(Request $request, BiayaOperasional $biayaOperasional)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        $biayaOperasional->update($request->all());

        return redirect()->route('biaya-operasional.index')->with('success', 'Biaya operasional berhasil diperbarui.');
    }

    /**
     * Menghapus biaya operasional dari database.
     */
    public function destroy(BiayaOperasional $biayaOperasional)
    {
        $biayaOperasional->delete();

        return redirect()->route('biaya-operasional.index')->with('success', 'Biaya operasional berhasil dihapus.');
    }
}