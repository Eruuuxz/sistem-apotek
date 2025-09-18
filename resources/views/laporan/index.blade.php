@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="container mx-auto px-6 py-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">📊 Dashboard Laporan</h1>

    <!-- Filter Periode -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="periode" class="block text-sm font-medium text-gray-700">Pilih Periode</label>
                <input type="month" id="periode" name="periode" value="{{ $periode }}"
                       class="mt-1 border rounded px-3 py-2 w-full focus:ring focus:ring-blue-300">
            </div>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                🔍 Tampilkan
            </button>
        </form>
    </div>

    <!-- Tabs -->
    <div x-data="{ tab: 'penjualan' }" class="bg-white shadow rounded-lg">
        <div class="flex border-b bg-gray-50 rounded-t-lg">
            <button @click="tab = 'penjualan'" 
                :class="tab === 'penjualan' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'text-gray-600'" 
                class="px-6 py-3 flex-1 text-center transition">
                📈 Penjualan
            </button>
            <button @click="tab = 'profit'" 
                :class="tab === 'profit' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'text-gray-600'" 
                class="px-6 py-3 flex-1 text-center transition">
                💰 Profit
            </button>
            <button @click="tab = 'stok'" 
                :class="tab === 'stok' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'text-gray-600'" 
                class="px-6 py-3 flex-1 text-center transition">
                📦 Stok
            </button>
            <button @click="tab = 'customer'" 
                :class="tab === 'customer' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'text-gray-600'" 
                class="px-6 py-3 flex-1 text-center transition">
                👥 Customer
            </button>
        </div>

        <div class="p-6">
            <div x-show="tab === 'penjualan'">
                @include('laporan.partials.penjualan')
            </div>
            <div x-show="tab === 'profit'" x-cloak>
                @include('laporan.partials.profit')
            </div>
            <div x-show="tab === 'stok'" x-cloak>
                @include('laporan.partials.stok')
            </div>
            <div x-show="tab === 'customer'" x-cloak>
                @include('laporan.partials.customer_analytics')
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">📤 Export Laporan</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('laporan.penjualan.export',['pdf','periode'=>$periode]) }}" 
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Penjualan PDF
            </a>
            <a href="{{ route('laporan.penjualan.export',['excel','periode'=>$periode]) }}" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Penjualan Excel
            </a>

            <a href="{{ route('laporan.stok.export',['pdf','periode'=>$periode]) }}" 
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Stok PDF
            </a>
            <a href="{{ route('laporan.stok.export',['excel','periode'=>$periode]) }}" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Stok Excel
            </a>

            <a href="{{ route('laporan.pelanggan.export',['pdf','periode'=>$periode]) }}" 
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Pelanggan PDF
            </a>
            <a href="{{ route('laporan.pelanggan.export',['excel','periode'=>$periode]) }}" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Pelanggan Excel
            </a>

            <a href="{{ route('laporan.laba.export',['pdf','periode'=>$periode]) }}" 
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Laba PDF
            </a>
            <a href="{{ route('laporan.laba.export',['excel','periode'=>$periode]) }}" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-center shadow">
                Laba Excel
            </a>
        </div>
    </div>
</div>
@endsection
