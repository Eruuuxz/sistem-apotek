@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <a href="{{ route('pembelian.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Tambah Pembelian
            </a>
        </div>

        {{-- Tabel Pembelian --}}
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="w-full table-auto text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">No Faktur</th>
                        <th class="border px-4 py-2 text-left">Tanggal</th>
                        <th class="border px-4 py-2 text-left">Supplier</th>
                        <th class="border px-4 py-2 text-right">Total</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-2 font-medium">{{ $row->no_faktur }}</td>
                            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
                            <td class="border px-4 py-2">{{ $row->supplier->nama ?? '-' }}</td>
                            <td class="border px-4 py-2 text-right font-semibold text-blue-600">Rp
                                {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2 text-center">
                                <a href="{{ route('pembelian.faktur', $row->id) }}" class="text-blue-600 hover:underline">Lihat
                                    Faktur</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-4 py-4 text-center text-gray-500">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-2 flex justify-end">
            {{ $data->links() }}
        </div>
    </div>
@endsection