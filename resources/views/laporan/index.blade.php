@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
        <a href="{{ route('laporan.profit') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-teal-600">📊</div>
            <h2 class="font-bold text-2xl">Laporan Profit</h2>
            <p class="text-gray-500 text-lg">Ringkasan keuntungan bulanan dari penjualan.</p>
        </a>
        <a href="{{ route('laporan.stok') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-red-600">📦</div>
            <h2 class="font-bold text-2xl">Laporan Stok</h2>
            <p class="text-gray-500 text-lg">Daftar stok obat dan status ketersediaan.</p>
        </a>

        <a href="{{ route('laporan.movement_analysis') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-purple-600">🔄</div>
            <h2 class="font-bold text-2xl">Laporan Perputaran Stok</h2>
            <p class="text-gray-500 text-lg">Menganalisis pergerakan stok (cepat, lambat, mati).</p>
        </a>
        <a href="{{ route('laporan.customer_analytics') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-pink-600">👥</div>
            <h2 class="font-bold text-2xl">Analisis Pelanggan</h2>
            <p class="text-gray-500 text-lg">Wawasan tentang perilaku dan loyalitas pelanggan.</p>
        </a>
        <a href="{{ route('laporan.daily_sales_recap') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-orange-600">📈</div>
            <h2 class="font-bold text-2xl">Rekap Penjualan Harian</h2>
            <p class="text-gray-500 text-lg">Ringkasan penjualan per hari dalam satu bulan.</p>
        </a>
        <a href="{{ route('laporan.stock_movement_analysis') }}"
            class="bg-white p-10 shadow-lg rounded-xl hover:shadow-2xl transition duration-300 flex flex-col items-start gap-4">
            <div class="text-5xl text-yellow-600">📦➡️</div>
            <h2 class="font-bold text-2xl">Analisis Pergerakan Stok</h2>
            <p class="text-gray-500 text-lg">Melacak semua barang masuk dan keluar dari gudang.</p>
        </a>
    </div>

@endsection