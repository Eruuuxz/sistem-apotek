@extends('layouts.admin')

@section('title', 'Transaksi Penjualan')

@section('content')

    <table class="w-full mt-4 bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2">No Nota</th>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">Kasir</th>
                <th class="px-4 py-2">Total</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-4 py-2">PJ001</td>
                <td class="border px-4 py-2">2025-08-11</td>
                <td class="border px-4 py-2">Budi</td>
                <td class="border px-4 py-2">Rp 200.000</td>
                <td class="border px-4 py-2">
                    <a href="/penjualan/detail" class="text-blue-500">Detail</a>
                </td>
            </tr>
        </tbody>
    </table>
@endsection