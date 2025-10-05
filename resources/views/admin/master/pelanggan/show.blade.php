@extends('layouts.admin')

@section('title', 'Detail Pelanggan: ' . $pelanggan->nama)

@section('content')
<div class="space-y-8" x-data="pelangganDetail()">
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg h-16 w-16 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->nama }}</h2>
                    {{-- PERBAIKAN: Menampilkan tipe pelanggan --}}
                    <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $pelanggan->tipe == 'tetap' ? 'bg-sky-100 text-sky-800' : 'bg-gray-200 text-gray-800' }}">
                        {{ $pelanggan->tipe == 'tetap' ? 'Pelanggan Tetap' : 'Umum' }}
                    </span>
                </div>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                        <span>{{ $pelanggan->telepon ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        <span>{{ $pelanggan->alamat ?? 'Alamat tidak diisi' }}</span>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-auto mt-4 md:mt-0 flex flex-col md:flex-row items-center gap-4 border-t md:border-none pt-4 md:pt-0">
                 <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="bg-yellow-100 text-yellow-800 font-bold py-2 px-4 rounded-lg inline-flex items-center w-full justify-center hover:bg-yellow-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                    Edit
                </a>
                <button @click="fetchHistory({{ $pelanggan->id }})" class="bg-blue-100 text-blue-800 font-bold py-2 px-4 rounded-lg inline-flex items-center w-full justify-center hover:bg-blue-200">
                     <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    Riwayat
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Riwayat (tidak ada perubahan) --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>
        {{-- ... isi modal ... --}}
    </div>

    <div class="flex justify-end mt-8">
        <a href="{{ route('pelanggan.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Kembali</a>
    </div>
</div>
@endsection

@push('scripts')
{{-- Skrip Alpine.js (tidak ada perubahan) --}}
<script>
    function pelangganDetail() {
        return {
            openModal: false,
            isLoading: false,
            history: [],
            async fetchHistory(pelangganId) {
                // ... isi skrip ...
            },
            formatTanggal(tanggal) {
                // ... isi skrip ...
            },
            formatRupiah(angka) {
                // ... isi skrip ...
            }
        }
    }
</script>
@endpush