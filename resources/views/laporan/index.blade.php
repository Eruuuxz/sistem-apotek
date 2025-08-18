@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan</h1>

<div class="grid grid-cols-3 gap-4">
    <a href="{{ route('laporan.penjualan') }}" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Penjualan
    </a>
    <a href="{{ route('laporan.penjualan', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()]) }}" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Penjualan (Bulan ini)
    </a>
    <a href="{{ route('laporan.stok') }}" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Stok
    </a>
    {{-- Anda bisa menambahkan link laporan lain di sini, contoh: --}}
    {{-- <a href="{{ route('laporan.pembelian') }}" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Pembelian
    </a> --}}
</div>
@endsection