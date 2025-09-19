@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="w-full table-auto text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">No Faktur PBF / Internal</th>
                        <th class="border px-4 py-2 text-left">Tanggal</th>
                        <th class="border px-4 py-2 text-left">Supplier</th>
                        <th class="border px-4 py-2 text-right">Total</th>
                        <th class="border px-4 py-2 text-center">Status</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50 {{ $row->status == 'draft' ? 'bg-yellow-50' : '' }}">
                            <td class="border px-4 py-2 font-medium">
                                {{ $row->no_faktur_pbf ?? $row->no_faktur }}
                                @if(!$row->no_faktur_pbf) <span class="text-xs text-gray-500">(Internal)</span> @endif
                            </td>
                            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i') }}</td>
                            <td class="border px-4 py-2">{{ $row->supplier->nama ?? '-' }}</td>
                            <td class="border px-4 py-2 text-right font-semibold text-blue-600">Rp
                                {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2 text-center">
                                @if($row->status == 'draft')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Draft (Input Batch)
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Final
                                    </span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-center">
                                @if($row->status == 'draft')
                                    <a href="{{ route('pembelian.edit', $row->id) }}" class="text-green-600 hover:underline font-bold">Input Faktur & Batch</a>
                                @else
                                    <a href="{{ route('pembelian.faktur', $row->id) }}" class="text-blue-600 hover:underline">Lihat Faktur</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-4 text-center text-gray-500">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2 flex justify-end">
            {{ $data->links() }}
        </div>
    </div>
@endsection