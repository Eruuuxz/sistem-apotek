@extends('layouts.admin')

@section('title', 'Detail Retur ' . $retur->no_retur)

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Retur - {{ $retur->no_retur }}</h1>

    <div class="mb-4">
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($retur->tanggal)->format('Y-m-d H:i:s') }}</p>
        <p><strong>Jenis Retur:</strong> {{ ucfirst($retur->jenis) }}</p>
        <p><strong>Keterangan:</strong> {{ $retur->keterangan ?? '-' }}</p>
    </div>

    <table class="w-full bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Nama Obat</th>
                <th class="px-4 py-2">Qty</th>
                <th class="px-4 py-2">Harga</th>
                <th class="px-4 py-2">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($retur->details as $index => $detail)
                <tr>
                    <td class="border px-4 py-2">{{ $index + 1 }}</td>
                    <td class="border px-4 py-2">{{ $detail->obat->nama ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $detail->qty }}</td>
                    <td class="border px-4 py-2 text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border px-4 py-2 text-center">Tidak ada detail retur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-right font-bold text-lg">
        Total Retur: Rp {{ number_format($retur->total, 0, ',', '.') }}
    </div>

    <a href="{{ route('retur.index') }}" class="mt-4 inline-block bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Kembali</a>
@endsection