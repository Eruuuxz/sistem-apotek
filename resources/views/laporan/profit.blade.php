@extends('layouts.admin')

@section('title', 'Laporan Profit Bulanan')

@section('content')

{{-- Pilih Bulan --}}
<form method="GET" class="mb-6 flex gap-2 items-center">
    <label class="font-bold">Pilih Bulan:</label>
    <input type="month" name="periode"
        value="{{ $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
        max="{{ now()->format('Y-m') }}"
        class="border px-3 py-2 rounded">
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat</button>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Total Penjualan (Harga Jual)</span>
        <span class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalPenjualan,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Jumlah total penjualan semua transaksi bulan ini</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Total Modal (Harga Dasar)</span>
        <span class="text-2xl font-bold text-orange-500 mt-2">Rp {{ number_format($totalModal,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Total biaya modal semua barang terjual bulan ini</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Total Pengeluaran (Pembelian)</span>
        <span class="text-2xl font-bold text-red-600 mt-2">Rp {{ number_format($totalPengeluaran,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Jumlah total pembelian obat ke supplier bulan ini</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Keuntungan</span>
        <span class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($keuntungan,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Selisih antara penjualan dan modal (Profit)</p>
    </div>

</div>


@endsection
