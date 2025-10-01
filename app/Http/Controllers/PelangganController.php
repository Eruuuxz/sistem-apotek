<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PelangganRequest;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statusMember = $request->query('status_member');

        if ($statusMember === 'member') {
            // Mengambil pelanggan member langsung dari tabel pelanggan
            $pelanggansQuery = Pelanggan::where('status_member', 'member')->latest();
        } elseif ($statusMember === 'non_member') {
            // Mengambil pelanggan non-member dari tabel penjualan
            $pelanggansQuery = Penjualan::select(
                'penjualan.nama_pelanggan as nama',
                'penjualan.telepon_pelanggan as telepon',
                'penjualan.alamat_pelanggan as alamat',
                DB::raw("'non_member' as status_member"),
                DB::raw("0 as point"),
                DB::raw("NULL as no_ktp"),
                DB::raw("NULL as id"), // ID tidak tersedia untuk non-member
                DB::raw("NULL as file_ktp") // File KTP tidak tersedia untuk non-member
            )
            ->leftJoin('pelanggan', 'pelanggan.nama', '=', 'penjualan.nama_pelanggan')
            ->whereNull('pelanggan.id')
            ->distinct()
            ->groupBy('penjualan.nama_pelanggan', 'penjualan.telepon_pelanggan', 'penjualan.alamat_pelanggan');
        } else {
            // Mengambil semua data pelanggan dengan UNION
            $membersQuery = Pelanggan::select(
                'nama',
                'telepon',
                'alamat',
                'status_member',
                'point',
                'no_ktp',
                'id',
                'file_ktp'
            )->where('status_member', 'member');

            $nonMembersQuery = Penjualan::select(
                'penjualan.nama_pelanggan as nama',
                'penjualan.telepon_pelanggan as telepon',
                'penjualan.alamat_pelanggan as alamat',
                DB::raw("'non_member' as status_member"),
                DB::raw("0 as point"),
                DB::raw("NULL as no_ktp"),
                DB::raw("NULL as id"),
                DB::raw("NULL as file_ktp")
            )
            ->leftJoin('pelanggan', 'pelanggan.nama', '=', 'penjualan.nama_pelanggan')
            ->whereNull('pelanggan.id')
            ->distinct()
            ->groupBy('penjualan.nama_pelanggan', 'penjualan.telepon_pelanggan', 'penjualan.alamat_pelanggan');

            // Gabungkan kedua query
            $pelanggansQuery = $membersQuery->unionAll($nonMembersQuery);
        }

        $pelanggans = $pelanggansQuery->paginate(10)->withQueryString();

        return view('admin.master.pelanggan.index', compact('pelanggans', 'statusMember'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PelangganRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file_ktp')) {
            $file = $request->file('file_ktp');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $data['file_ktp'] = $file->storeAs('public/ktp_files', $fileName, 'public');
        } else {
            $data['file_ktp'] = null;
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
        return view('admin.master.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('admin.master.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PelangganRequest $request, Pelanggan $pelanggan)
    {
        $data = $request->validated();

        if ($request->hasFile('file_ktp')) {
            if ($pelanggan->file_ktp) {
                Storage::disk('public')->delete($pelanggan->file_ktp);
            }
            $file = $request->file('file_ktp');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $data['file_ktp'] = $file->storeAs('public/ktp_files', $fileName, 'public');
        } elseif ($request->input('remove_file_ktp')) {
            if ($pelanggan->file_ktp) {
                Storage::disk('public')->delete($pelanggan->file_ktp);
            }
            $data['file_ktp'] = null;
        } else {
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
        if ($pelanggan->file_ktp) {
            Storage::disk('public')->delete($pelanggan->file_ktp);
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
