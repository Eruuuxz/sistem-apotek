@extends('layouts.admin')

@section('title', 'Finalisasi Faktur & Batch')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
             <a href="{{ route('pembelian.index') }}" class="p-2 rounded-full hover:bg-gray-200 transition-colors text-gray-500">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Lengkapi Data Faktur</h2>
                <p class="text-sm text-gray-500">Masukkan nomor faktur asli dan detail batch obat.</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 uppercase tracking-wide">
            Draft Mode
        </span>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-feather="alert-circle" class="text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">Terdapat kesalahan pada inputan:</p>
                    <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Section 1: Info Faktur --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Data Administrasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">No. Faktur Internal</label>
                    <input type="text" value="{{ $pembelian->no_faktur }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 font-mono text-sm" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Faktur PBF <span class="text-red-500">*</span></label>
                    <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf', $pembelian->no_faktur_pbf) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium" placeholder="Nomor dari kuitansi..." required autofocus>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', $pembelian->tanggal->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Supplier</label>
                    <input type="text" value="{{ $pembelian->supplier->nama ?? '-' }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700" readonly>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Item --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-lg font-bold text-gray-800">Detail Item & Batch</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Total Item: {{ $pembelian->detail->count() }}</span>
            </div>

            @foreach($pembelian->detail as $index => $detail)
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:border-blue-200 transition-all duration-200 relative group">
                    <div class="absolute top-4 right-4 text-gray-300 font-bold text-4xl opacity-10 select-none group-hover:text-blue-200 transition-colors">
                        #{{ $index + 1 }}
                    </div>

                    <input type="hidden" name="items[{{ $index }}][pembelian_detail_id]" value="{{ $detail->id }}">
                    
                    {{-- Nama Obat --}}
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i data-feather="package" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">{{ $detail->obat->nama }}</h4>
                            <p class="text-xs text-gray-500">Kode: <span class="font-mono">{{ $detail->obat->kode }}</span></p>
                        </div>
                    </div>

                    {{-- Grid Input --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        
                        {{-- Qty --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Qty Diterima</label>
                            <div class="relative">
                                <input type="number" name="items[{{ $index }}][jumlah]" value="{{ old('items.'.$index.'.jumlah', $detail->jumlah) }}" class="w-full pl-3 pr-10 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500 font-bold text-gray-800" required min="1">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-xs">{{ $detail->obat->satuan_terkecil }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Harga Beli --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Harga Satuan (Rp)</label>
                            <input type="number" name="items[{{ $index }}][harga_beli]" value="{{ old('items.'.$index.'.harga_beli', $detail->harga_beli) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-right" required step="0.01" min="0">
                        </div>

                        {{-- Batch --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Nomor Batch <span class="text-red-500">*</span></label>
                            <input type="text" name="items[{{ $index }}][no_batch]" value="{{ old('items.'.$index.'.no_batch', $detail->obat->kode . '-' . date('Ymd')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500 font-mono text-sm" required placeholder="Kode Batch Pabrik">
                        </div>

                        {{-- Expired --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Expired Date <span class="text-red-500">*</span></label>
                            <input type="date" name="items[{{ $index }}][expired_date]" value="{{ old('items.'.$index.'.expired_date', \Carbon\Carbon::now()->addYear()->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Action Bar --}}
        <div class="mt-8 flex items-center justify-end gap-4 sticky bottom-6">
            <div class="bg-white/80 backdrop-blur shadow-lg border border-gray-200 p-2 rounded-xl flex gap-3">
                <a href="{{ route('pembelian.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan & Finalisasi Stok
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });
</script>
@endpush