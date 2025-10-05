<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $pelanggansQuery = Pelanggan::query();

        if ($filter === 'tetap') {
            $pelanggansQuery->where('tipe', 'tetap');
        }

        $pelanggans = $pelanggansQuery->orderBy('nama', 'asc')->paginate(15)->withQueryString();
        
        // PERBAIKAN PATH
        return view('admin.master.pelanggan.index', compact('pelanggans', 'filter'));
    }

    public function create()
    {
        // PERBAIKAN PATH
        return view('admin.master.pelanggan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_ktp' => 'nullable|string|max:50',
            'file_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data['tipe'] = 'tetap';

        if ($request->hasFile('file_ktp')) {
            $data['file_ktp'] = $request->file('file_ktp')->store('ktp_files', 'public');
        }

        Pelanggan::create($data);
        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan tetap berhasil ditambahkan.');
    }

    public function show(Pelanggan $pelanggan)
    {
        // PERBAIKAN PATH
        return view('admin.master.pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        // PERBAIKAN PATH
        return view('admin.master.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_ktp' => 'nullable|string|max:50',
            'file_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('file_ktp')) {
            if ($pelanggan->file_ktp) {
                Storage::disk('public')->delete($pelanggan->file_ktp);
            }
            $data['file_ktp'] = $request->file('file_ktp')->store('ktp_files', 'public');
        }
        $pelanggan->update($data);
        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->file_ktp) {
            Storage::disk('public')->delete($pelanggan->file_ktp);
        }
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan tetap berhasil dihapus.');
    }

    public function riwayatPembelianJson(Pelanggan $pelanggan)
    {
        $riwayat = $pelanggan->penjualan()
            ->where('tanggal', '>=', now()->subMonth())
            ->with('details.obat')
            ->latest('tanggal')
            ->get();
        return response()->json($riwayat);
    }
}