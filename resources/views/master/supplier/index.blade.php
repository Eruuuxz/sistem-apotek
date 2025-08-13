@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('content')
<h1 class="text-2xl font-bold mb-4">Data Supplier</h1>

@if (session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('supplier.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Supplier</a>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">Kode</th>
            <th class="px-4 py-2">Nama Supplier</th>
            <th class="px-4 py-2">Alamat</th>
            <th class="px-4 py-2">Kota</th>
            <th class="px-4 py-2">Telepon</th>
            <th class="px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($suppliers as $supplier)
        <tr>
            <td class="border px-4 py-2">{{ $supplier->kode }}</td>
            <td class="border px-4 py-2">{{ $supplier->nama }}</td>
            <td class="border px-4 py-2">{{ $supplier->alamat }}</td>
            <td class="border px-4 py-2">{{ $supplier->kota }}</td>
            <td class="border px-4 py-2">{{ $supplier->telepon }}</td>
            <td class="border px-4 py-2">
                <a href="{{ route('supplier.edit', $supplier->id) }}" class="text-yellow-500">Edit</a> |
                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="inline">
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

