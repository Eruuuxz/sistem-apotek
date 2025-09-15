@extends('layouts.admin')

@section('title', 'Detail Stock Movement')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Detail Stock Movement</h1>

    <form method="GET" action="{{ route('stock.movement.detail') }}" class="mb-6 flex flex-wrap items-center gap-4">
        <label for="period" class="font-medium">Filter Periode:</label>
        <select name="period" id="period" onchange="this.form.submit()" class="border rounded px-3 py-1">
            <option value="3" {{ $period == '3' ? 'selected' : '' }}>3 Bulan</option>
            <option value="6" {{ $period == '6' ? 'selected' : '' }}>6 Bulan</option>
            <option value="12" {{ $period == '12' ? 'selected' : '' }}>1 Tahun</option>
        </select>

        <label for="search" class="font-medium">Cari:</label>
        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama, Kode, Kategori"
            class="border rounded px-3 py-1" />

        <button type="submit"
            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition-colors">Cari</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Kode</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Nama</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Kategori</th>
                    <th class="border border-gray-300 px-4 py-2 text-right">Total Penjualan ({{ $period }} Bulan)</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $item['kode'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 flex items-center gap-2">
                            @php
                                $statusColor = [
                                    'Fast Moving' => 'bg-green-500',
                                    'Slow Moving' => 'bg-yellow-400',
                                    'Dead Stock' => 'bg-red-600',
                                ];
                            @endphp
                            <span class="w-3 h-3 rounded-full {{ $statusColor[$item['status']] ?? 'bg-gray-400' }}"></span>
                            {{ $item['nama'] }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ $item['kategori'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">{{ $item['total_terjual'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-white text-sm font-semibold
                                {{ $statusColor[$item['status']] ?? 'bg-gray-400' }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $data->appends(request()->except('page'))->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('stock.movement', ['period' => $period]) }}"
            class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection