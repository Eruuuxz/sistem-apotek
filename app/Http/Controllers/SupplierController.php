<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Obat;
use App\Models\Pembelian; // Import model Pembelian
use App\Models\Retur; // Import model Retur
use App\Models\SuratPesanan; // Import model SuratPesanan
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('admin.master.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:supplier,kode',
            'nama' => 'required',
            'alamat' => 'nullable',
            'kota' => 'nullable',
            'telepon' => 'nullable',
        ]);

        Supplier::create($request->all());
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
        public function show(Supplier $supplier)
    {
        // Ambil riwayat pembelian dengan nama paginator 'pembelianPage'
        $riwayatPembelian = $supplier->pembelian()
                                ->latest()
                                ->paginate(10, ['*'], 'pembelianPage');

        // Ambil riwayat Surat Pesanan dengan nama paginator 'spPage'
        $riwayatSuratPesanan = $supplier->suratPesanans() // Gunakan relasi 'suratPesanans'
                                    ->latest()
                                    ->paginate(10, ['*'], 'spPage');
        
        // Ambil riwayat retur
        $riwayatRetur = Retur::where('jenis', 'pembelian')
                             ->whereHas('pembelian', function ($query) use ($supplier) {
                                 $query->where('supplier_id', $supplier->id);
                             })
                             ->latest()
                             ->paginate(5, ['*'], 'returPage');

        // Ambil data obat dari supplier ini dengan nama paginator 'obatPage'
        $obats = $supplier->obats() // Gunakan relasi 'obats' (plural)
                         ->latest()
                         ->paginate(10, ['*'], 'obatPage');

        // Kirim semua data ke view
        return view('admin.master.supplier.show', compact(
            'supplier',
            'riwayatPembelian',
            'riwayatSuratPesanan',
            'riwayatRetur',
            'obats' // Pastikan 'obats' ada di sini
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.master.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'kode' => 'required|unique:supplier,kode,' . $supplier->id,
            'nama' => 'required',
            'alamat' => 'nullable',
            'kota' => 'nullable',
            'telepon' => 'nullable',
        ]);

        $supplier->update($request->all());
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
