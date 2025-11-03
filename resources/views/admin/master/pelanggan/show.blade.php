@extends('layouts.admin')

@section('title', 'Detail Pelanggan: ' . $pelanggan->nama)

@section('content')
<div class="space-y-6">
    {{-- KARTU INFORMASI PELANGGAN --}}
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
            {{-- Bagian Kiri: Info Dasar --}}
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg h-14 w-14 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->nama }}</h2>
                        <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $pelanggan->tipe == 'tetap' ? 'bg-sky-100 text-sky-800' : 'bg-gray-200 text-gray-800' }}">
                            {{ $pelanggan->tipe == 'tetap' ? 'Pelanggan Tetap' : 'Umum' }}
                        </span>
                    </div>
                </div>
                 <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <dt class="w-4 h-4 text-gray-400"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg></dt>
                        <dd>{{ $pelanggan->telepon ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center gap-2">
                         <dt class="w-4 h-4 text-gray-400"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg></dt>
                        <dd>{{ $pelanggan->alamat ?? 'Alamat tidak diisi' }}</dd>
                    </div>
                </dl>
            </div>
            {{-- Bagian Kanan: Tombol Aksi --}}
            <div class="w-full md:w-auto flex flex-row items-center gap-3 pt-4 border-t md:border-none md:pt-0">
                 <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="flex-1 bg-yellow-100 text-yellow-800 font-bold py-2 px-4 rounded-lg inline-flex items-center justify-center hover:bg-yellow-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                    Edit
                </a>
                 <a href="{{ route('pelanggan.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 font-semibold inline-flex items-center justify-center">Kembali</a>
            </div>
        </div>
    </div>

    {{-- KARTU RIWAYAT TRANSAKSI --}}
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Riwayat Transaksi (1 Bulan Terakhir)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Kode Transaksi</th>
                        <th class="px-4 py-3 text-left">Detail Pembelian</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($riwayat as $transaksi)
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 align-top">{{ \Carbon\Carbon::parse($transaksi->tanggal)->isoFormat('D MMMM YYYY') }}</td>
                            <td class="px-4 py-3 align-top font-mono">{{ $transaksi->kode_transaksi }}</td>
                            <td class="px-4 py-3">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($transaksi->details as $item)
                                        <li>{{ $item->obat->nama }} ({{ $item->qty }} pcs)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-right align-top font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-500">
                                Tidak ada riwayat transaksi dalam 1 bulan terakhir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection