@extends('layouts.admin')

@section('title', 'Retur Barang')

@section('content')
<h1 class="text-2xl font-bold mb-4">Retur Barang</h1>

<a href="/retur/create" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Retur</a>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">No Retur</th>
            <th class="px-4 py-2">Tanggal</th>
            <th class="px-4 py-2">Jenis</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">RT001</td>
            <td class="border px-4 py-2">2025-08-11</td>
            <td class="border px-4 py-2">Penjualan</td>
            <td class="border px-4 py-2">Rp 50.000</td>
            <td class="border px-4 py-2">
                <a href="/retur/detail" class="text-blue-500">Detail</a>
            </td>
        </tr>
    </tbody>
</table>
@endsection
