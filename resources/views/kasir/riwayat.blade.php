@extends('layouts.kasir')

@section('title', 'Riwayat Penjualan Harian')

@section('content')
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Penjualan Harian</h2>
                <p class="text-sm text-gray-500">Lihat riwayat transaksi penjualan berdasarkan tanggal.</p>
            </div>
        </div>

        {{-- Form Filter Tanggal --}}
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <form action="{{ route('kasir.riwayat') }}" method="GET" class="flex items-center space-x-4">
                <label for="date" class="font-medium text-sm text-gray-700">Pilih Tanggal:</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">Tampilkan</button>
                <a href="{{ route('kasir.riwayat') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-semibold">Hari Ini</a>
            </form>
        </div>
        
        {{-- Ringkasan Penjualan Harian --}}
        <div class="border-t border-b py-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800">
                Laporan untuk tanggal: <span class="text-blue-600">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
            </h3>
            <div class="mt-2 text-xl font-bold text-green-600">
                Total Penjualan: Rp {{ number_format($totalHarian, 0, ',', '.') }}
            </div>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No. Nota</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-right">Total Transaksi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y">
                    @forelse($data as $penjualan)
                        <tr class="hover:bg-blue-50/50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $penjualan->no_nota }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('H:i') }}</td>
                            <td class="px-4 py-3">{{ $penjualan->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('penjualan.show', $penjualan->id) }}" class="text-blue-600 hover:underline font-semibold">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                Tidak ada data penjualan pada tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
@endsection