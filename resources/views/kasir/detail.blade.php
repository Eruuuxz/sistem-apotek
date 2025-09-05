{{-- File: /views/kasir/detail.blade.php --}}
@extends('layouts.kasir')

@section('title', 'Detail Penjualan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Penjualan - {{ $p->no_nota }}</h1>

    <table class="w-full bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($p->details ?? [] as $index => $item)
                <tr>
                    <td class="border px-4 py-2">{{ $index + 1 }}</td>
                    <td class="border px-4 py-2">{{ $item->obat->nama ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $item->qty }}</td>
                    <td class="border px-4 py-2 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border px-4 py-2 text-center">Tidak ada item penjualan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection