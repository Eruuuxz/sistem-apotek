@extends('layouts.admin')

@section('title', 'Detail Pergerakan Stok')

@section('content')
    <div class="bg-white p-6 shadow-lg rounded-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Analisis Pergerakan Stok</h1>
                <p class="text-gray-600">Daftar obat berdasarkan performa penjualan dalam {{ $period }} bulan terakhir.</p>
            </div>
            
            <form action="{{ route('stock_movement.detail') }}" method="GET" class="flex items-center gap-3">
                <select name="period" onchange="this.form.submit()" class="border px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="3" {{ $period == 3 ? 'selected' : '' }}>3 Bulan</option>
                    <option value="6" {{ $period == 6 ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12" {{ $period == 12 ? 'selected' : '' }}>12 Bulan</option>
                </select>
                <input type="text" name="search" placeholder="Cari obat..." value="{{ $search }}"
                    class="border px-3 py-2 w-full md:w-64 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3 text-right">Total Terjual</th>
                        <th class="px-4 py-3 text-right">Rata-rata/Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-3">{{ $item['kode'] }}</td>
                            <td class="border px-4 py-3 font-semibold">{{ $item['nama'] }}</td>
                            <td class="border px-4 py-3">{{ $item['kategori'] }}</td>
                            <td class="border px-4 py-3 text-center">
                                @if ($item['status'] === 'Fast-Moving')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @elseif ($item['status'] === 'Slow-Moving')
                                    <span class="px-2 py-1 text-xs bg-sky-100 text-sky-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-slate-200 text-slate-800 rounded-full font-semibold">{{ $item['status'] }}</span>
                                @endif
                            </td>
                            {{-- PERBAIKAN: Mengganti 'stok_saat_ini' menjadi 'stok' --}}
                            <td class="border px-4 py-3 text-right">{{ $item['stok'] }}</td>
                            <td class="border px-4 py-3 text-right font-bold">{{ $item['total_terjual'] }}</td>
                            <td class="border px-4 py-3 text-right">{{ number_format($item['avg_sales'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-gray-500">
                                Tidak ada data yang ditemukan untuk kriteria yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{-- Menambahkan query string ke link pagination --}}
            {{ $data->withQueryString()->links() }}
        </div>
    </div>
@endsection

