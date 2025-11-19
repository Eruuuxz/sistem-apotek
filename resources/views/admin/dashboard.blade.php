@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8 animate-fade-in-down">

    {{-- SECTION 1: HEADER & SAMBUTAN --}}
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}! </h2>
            <p class="text-gray-500 text-sm mt-1">Berikut adalah ringkasan data stok dan pengadaan apotek hari ini.</p>
        </div>
        <div class="mt-4 md:mt-0 text-right">

        </div>
    </div>

    {{-- SECTION 2: STATISTIK UTAMA (GENERAL STATS) --}}
    {{-- Informasi umum mengenai volume data dan keuangan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Card 1: Total Obat --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <i data-feather="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Obat</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalObat ?? 0 }} <span class="text-xs font-normal text-gray-400">Item</span></h3>
            </div>
        </div>

        {{-- Card 2: Total Supplier --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                <i data-feather="truck" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mitra Supplier</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalSupplier ?? 0 }} <span class="text-xs font-normal text-gray-400">Mitra</span></h3>
            </div>
        </div>

        {{-- Card 3: SP Pending --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4 relative overflow-hidden">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-lg z-10">
                <i data-feather="file-text" class="w-6 h-6"></i>
            </div>
            <div class="z-10">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">SP Belum Proses</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $spPending ?? 0 }} <span class="text-xs font-normal text-gray-400">Pesanan</span></h3>
            </div>
            {{-- Indikator visual jika ada pending --}}
            @if(($spPending ?? 0) > 0)
                <span class="absolute top-2 right-2 w-3 h-3 bg-purple-500 rounded-full animate-ping"></span>
                <span class="absolute top-2 right-2 w-3 h-3 bg-purple-500 rounded-full"></span>
            @endif
        </div>

        {{-- Card 4: Pengeluaran --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                <i data-feather="dollar-sign" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pembelian (Bulan Ini)</p>
                <h3 class="text-xl font-bold text-gray-800">Rp {{ number_format($totalPembelianBulanIni ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    {{-- SECTION 3: PERINGATAN STOK (CRITICAL ALERTS) --}}
    {{-- Dibuat menonjol agar admin segera bertindak --}}
    <div>
        <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="alert-circle" class="w-5 h-5 text-gray-400"></i>
            Status Stok Perlu Perhatian
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Alert: Stok Menipis --}}
            <a href="{{ route('obat.index', ['filter' => 'menipis']) }}" class="group bg-white p-6 rounded-xl shadow-sm border border-l-4 border-yellow-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 group-hover:text-yellow-600 transition-colors">Stok Menipis</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stokMenipis ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">Perlu restock segera</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-500 group-hover:bg-yellow-100 transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    </div>
                </div>
            </a>

            {{-- Alert: Stok Habis --}}
            <a href="{{ route('obat.index', ['filter' => 'habis']) }}" class="group bg-white p-6 rounded-xl shadow-sm border border-l-4 border-red-500 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 group-hover:text-red-600 transition-colors">Stok Habis (0)</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stokHabis ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">Hilang potensi penjualan</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-50 text-red-500 group-hover:bg-red-100 transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                </div>
            </a>

            {{-- Alert: Kadaluarsa --}}
            <a href="{{ route('obat.index', ['filter' => 'kadaluarsa']) }}" class="group bg-white p-6 rounded-xl shadow-sm border border-l-4 border-orange-400 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 group-hover:text-orange-600 transition-colors">Hampir Expired</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">{{ $obatHampirExpired ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">Dalam 30 hari kedepan</p>
                    </div>
                    <div class="p-3 rounded-full bg-orange-50 text-orange-500 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Animasi sederhana saat halaman dimuat (opsional)
    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();
    });
</script>
@endpush