@extends('layouts.admin')

@section('title', 'Data Pelanggan')

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
                <h2 class="text-2xl font-bold text-gray-800">Data Pelanggan</h2>
                <p class="text-sm text-gray-500">Lihat semua pelanggan dari riwayat transaksi dan kelola pelanggan tetap.</p>
            </div>
            <a href="{{ route('pelanggan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Pelanggan Tetap
            </a>
        </div>

        <div class="mb-4 flex gap-2 items-center">
            <span class="text-sm font-semibold text-gray-600">Filter:</span>
            <a href="{{ route('pelanggan.index') }}" class="px-3 py-1 rounded-full text-sm font-medium {{ !$filter ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Semua Pelanggan</a>
            <a href="{{ route('pelanggan.index', ['filter' => 'tetap']) }}" class="px-3 py-1 rounded-full text-sm font-medium {{ $filter == 'tetap' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Pelanggan Tetap</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kontak</th>
                        <th class="px-4 py-3 text-center">Tipe</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($pelanggans as $pelanggan)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $pelanggan->nama }}</span>
                                <span class="block text-xs text-gray-500">{{ $pelanggan->no_ktp ?? 'No. KTP tidak terdaftar' }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $pelanggan->telepon ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($pelanggan->tipe == 'tetap')
                                    <span class="px-2 py-1 text-xs bg-sky-100 text-sky-800 rounded-full font-semibold">Pelanggan Tetap</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full font-semibold">Umum</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($pelanggan->id)
                                    <div class="flex justify-center items-center space-x-2">
                                         <a href="{{ route('pelanggan.show', $pelanggan) }}" class="text-gray-500 hover:text-blue-600 p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition" title="Lihat Detail & Riwayat">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-4.43a1.012 1.012 0 0 1 1.431 0l4.43 4.43a1.012 1.012 0 0 1 0 .639l-4.43 4.43a1.012 1.012 0 0 1-1.431 0l-4.43-4.43Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </a>
                                        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="text-gray-500 hover:text-yellow-600 p-2 rounded-full bg-gray-100 hover:bg-yellow-100 transition" title="Edit Pelanggan">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                        </a>
                                        {{-- Form Hapus --}}
                                    </div>
                                @else
                                     <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-10 text-gray-500">Tidak ada data pelanggan yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pelanggans->links() }}</div>
    </div>
@endsection