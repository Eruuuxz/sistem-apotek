@extends('layouts.kasir')

@section('title', 'Riwayat Penjualan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Riwayat Penjualan</h1>

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">No Nota</th>
            <th class="px-4 py-2">Tanggal</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2">PJ001</td>
            <td class="border px-4 py-2">2025-08-11</td>
            <td class="border px-4 py-2">Rp 150.000</td>
            <td class="border px-4 py-2">
                <a href="#" class="text-blue-500">Cetak Ulang</a>
            </td>
        </tr>
    </tbody>
</table>
@endsection
