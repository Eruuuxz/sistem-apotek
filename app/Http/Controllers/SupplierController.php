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
        // Load relasi yang diperlukan
        $supplier->load('obat');

        // Ambil riwayat pembelian
        $riwayatPembelian = Pembelian::where('supplier_id', $supplier->id)
                                    ->with('detail.obat')
                                    ->latest()
                                    ->paginate(5, ['*'], 'pembelian_page');

        // Ambil riwayat retur pembelian (jika ada)
        // Asumsi retur pembelian memiliki jenis 'pembelian' dan transaksi_id mengacu ke pembelian_id
        $riwayatRetur = Retur::where('jenis', 'pembelian')
                            ->whereHas('pembelian', function ($query) use ($supplier) {
                                $query->where('supplier_id', $supplier->id);
                            })
                            ->with('details.obat')
                            ->latest()
                            ->paginate(5, ['*'], 'retur_page');

        // Ambil riwayat Surat Pesanan
        $riwayatSuratPesanan = SuratPesanan::where('supplier_id', $supplier->id)
                                            ->with('details.obat')
                                            ->latest()
                                            ->paginate(5, ['*'], 'sp_page');

        return view('admin.master.supplier.show', compact('supplier', 'riwayatPembelian', 'riwayatRetur', 'riwayatSuratPesanan'));
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
