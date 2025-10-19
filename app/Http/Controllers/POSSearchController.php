<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class POSSearchController extends Controller
{
    /**
     * Mencari obat untuk fitur autocomplete.
     */
    public function searchObat(Request $request)
    {
        $keyword = $request->get('q');

        $obat = Obat::where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_date')
                    ->orWhere('expired_date', '>', now());
            })
            ->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('kode', 'like', '%' . $keyword . '%');
            })
            ->orderBy('nama')
            ->limit(10)
            ->get(['id', 'kode', 'nama', 'kategori', 'expired_date', 'harga_jual', 'stok', 'is_psikotropika', 'ppn_rate', 'ppn_included']);

        return response()->json($obat);
    }

    /**
     * Mencari pelanggan untuk fitur autocomplete.
     */
    public function searchPelanggan(Request $request)
    {
        $query = $request->input('q');
        $pelanggans = Pelanggan::where('tipe', 'tetap')
            ->where(function($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                  ->orWhere('telepon', 'like', "%{$query}%")
                  ->orWhere('no_ktp', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
        return response()->json($pelanggans);
    }

    /**
     * Menambahkan pelanggan secara cepat dari halaman POS.
     */
    public function addPelangganCepat(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ]);
        
        $data['tipe'] = 'tetap'; // Pelanggan baru otomatis 'tetap'
        $pelanggan = Pelanggan::create($data);
        return response()->json($pelanggan);
    }
}