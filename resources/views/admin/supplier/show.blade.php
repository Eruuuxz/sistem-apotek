@extends('layouts.admin')

@section('title', 'Detail Supplier: ' . $supplier->nama)

@section('content')
    <div class="space-y-8">
        {{-- Card Informasi Supplier --}}
        <div class="bg-white p-6 shadow-lg rounded-xl">
            <div class="flex flex-col md:flex-row items-start gap-6">
                 <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg h-16 w-16 flex items-center justify-center">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h.008v.008h-.008v-.008Zm-3 0H12m0 0h-1.5m-12.75 0H3.375c.621 0 1.125-.504 1.125-1.125V14.25m17.25 4.5v-4.5A3.375 3.375 0 0 0 16.5 11.25V6.75a3.375 3.375 0 0 0-3.375-3.375H8.25a3.375 3.375 0 0 0-3.375 3.375v4.5A3.375 3.375 0 0 0 6.25 15.75v2.25m10.5-11.25h.008v.008h-.008V4.5Zm-3 0h.008v.008h-.008V4.5Z" /></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $supplier->nama }}</h2>
                    <p class="text-sm text-gray-500">Kode: {{ $supplier->kode }}</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                             <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            <span>{{ $supplier->alamat ?? '-' }}, {{ $supplier->kota ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            <span>{{ $supplier->telepon ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                 <div class="w-full md:w-auto mt-4 md:mt-0">
                     <a href="{{ route('supplier.edit', $supplier->id) }}" class="bg-yellow-100 text-yellow-800 font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300 w-full justify-center hover:bg-yellow-200">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                        Edit Supplier
                    </a>
                </div>
            </div>
        </div>

        {{-- Tab Riwayat Transaksi --}}
        <div class="bg-white p-6 shadow-lg rounded-xl" x-data="{ activeTab: 'pembelian' }">
             <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <button @click="activeTab = 'pembelian'" :class="{ 'text-blue-600 border-blue-600': activeTab === 'pembelian', 'text-gray-500 hover:text-gray-600 border-transparent hover:border-gray-300': activeTab !== 'pembelian' }" class="inline-block p-4 border-b-2 rounded-t-lg">
                            Riwayat Pembelian
                        </button>
                    </li>
                    <li class="mr-2">
                         <button @click="activeTab = 'sp'" :class="{ 'text-blue-600 border-blue-600': activeTab === 'sp', 'text-gray-500 hover:text-gray-600 border-transparent hover:border-gray-300': activeTab !== 'sp' }" class="inline-block p-4 border-b-2 rounded-t-lg">
                            Riwayat Surat Pesanan
                        </button>
                    </li>
                    <li>
                         <button @click="activeTab = 'retur'" :class="{ 'text-blue-600 border-blue-600': activeTab === 'retur', 'text-gray-500 hover:text-gray-600 border-transparent hover:border-gray-300': activeTab !== 'retur' }" class="inline-block p-4 border-b-2 rounded-t-lg">
                           Riwayat Retur
                        </button>
                    </li>
                </ul>
            </div>
            
            {{-- Konten Tab --}}
            <div>
                {{-- Riwayat Pembelian --}}
                <div x-show="activeTab === 'pembelian'">
                    <table class="w-full text-sm">
                         <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">No Faktur</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-right">Total</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($riwayatPembelian as $pembelian)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $pembelian->no_faktur_pbf ?? $pembelian->no_faktur }}</td>
                                    <td class="px-4 py-3">{{ $pembelian->tanggal->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('pembelian.faktur', $pembelian->id) }}" class="text-blue-600 hover:underline">Lihat Faktur</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-gray-500">Tidak ada riwayat pembelian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                     <div class="mt-4">{{ $riwayatPembelian->links('pagination::tailwind') }}</div>
                </div>

                {{-- Riwayat Surat Pesanan --}}
                <div x-show="activeTab === 'sp'" x-cloak>
                     <table class="w-full text-sm">
                         <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">No SP</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($riwayatSuratPesanan as $sp)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $sp->no_sp }}</td>
                                    <td class="px-4 py-3">{{ $sp->tanggal_sp->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            @if($sp->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($sp->status == 'selesai') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($sp->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('surat_pesanan.pdf', $sp->id) }}" target="_blank" class="text-blue-600 hover:underline">Lihat PDF</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-gray-500">Tidak ada riwayat surat pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $riwayatSuratPesanan->links('pagination::tailwind') }}</div>
                </div>

                 {{-- Riwayat Retur --}}
                <div x-show="activeTab === 'retur'" x-cloak>
                     <table class="w-full text-sm">
                         <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">No Retur</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-right">Total Retur</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($riwayatRetur as $retur)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $retur->no_retur }}</td>
                                    <td class="px-4 py-3">{{ $retur->tanggal->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('retur.show', $retur->id) }}" class="text-blue-600 hover:underline">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-gray-500">Tidak ada riwayat retur.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                     <div class="mt-4">{{ $riwayatRetur->links('pagination::tailwind') }}</div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <a href="{{ route('supplier.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Kembali ke Daftar Supplier</a>
        </div>
    </div>
@endsection