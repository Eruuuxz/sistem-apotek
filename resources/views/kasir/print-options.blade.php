@extends('layouts.kasir')

@section('title', 'Cetak Bukti Transaksi')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-8 text-center max-w-lg mx-auto">
    <div class="mb-4">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>
    <h2 class="text-2xl font-bold mb-2 text-gray-800">Transaksi Berhasil!</h2>
    <p class="text-gray-500 mb-8">Pilih jenis bukti transaksi yang ingin Anda cetak.</p>
    
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="{{ route('pos.print.faktur', $penjualan->id) }}" target="_blank"
           class="w-full px-6 py-3 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg shadow-sm font-semibold transition">
           Cetak Faktur (A4)
        </a>
        <a href="{{ route('pos.print.invoice', $penjualan->id) }}" target="_blank"
           class="w-full px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg shadow-sm font-semibold transition">
           Cetak Struk (58mm)
        </a>
    </div>
    <div class="mt-8">
        <a href="{{ route('pos.index') }}" 
           class="w-full sm:w-auto inline-block px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition">
           Kembali ke POS
        </a>
    </div>
</div>
@endsection
