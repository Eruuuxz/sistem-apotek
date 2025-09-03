@extends('layouts.admin')

@section('title','Laporan Stok Menipis')

@section('content')

{{-- Filter Threshold --}}
<form method="GET" class="flex flex-wrap gap-2 mb-4 items-center bg-white p-4 rounded shadow">
    <input type="number" name="threshold" value="{{ $threshold }}" 
           class="border px-3 py-2 rounded w-32" 
           placeholder="Ambang (mis. 5)">
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Terapkan</button>
    <a href="{{ route('laporan.stok') }}" 
       class="px-4 py-2 rounded border hover:bg-gray-100 transition">Reset</a>
</form>

{{-- Tabel Stok --}}
<div class="overflow-x-auto bg-white shadow-md rounded">
    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border text-left">Kode</th>
                <th class="px-4 py-2 border text-left">Nama Obat</th> 
                <th class="px-4 py-2 border text-right">Stok</th>
                <th class="px-4 py-2 border text-right">Min Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $o) 
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">{{ $o->kode }}</td> 
                <td class="border px-4 py-2">{{ $o->nama }}</td> 
                <td class="border px-4 py-2 text-right font-medium {{ $o->stok <= $threshold ? 'text-red-600' : '' }}">
                    {{ $o->stok }}
                </td> 
                <td class="border px-4 py-2 text-right">{{ $o->min_stok ?? '-' }}</td> 
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">{{ $data->links() }}</div>

@endsection
