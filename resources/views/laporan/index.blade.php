@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <a href="{{ route('laporan.penjualan') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-blue-600">💰</div>
            <h2 class="font-bold text-2xl">Laporan Penjualan (Harian)</h2>
            <p class="text-gray-500 text-lg">Ringkasan transaksi harian dan total penjualan.</p>
        </a>

        <a href="{{ route('laporan.penjualan.bulanan') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-green-600">📅</div>
            <h2 class="font-bold text-2xl">Laporan Penjualan (Bulanan)</h2>
            <p class="text-gray-500 text-lg">Ringkasan transaksi bulanan dan total penjualan.</p>
        </a>

        <a href="{{ route('laporan.stok') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-red-600">📦</div>
            <h2 class="font-bold text-2xl">Laporan Stok</h2>
            <p class="text-gray-500 text-lg">Daftar stok obat dan status ketersediaan.</p>
        </a>
    </div>

@endsection