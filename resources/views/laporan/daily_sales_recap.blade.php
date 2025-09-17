@extends('layouts.admin')

@section('title', 'Rekap Penjualan Harian')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Rekap Penjualan Harian</h1>
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="{{ route('laporan.daily_sales_recap') }}" method="GET" class="flex items-center space-x-4">
            <label for="periode" class="font-medium">Pilih Bulan:</label>
            <input type="month" id="periode" name="periode" value="{{ $periode }}" class="rounded-md border-gray-300">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tampilkan</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Rekap Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Transaksi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penjualanHarian as $rekap)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($rekap->tanggal_jual)->translatedFormat('d F Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $rekap->jumlah_transaksi }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">Rp {{ number_format($rekap->total_penjualan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data penjualan di bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection