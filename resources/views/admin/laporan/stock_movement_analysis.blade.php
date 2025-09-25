@extends('layouts.admin')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Laporan Pergerakan Stok</h1>
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="{{ route('laporan.stock_movement_analysis') }}" method="GET" class="flex items-center space-x-4">
            <label for="periode" class="font-medium">Pilih Bulan:</label>
            <input type="month" id="periode" name="periode" value="{{ $periode }}" class="rounded-md border-gray-300">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tampilkan</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Pergerakan Stok Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Obat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pergerakan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pergerakanStok as $pergerakan)
                        <tr class="{{ $pergerakan['jenis'] === 'Penjualan' ? 'bg-red-50' : 'bg-green-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pergerakan['tanggal'])->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pergerakan['obat_nama'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pergerakan['jenis'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pergerakan['qty'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pergerakan['no_referensi'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pergerakan stok di bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection