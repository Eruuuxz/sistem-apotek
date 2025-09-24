@extends('layouts.admin')

@section('title', 'Detail Pelanggan: ' . $pelanggan->nama)

@section('content')
    <div class="space-y-8">
        {{-- Card Informasi Pelanggan --}}
        <div class="bg-white p-6 shadow-lg rounded-xl">
            <div class="flex flex-col md:flex-row items-start gap-6">
                <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg h-16 w-16 flex items-center justify-center">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-4">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->nama }}</h2>
                        <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $pelanggan->status_member == 'member' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', '-', $pelanggan->status_member)) }}
                        </span>
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            <span>{{ $pelanggan->telepon ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                             <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            <span>{{ $pelanggan->alamat ?? 'Alamat tidak diisi' }}</span>
                        </div>
                         <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                            <span>Point: <span class="font-bold text-blue-600">{{ $pelanggan->point ?? 0 }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-auto mt-4 md:mt-0">
                     <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="bg-yellow-100 text-yellow-800 font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300 w-full justify-center hover:bg-yellow-200">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                        Edit Pelanggan
                    </a>
                </div>
            </div>
             @if ($pelanggan->file_ktp)
                <div class="mt-6 border-t pt-4">
                    <h4 class="font-semibold text-gray-700 mb-2">File KTP Terlampir</h4>
                    <img src="{{ asset('storage/'.$pelanggan->file_ktp) }}" alt="Foto KTP" class="max-w-xs rounded-lg border shadow-sm">
                </div>
            @endif
        </div>

        {{-- Riwayat Transaksi --}}
        <div class="bg-white p-6 shadow-lg rounded-xl">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Transaksi Penjualan</h3>
             <div class="overflow-x-auto">
                 <table class="w-full text-sm">
                     <thead class="bg-gray-50 text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">No. Transaksi</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Total Belanja</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($riwayatPenjualan as $penjualan)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $penjualan->no_transaksi }}</td>
                                <td class="px-4 py-3">{{ $penjualan->tanggal->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('penjualan.show', $penjualan->id) }}" class="text-blue-600 hover:underline">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-gray-500">Tidak ada riwayat transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">{{ $riwayatPenjualan->links() }}</div>
        </div>

        <div class="flex justify-end mt-8">
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Kembali ke Daftar Pelanggan</a>
        </div>
    </div>
@endsection