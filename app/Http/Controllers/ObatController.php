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
                'kode' => 'required|unique:obats,kode',
                'nama' => 'required',
                'kategori' => 'required',
                'sediaan' => 'nullable|string', 
                'kemasan_besar' => 'nullable|string', 
                'satuan_terkecil' => 'required|string', 
                'rasio_konversi' => 'required_if:kemasan_besar,!=,null|integer|min:1', // NEW : Wajib jika ada kemasan besar
                'stok' => 'required|integer|min:0',
                'min_stok' => 'nullable|integer|min:0',
                'harga_dasar' => 'required|numeric|min:0',
                'persen_untung' => 'nullable|numeric|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'supplier_id' => 'required|exists:suppliers,id',
                'expired_date' => 'nullable|date|after_or_equal:today',
            ], [
                'rasio_konversi.required_if' => 'Rasio konversi wajib diisi jika kemasan besar dipilih.',
            ]);

            Obat::create($request->all());

            return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan.');
        }

    public function edit(Obat $obat)
    {
        $suppliers = Supplier::all();
        return view('master.obat.edit', compact('obat', 'suppliers'));
    }

    public function update(Request $request, Obat $obat)
        {
            $request->validate([
                'kode' => 'required|unique:obats,kode,' . $obat->id,
                'nama' => 'required',
                'kategori' => 'required',
                'sediaan' => 'nullable|string', 
                'kemasan_besar' => 'nullable|string', 
                'satuan_terkecil' => 'required|string', 
                'rasio_konversi' => 'required_if:kemasan_besar,!=,null|integer|min:1', 
                'stok' => 'required|integer|min:0',
                'min_stok' => 'nullable|integer|min:0',
                'harga_dasar' => 'required|numeric|min:0',
                'persen_untung' => 'nullable|numeric|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'supplier_id' => 'required|exists:suppliers,id',
                'expired_date' => 'nullable|date|after_or_equal:today',
            ], [
                'rasio_konversi.required_if' => 'Rasio konversi wajib diisi jika kemasan besar dipilih.',
            ]);

            $obat->update($request->all());

            return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
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

