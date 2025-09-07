@extends('layouts.kasir')

@section('title', 'Transaksi Berhasil')

@section('content')
    <div class="bg-white shadow rounded p-6 text-center">
        <h1 class="text-2xl font-bold text-green-600 mb-4">✅ Transaksi Berhasil!</h1>
        <p class="mb-6">No Nota: <strong>{{ $penjualan->no_nota }}</strong></p>
        <p class="mb-6">Total: <strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></p>

        <div class="flex justify-center gap-4">
            <a href="{{ route('pos.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">
                Kembali ke POS
            </a>
        </div>
    </div>
@endsection