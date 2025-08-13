@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('content')
<h1 class="text-2xl font-bold mb-4">Data Supplier</h1>

<a href="/supplier/create" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Supplier</a>

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
        <tr>
            <td class="border px-4 py-2">SUP001</td>
            <td class="border px-4 py-2">PT Farmasi Sehat</td>
            <td class="border px-4 py-2">Jl. Merdeka No. 1</td>
            <td class="border px-4 py-2">Jakarta</td>
            <td class="border px-4 py-2">021-123456</td>
            <td class="border px-4 py-2">
                <a href="/supplier/edit" class="text-yellow-500">Edit</a> |
                <a href="#" class="text-red-500">Hapus</a>
            </td>
        </tr>
    </tbody>
</table>
@endsection
