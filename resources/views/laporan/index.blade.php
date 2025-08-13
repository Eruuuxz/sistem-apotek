@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan</h1>

<div class="grid grid-cols-3 gap-4">
    <a href="/laporan/penjualan" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Penjualan
    </a>
    <a href="/laporan/pembelian" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Pembelian
    </a>
    <a href="/laporan/stok" class="bg-white p-4 shadow rounded hover:bg-gray-100">
        Laporan Stok
    </a>
</div>
@endsection
