{{-- File: /kasir/riwayat.blade.php --}}
@extends('layouts.kasir')

@section('title', 'Riwayat Penjualan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Riwayat Penjualan</h1>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">No Nota</th>
            <th class="px-4 py-2 text-left">Tanggal</th>
            <th class="px-4 py-2 text-left">Kasir</th>
            <th class="px-4 py-2 text-right">Total</th>
            <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="border px-4 py-2">{{ $row->no_nota }}</td>
            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
            <td class="border px-4 py-2">{{ $row->kasir_nama }}</td>
            <td class="border px-4 py-2 text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
            <td class="border px-4 py-2 text-center">
                <a href="{{ route('penjualan.show', $row->id) }}" class="text-blue-500 hover:underline">Detail</a> |
                <a href="{{ route('penjualan.struk', $row->id) }}" class="text-green-600 hover:underline">Cetak</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="border px-4 py-2 text-center">Tidak ada data penjualan.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $data->links() }}
</div>
@endsection