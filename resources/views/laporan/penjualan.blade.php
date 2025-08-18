@extends('layouts.admin')

@section('title','Laporan Penjualan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan Penjualan</h1>

<form method="GET" class="flex gap-2 mb-4">
    <input type="date" name="from" value="{{ $from }}" class="border px-3 py-2">
    <input type="date" name="to" value="{{ $to }}" class="border px-3 py-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    <a href="{{ route('laporan.penjualan') }}" class="px-4 py-2 rounded border">Reset</a>
    <a href="{{ route('laporan.penjualan.pdf', request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded">PDF</a>
    <a href="{{ route('laporan.penjualan.excel', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded">Excel</a>
</form>

<table class="w-full bg-white shadow rounded text-sm">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-3 py-2">No Nota</th>
            <th class="px-3 py-2">Tanggal</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2 text-center">Item</th>
            <th class="px-3 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="border px-3 py-2">{{ $row->no_nota }}</td>
            <td class="border px-3 py-2">{{ $row->tanggal }}</td>
            <td class="border px-3 py-2 text-right">Rp {{ number_format($row->total,0,',','.') }}</td>
            <td class="border px-3 py-2 text-center">{{ $row->detail_count }}</td>
            <td class="border px-3 py-2">
                <a class="text-blue-600" href="{{ route('penjualan.show',$row->id) }}">Detail</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $data->links() }}</div>

<div class="mt-4 text-right">
    <span class="font-semibold">Total (sesuai filter):</span>
    <span class="font-bold">Rp {{ number_format($totalAll,0,',','.') }}</span>
</div>
@endsection