@extends('layouts.kasir')

@section('title', 'Riwayat Penjualan')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm-inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Penjualan</h1>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-sm border-collapse border border-gray-200">
            <thead class="bg-gray-100 rounded-t-lg">
                <tr>
                    <th class="px-4 py-2 text-left">No Nota</th>
                    <th class="px-4 py-2 text-left">Tanggal & Waktu</th>
                    <th class="px-4 py-2 text-left">Kasir</th>
                    <th class="px-4 py-2 text-left">Nama Pelanggan</th>
                    <th class="px-4 py-2 text-right">Total</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border px-4 py-2">{{ $row->no_nota }}</td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d H:i:s') }}</td>
                        <td class="border px-4 py-2">{{ $row->kasir->name ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $row->pelanggan->nama ?? $row->nama_pelanggan ?? '-' }}</td>
                        <td class="border px-4 py-2 text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2 text-center flex justify-center gap-2">
                            <a href="{{ route('penjualan.show', $row->id) }}" class="text-blue-500 hover:underline">Detail</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('pos.print.faktur', $row->id) }}" class="text-green-600 hover:underline">Cetak</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-4 py-2 text-center text-gray-500">Tidak ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $data->links() }}
    </div>
@endsection