<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        return view('admin.biaya-operasional.index', compact('data', 'bulan'));
    }

    /**
     * Menampilkan formulir untuk membuat biaya operasional baru.
     */
    public function create()
    {
        return view('admin.biaya-operasional.create');
    }

    /**
     * Menyimpan beberapa biaya operasional baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'biaya' => 'required|array|min:1'
        ]);

        Validator::make($request->all(), [
            'biaya.*.jenis_biaya' => 'required|string|max:255',
            'biaya.*.keterangan' => 'nullable|string',
            'biaya.*.jumlah' => 'required|numeric|min:0',
        ])->validate();

        foreach ($request->biaya as $item) {
            BiayaOperasional::create([
                'tanggal' => $request->tanggal,
                'jenis_biaya' => $item['jenis_biaya'],
                'keterangan' => $item['keterangan'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return redirect()->route('biaya-operasional.index')->with('success', 'Biaya operasional berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit biaya operasional.
     */
    public function edit(BiayaOperasional $biayaOperasional)
    {
        return view('admin.biaya-operasional.edit', compact('biayaOperasional'));
    }

    /**
     * Memperbarui data biaya operasional di database.
     */
    public function update(Request $request, BiayaOperasional $biayaOperasional)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_biaya' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
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