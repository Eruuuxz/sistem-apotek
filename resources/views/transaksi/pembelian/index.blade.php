{{-- File: /pembelian/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@section('content')
<h1 class="text-2xl font-bold mb-4">Transaksi Pembelian</h1>

<a href="{{ route('pembelian.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Pembelian</a>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">No Faktur</th>
            <th class="px-4 py-2 text-left">Tanggal</th>
            <th class="px-4 py-2 text-left">Supplier</th>
            <th class="px-4 py-2 text-right">Total</th>
            <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="border px-4 py-2">{{ $row->no_faktur }}</td>
            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
            <td class="border px-4 py-2">{{ $row->supplier->nama ?? '-' }}</td>
            <td class="border px-4 py-2 text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
            <td class="border px-4 py-2 text-center">
                <a href="{{ route('pembelian.faktur', $row->id) }}" class="text-blue-500 hover:underline">Lihat Faktur</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="border px-4 py-2 text-center">Tidak ada data pembelian.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $data->links() }}
</div>
@endsection