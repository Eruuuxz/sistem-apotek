@extends('layouts.admin')

@section('title', 'Laporan Analisis Pelanggan')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Laporan Analisis Pelanggan</h1>
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="{{ route('laporan.customer_analytics') }}" method="GET" class="flex items-center space-x-4">
            <label for="periode" class="font-medium">Pilih Periode:</label>
            <input type="month" id="periode" name="periode" value="{{ $periode }}" class="rounded-md border-gray-300">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tampilkan</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h3 class="text-gray-500 text-sm">Total Pelanggan Terdaftar</h3>
            <span class="text-4xl font-bold text-blue-600">{{ $totalPelanggan }}</span>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h3 class="text-gray-500 text-sm">Pelanggan Baru Bulan Ini</h3>
            <span class="text-4xl font-bold text-green-600">{{ $pelangganBaru }}</span>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h3 class="text-gray-500 text-sm">Rata-rata Nilai Transaksi</h3>
            <span class="text-2xl font-bold text-purple-600">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Top 10 Pelanggan Paling Sering Transaksi</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pelangganTerbaik as $pelanggan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $pelanggan->pelanggan->nama ?? 'Bukan Member' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $pelanggan->total_transaksi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection