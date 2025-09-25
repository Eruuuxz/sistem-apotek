@extends('layouts.admin')

@section('title', 'Analisis Pergerakan Stok')

@section('content')
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Analisis Pergerakan Stok</h2>
                <p class="text-sm text-gray-500">Performa penjualan obat dalam {{ $period }} bulan terakhir.</p>
            </div>
            
            <form action="{{ route('stock_movement.detail') }}" method="GET" class="flex items-center gap-3">
                <select name="period" onchange="this.form.submit()" class="border px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="3" {{ $period == 3 ? 'selected' : '' }}>3 Bulan Terakhir</option>
                    <option value="6" {{ $period == 6 ? 'selected' : '' }}>6 Bulan Terakhir</option>
                    <option value="12" {{ $period == 12 ? 'selected' : '' }}>12 Bulan Terakhir</option>
                </select>
                <input type="text" name="search" placeholder="Cari obat..." value="{{ $search }}"
                    class="border px-3 py-2 w-full md:w-64 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama Obat</th>
                        <th class="px-4 py-3 text-center">Status Performa</th>
                        <th class="px-4 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3 text-right">Total Terjual</th>
                        <th class="px-4 py-3 text-right">Rata-rata/Bulan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($data as $item)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                            <td class="px-4 py-3">{{ $item['kode'] }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $item['nama'] }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($item['status'] === 'Fast-Moving')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @elseif ($item['status'] === 'Slow-Moving')
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold">{{ $item['stok'] }}</td>
                            <td class="px-4 py-3 text-right font-bold text-blue-600">{{ $item['total_terjual'] }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($item['avg_sales'], 2) }}</td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                <div class="flex flex-col items-center">
                                     <svg class="w-12 h-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                                    <h4 class="mt-2 text-lg font-semibold text-gray-700">Data Tidak Ditemukan</h4>
                                    <p class="mt-1 text-sm">Tidak ada data yang cocok dengan kriteria pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>
@endsection
