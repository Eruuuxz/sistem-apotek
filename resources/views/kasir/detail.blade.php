{{-- File: /kasir/detail.blade.php --}}
@extends('layouts.kasir')

@section('title', 'Detail Penjualan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Detail Penjualan {{ $p->no_nota }}</h1>

<div class="bg-white shadow rounded p-6 mb-4">
    <p class="mb-2"><strong>No Nota:</strong> {{ $p->no_nota }}</p>
    <p class="mb-2"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</p>
    {{-- Tampilkan nama kasir dari relasi user --}}
    <p class="mb-2"><strong>Kasir:</strong> {{ $p->kasir->name ?? '-' }}</p> 
</div>

<h2 class="text-xl font-semibold mb-3">Item Penjualan</h2>
<table class="w-full bg-white border shadow rounded">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-3 py-2 text-left">Obat</th>
            <th class="px-3 py-2 text-right">Qty</th>
            <th class="px-3 py-2 text-right">Harga Satuan</th>
            <th class="px-3 py-2 text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($p->detail as $d)
        <tr>
            <td class="border px-3 py-1">{{ $d->obat->kode }} - {{ $d->obat->nama }}</td> 
            <td class="border px-3 py-1 text-right">{{ $d->qty }}</td>
            <td class="border px-3 py-1 text-right">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
            <td class="border px-3 py-1 text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="border px-3 py-1 text-right font-bold">Total</td>
            <td class="border px-3 py-1 text-right font-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<div class="mt-6 flex gap-2">
    <a href="{{ route('penjualan.struk', $p->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Cetak Struk</a>
    <a href="{{ route('penjualan.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded shadow">Kembali ke Riwayat</a>
</div>
@endsection
