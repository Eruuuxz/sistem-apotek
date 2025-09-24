@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Laporan</h2>
                <p class="text-sm text-gray-500">Analisis performa apotek berdasarkan periode yang dipilih.</p>
            </div>
        </div>

        <!-- Filter Periode -->
        <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg mb-6">
            <div>
                <label for="periode" class="block text-sm font-medium text-gray-700">Pilih Periode</label>
                <input type="month" id="periode" name="periode" value="{{ $periode }}"
                       class="mt-1 border rounded-md px-3 py-2 w-full focus:ring focus:ring-blue-300">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow font-semibold">
                Tampilkan
            </button>
        </form>

        <!-- Tabs -->
        <div x-data="{ tab: 'penjualan' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'penjualan'" :class="tab === 'penjualan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm items-center gap-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                        Penjualan
                    </button>
                    <button @click="tab = 'profit'" :class="tab === 'profit' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm items-center gap-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                        Profit
                    </button>
                    <button @click="tab = 'stok'" :class="tab === 'stok' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm items-center gap-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                        Stok
                    </button>
                    <button @click="tab = 'customer'" :class="tab === 'customer' ? 'border-b-2 border-blue-600 font-semibold text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm items-center gap-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.14 5.441A3 3 0 0 1 4.5 12.72M18.72 18.72A3.001 3.001 0 0 1 12.72 18.72m-7.14 0A3.001 3.001 0 0 1 12 12.72m-3.741-.479A3 3 0 0 1 12 4.5m0-3.75v3.75m0 10.5a3 3 0 0 1-3 3m3-3a3 3 0 0 0 3 3m-3-3a3 3 0 0 1 3-3m-3 3a3 3 0 0 0-3-3" /></svg>
                        Customer
                    </button>
                </nav>
            </div>
            <div class="py-6">
                <div x-show="tab === 'penjualan'">@include('laporan.partials.penjualan')</div>
                <div x-show="tab === 'profit'" x-cloak>@include('laporan.partials.profit')</div>
                <div x-show="tab === 'stok'" x-cloak>@include('laporan.partials.stok')</div>
                <div x-show="tab === 'customer'" x-cloak>@include('laporan.partials.customer_analytics')</div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Export Laporan</h2>
        <p class="text-sm text-gray-500 mb-6">Unduh laporan dalam format PDF atau Excel untuk periode <span class="font-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</span>.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="space-y-2">
                 <h4 class="font-semibold">Penjualan</h4>
                <a href="{{ route('laporan.penjualan.export',['pdf','periode'=>$periode]) }}" class="w-full text-center bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-4 py-2 rounded-lg block transition">PDF</a>
                <a href="{{ route('laporan.penjualan.export',['excel','periode'=>$periode]) }}" class="w-full text-center bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-4 py-2 rounded-lg block transition">Excel</a>
            </div>
             <div class="space-y-2">
                 <h4 class="font-semibold">Stok</h4>
                <a href="{{ route('laporan.stok.export',['pdf','periode'=>$periode]) }}" class="w-full text-center bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-4 py-2 rounded-lg block transition">PDF</a>
                <a href="{{ route('laporan.stok.export',['excel','periode'=>$periode]) }}" class="w-full text-center bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-4 py-2 rounded-lg block transition">Excel</a>
            </div>
             <div class="space-y-2">
                 <h4 class="font-semibold">Pelanggan</h4>
                <a href="{{ route('laporan.pelanggan.export',['pdf','periode'=>$periode]) }}" class="w-full text-center bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-4 py-2 rounded-lg block transition">PDF</a>
                <a href="{{ route('laporan.pelanggan.export',['excel','periode'=>$periode]) }}" class="w-full text-center bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-4 py-2 rounded-lg block transition">Excel</a>
            </div>
             <div class="space-y-2">
                 <h4 class="font-semibold">Laba</h4>
                <a href="{{ route('laporan.laba.export',['pdf','periode'=>$periode]) }}" class="w-full text-center bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-4 py-2 rounded-lg block transition">PDF</a>
                <a href="{{ route('laporan.laba.export',['excel','periode'=>$periode]) }}" class="w-full text-center bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-4 py-2 rounded-lg block transition">Excel</a>
            </div>
        </div>
    </div>
</div>
@endsection