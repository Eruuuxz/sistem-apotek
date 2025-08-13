@extends('layouts.admin')

@section('title', 'Data Barang')

@section('content')
<h1 class="text-2xl font-bold mb-4">Data Barang</h1>

@if (session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<a href="/barang/create" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Barang</a>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">Kode</th>
            <th class="px-4 py-2">Nama Barang</th>
            <th class="px-4 py-2">Harga Jual</th>
            <th class="px-4 py-2">Stok</th>
            <th class="px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($barang as $item)
        <tr>
            <td class="border px-4 py-2">{{ $item->kode }}</td>
            <td class="border px-4 py-2">{{ $item->nama }}</td>
            <td class="border px-4 py-2">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
            <td class="border px-4 py-2">{{ $item->stok }}</td>
            <td class="border px-4 py-2">
                <a href="{{ url('/barang/' . $item->id . '/edit') }}" class="text-yellow-500">Edit</a> |
                <form action="{{ url('/barang/' . $item->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500" onclick="return confirm('Yakin ingin hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
