@extends('layouts.admin')

@section('title','Laporan Stok Menipis')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan Stok Menipis</h1>

<form method="GET" class="flex gap-2 mb-4">
    <input type="number" name="threshold" value="{{ $threshold }}" class="border px-3 py-2" placeholder="Ambang (mis. 5)">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Terapkan</button>
    <a href="{{ route('laporan.stok') }}" class="px-4 py-2 rounded border">Reset</a>
</form>

<table class="w-full bg-white shadow rounded text-sm">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-3 py-2">Kode</th>
            <th class="px-3 py-2">Nama Obat</th> 
            <th class="px-3 py-2 text-right">Stok</th>
            <th class="px-3 py-2 text-right">Min Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $o) 
        <tr>
            <td class="border px-3 py-2">{{ $o->kode }}</td> 
            <td class="border px-3 py-2">{{ $o->nama }}</td> 
            <td class="border px-3 py-2 text-right">{{ $o->stok }}</td> 
            <td class="border px-3 py-2 text-right">{{ $o->min_stok ?? ' - ' }}</td> 
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">{{ $data->links() }}</div>
@endsection
