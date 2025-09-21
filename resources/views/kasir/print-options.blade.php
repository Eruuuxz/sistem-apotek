@extends('layouts.kasir')

@section('title', 'Cetak Bukti Transaksi')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-8 text-center">
    <h2 class="text-xl font-bold mb-6">Pilih Jenis Cetak</h2>
    <div class="flex justify-center gap-6">
        <a href="{{ route('pos.print.faktur', $penjualan->id) }}" target="_blank"
           class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
           Cetak Faktur
        </a>
        <a href="{{ route('pos.print.kwitansi', $penjualan->id) }}" target="_blank"
           class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
           Cetak Kwitansi
        </a>
        <a href="{{ route('pos.print.invoice', $penjualan->id) }}" target="_blank"
           class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg shadow">
           Cetak Invoice (58mm)
        </a>
    </div>
    <div class="mt-8">
        <a href="{{ route('kasir.success',  $penjualan->id) }}" 
           class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow">
           Selesai
        </a>
    </div>
</div>
@endsection