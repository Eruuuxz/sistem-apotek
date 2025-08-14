{{-- File: /retur/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Retur Barang')

@section('content')
<h1 class="text-2xl font-bold mb-4">Retur Barang</h1>

<a href="{{ route('retur.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Retur</a>

<form method="GET" class="flex gap-2 mb-4 mt-4 items-end">
    <div>
        <label for="jenis_filter" class="block text-sm font-medium text-gray-700">Jenis Retur</label>
        <select name="jenis" id="jenis_filter" class="border rounded px-2 py-1">
            <option value="">Semua Jenis</option>
            <option value="pembelian" {{ request('jenis')=='pembelian'?'selected':'' }}>Pembelian</option>
            <option value="penjualan" {{ request('jenis')=='penjualan'?'selected':'' }}>Penjualan</option>
        </select>
    </div>
    <div>
        <label for="from_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
        <input type="date" name="from" id="from_date" value="{{ request('from') }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label for="to_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
        <input type="date" name="to" id="to_date" value="{{ request('to') }}" class="border rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded h-fit">Filter</button>
    <a href="{{ route('retur.index') }}" class="bg-gray-400 text-white px-3 py-1 rounded h-fit">Reset</a>
</form>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 text-left">No Retur</th>
            <th class="px-4 py-2 text-left">Tanggal</th>
            <th class="px-4 py-2 text-left">Jenis</th>
            <th class="px-4 py-2 text-right">Total</th>
            <th class="px-4 py-2 text-center">Keterangan</th>
            <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="border px-4 py-2">{{ $row->no_retur }}</td>
            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
            <td class="border px-4 py-2">{{ ucfirst($row->jenis) }}</td>
            <td class="border px-4 py-2 text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
            <td class="border px-4 py-2">{{ $row->keterangan ?? '-' }}</td>
            <td class="border px-4 py-2 text-center">
                {{-- <a href="/retur/detail/{{ $row->id }}" class="text-blue-500">Detail</a> --}}
                {{-- Jika ada halaman detail retur, uncomment dan sesuaikan rutenya --}}
                -
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="border px-4 py-2 text-center">Tidak ada data retur.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $data->links() }}
</div>
@endsection