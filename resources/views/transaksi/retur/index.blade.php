@extends('layouts.admin')

@section('title', 'Retur Barang')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <a href="{{ route('retur.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Tambah Retur</a>
        </div>

        {{-- Filter --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded shadow">
            <div>
                <label for="jenis_filter" class="block text-sm font-medium text-gray-700 mb-1">Jenis Retur</label>
                <select name="jenis" id="jenis_filter" class="w-full border rounded px-2 py-1">
                    <option value="">Semua Jenis</option>
                    <option value="pembelian" {{ request('jenis') == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                    <option value="penjualan" {{ request('jenis') == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="from" id="from_date" value="{{ request('from') }}"
                    class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" id="to_date" value="{{ request('to') }}"
                    class="w-full border rounded px-2 py-1">
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Filter</button>
                <a href="{{ route('retur.index') }}"
                    class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500 transition">Reset</a>
            </div>
        </form>

        {{-- Tabel Retur --}}
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="w-full table-auto border-collapse text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">No Retur</th>
                        <th class="border px-4 py-2 text-left">Tanggal</th>
                        <th class="border px-4 py-2 text-left">Jenis</th>
                        <th class="border px-4 py-2 text-right">Total</th>
                        <th class="border px-4 py-2 text-center">Keterangan</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2 font-medium">{{ $row->no_retur }}</td>
                            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
                            <td class="border px-4 py-2 capitalize">{{ $row->jenis }}</td>
                            <td class="border px-4 py-2 text-right font-semibold text-blue-600">Rp
                                {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2 text-center">{{ $row->keterangan ?? '-' }}</td>
                            <td class="border px-4 py-2 text-center">
                                {{-- Contoh tombol aksi, bisa diisi sesuai kebutuhan --}}
                                <a href="#" class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-4 text-center text-gray-500">Belum ada data retur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-2 flex justify-end">
            {{ $data->links() }}
        </div>
    </div>
@endsection