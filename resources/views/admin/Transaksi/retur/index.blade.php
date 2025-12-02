@extends('layouts.admin')

@section('title', 'Riwayat Retur Barang')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Retur Barang</h2>
                <p class="text-sm text-gray-500">Lacak semua transaksi retur pembelian dan penjualan.</p>
            </div>
            <a href="{{ route('retur.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Retur Baru
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg mb-6">
            <div>
                <label for="jenis_filter" class="block text-sm font-medium text-gray-700 mb-1">Jenis Retur</label>
                <select name="jenis" id="jenis_filter"
                    class="w-full border rounded-md px-2 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="pembelian" {{ request('jenis') == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                    <option value="penjualan" {{ request('jenis') == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="from" id="from_date" value="{{ request('from') }}"
                    class="w-full border rounded-md px-2 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" id="to_date" value="{{ request('to') }}"
                    class="w-full border rounded-md px-2 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Filter</button>
                <a href="{{ route('retur.index') }}"
                    class="w-full bg-gray-200 text-gray-700 text-center px-3 py-2 rounded-lg hover:bg-gray-300 transition font-semibold">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No Retur</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($data as $row)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                            <td class="px-4 py-3 font-medium">{{ $row->no_retur }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 capitalize">{{ $row->jenis }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-blue-600">Rp
                                {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    {{-- Tombol Detail Lama --}}
                                    <a href="{{ route('retur.show', $row->id) }}"
                                        class="text-blue-600 hover:underline font-semibold">Lihat Detail</a>

                                    {{-- TOMBOL PDF BARU --}}
                                    <a href="{{ route('retur.pdf', $row->id) }}" target="_blank"
                                        class="text-red-600 hover:text-red-800" title="Cetak PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 12h-15m0 0l6.75-6.75M4.5 12l6.75 6.75" />
                                    </svg>
                                    <h4 class="mt-2 text-lg font-semibold text-gray-700">Belum Ada Data Retur</h4>
                                    <p class="mt-1 text-sm">Tidak ada transaksi retur yang tercatat.</p>
                                </div>
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