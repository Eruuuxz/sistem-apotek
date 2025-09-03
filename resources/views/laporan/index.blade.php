@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('laporan.penjualan') }}" 
       class="bg-white p-6 shadow-md rounded-lg hover:shadow-xl transition duration-300 flex flex-col items-start gap-2">
        <div class="text-3xl text-blue-600">💰</div>
        <h2 class="font-bold text-lg">Laporan Penjualan (Harian)</h2>
        <p class="text-gray-500 text-sm">Ringkasan transaksi harian dan total penjualan.</p>
    </a>

    <a href="{{ route('laporan.penjualan.bulanan') }}" 
       class="bg-white p-6 shadow-md rounded-lg hover:shadow-xl transition duration-300 flex flex-col items-start gap-2">
        <div class="text-3xl text-green-600">📅</div>
        <h2 class="font-bold text-lg">Laporan Penjualan (Bulanan)</h2>
        <p class="text-gray-500 text-sm">Ringkasan transaksi bulanan dan total penjualan.</p>
    </a>

    <a href="{{ route('laporan.stok') }}" 
       class="bg-white p-6 shadow-md rounded-lg hover:shadow-xl transition duration-300 flex flex-col items-start gap-2">
        <div class="text-3xl text-red-600">📦</div>
        <h2 class="font-bold text-lg">Laporan Stok</h2>
        <p class="text-gray-500 text-sm">Daftar stok obat dan status ketersediaan.</p>
    </a>
</div>

@endsection