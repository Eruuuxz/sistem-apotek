@extends('layouts.admin')

@section('title', 'Detail Retur ' . $retur->no_retur)

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-4xl mx-auto mt-6">
    {{-- Header --}}
    <div class="flex justify-between items-start mb-6 pb-6 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Retur</h1>
            <p class="text-sm text-gray-500">No. <span class="font-semibold">{{ $retur->no_retur }}</span></p>
        </div>
        <div>
             @if($retur->jenis == 'pembelian')
                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full font-semibold">Retur Pembelian</span>
            @else
                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full font-semibold">Retur Penjualan</span>
            @endif
        </div>
    </div>
    
    {{-- Informasi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
        <div>
            <h3 class="font-semibold text-gray-600 mb-2">Tanggal Retur</h3>
            <p>{{ \Carbon\Carbon::parse($retur->tanggal)->format('d F Y, H:i') }}</p>
        </div>
        <div>
            <h3 class="font-semibold text-gray-600 mb-2">Keterangan</h3>
            <p>{{ $retur->keterangan ?? '-' }}</p>
        </div>
    </div>

    {{-- Tabel Detail Item --}}
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4">Item yang Diretur</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama Obat</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Qty</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Harga Satuan</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($retur->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $detail->obat->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $detail->qty }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada detail item.</td>
                        </tr>
                    @endforelse
                </tbody>
                 <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">Total Retur</td>
                        <td class="px-4 py-3 text-right font-bold text-xl text-blue-600">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-8 text-right">
        <a href="{{ route('retur.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Kembali</a>
    </div>
</div>
@endsection