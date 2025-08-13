@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@section('content')
<h1 class="text-2xl font-bold mb-4">Transaksi Pembelian</h1>

<a href="/pembelian/create" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Pembelian</a>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">No Faktur</th>
            <th class="px-4 py-2">Tanggal</th>
            <th class="px-4 py-2">Supplier</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">FPB-2025-001</td>
            <td class="border px-4 py-2">2025-08-11</td>
            <td class="border px-4 py-2">PT Farmasi Sehat</td>
            <td class="border px-4 py-2">Rp 500.000</td>
            <td class="border px-4 py-2">
                <a href="{{ route('pembelian.faktur') }}" class="text-blue-500 hover:underline">Lihat Faktur</a>
            </td>
        </tr>
    </tbody>
</table>
@endsection
