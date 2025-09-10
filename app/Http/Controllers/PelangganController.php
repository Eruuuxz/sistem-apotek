<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request; // Tetap import Request untuk show, destroy, dll.
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PelangganRequest; // Import PelangganRequest

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::latest()->paginate(10);
        return view('master.pelanggan.index', compact('pelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PelangganRequest $request) // Gunakan PelangganRequest
    {
        $data = $request->validated(); // Ambil data yang sudah divalidasi

        // Handle file upload KTP
        if ($request->hasFile('file_ktp')) {
            $file = $request->file('file_ktp');
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Simpan file di direktori 'public/ktp_files'
            $data['file_ktp'] = $file->storeAs('public/ktp_files', $fileName, 'public'); // Simpan path lengkap
        } else {
            $data['file_ktp'] = null; // Pastikan null jika tidak ada file
        }

        Pelanggan::create($data);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        return view('master.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('master.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PelangganRequest $request, Pelanggan $pelanggan) // Gunakan PelangganRequest
    {
        $data = $request->validated(); // Ambil data yang sudah divalidasi

        // Handle file upload KTP
        if ($request->hasFile('file_ktp')) {
            // Hapus file lama jika ada
            if ($pelanggan->file_ktp) {
                Storage::disk('public')->delete($pelanggan->file_ktp);
            }
            $file = $request->file('file_ktp');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $data['file_ktp'] = $file->storeAs('public/ktp_files', $fileName, 'public');
        } elseif ($request->input('remove_file_ktp')) { // Jika ada permintaan untuk menghapus file
            if ($pelanggan->file_ktp) {
                Storage::disk('public')->delete($pelanggan->file_ktp);
            }
            $data['file_ktp'] = null;
        } else {
            // Jika tidak ada file baru diupload dan tidak ada permintaan hapus, pertahankan file lama
            // Pastikan file_ktp tidak di-unset jika tidak ada perubahan pada file
            $data['file_ktp'] = $pelanggan->file_ktp; 
        }

        $pelanggan->update($data);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Hapus file KTP terkait jika ada
        if ($pelanggan->file_ktp) {
            Storage::disk('public')->delete($pelanggan->file_ktp);
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }
}