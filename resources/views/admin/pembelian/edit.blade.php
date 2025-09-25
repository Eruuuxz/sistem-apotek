@extends('layouts.admin')

@section('title', 'Input Faktur & Batch Pembelian')

@section('content')
<div class="bg-white p-6 sm:p-8 shadow-lg rounded-xl max-w-4xl mx-auto my-6">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Faktur & Batch</h2>
            <p class="text-sm text-gray-500">No. Internal: <span class="font-medium text-gray-700">{{ $pembelian->no_faktur }}</span></p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
            <svg class="-ml-1 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
            Draft
        </span>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Faktur</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="no_faktur_pbf" class="block text-sm font-medium text-gray-700 mb-1">No. Faktur PBF <span class="text-red-500">*</span></label>
                    <input type="text" id="no_faktur_pbf" name="no_faktur_pbf" value="{{ old('no_faktur_pbf', $pembelian->no_faktur_pbf) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Faktur <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="tanggal" name="tanggal" value="{{ old('tanggal', $pembelian->tanggal->format('Y-m-d\TH:i')) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" value="{{ $pembelian->supplier->nama ?? '' }}" class="w-full p-2 rounded-md border-gray-300 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Surat Pesanan</label>
                    <input type="text" value="{{ $pembelian->suratPesanan->no_sp ?? 'Manual' }}" class="w-full p-2 rounded-md border-gray-300 bg-gray-100" readonly>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Obat & Batch</h3>
            @foreach($pembelian->detail as $index => $detail)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-blue-100 text-blue-600 rounded-full h-10 w-10 flex-shrink-0 flex items-center justify-center">
                             <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.563A.562.562 0 0 1 9 14.437V9.564Z" /></svg>
                        </div>
                         <div>
                            <p class="font-bold text-gray-800">{{ $detail->obat->nama }}</p>
                            <p class="text-sm text-gray-600">Kode: {{ $detail->obat->kode }}</p>
                        </div>
                    </div>
                    
                    <input type="hidden" name="items[{{ $index }}][pembelian_detail_id]" value="{{ $detail->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah Diterima <span class="text-red-500">*</span></label>
                            <input type="number" name="items[{{ $index }}][jumlah]" value="{{ old('items.'.$index.'.jumlah', $detail->jumlah) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required min="1">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Beli Satuan <span class="text-red-500">*</span></label>
                            <input type="number" name="items[{{ $index }}][harga_beli]" value="{{ old('items.'.$index.'.harga_beli', $detail->harga_beli) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required step="0.01" min="0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. Batch <span class="text-red-500">*</span></label>
                            <input type="text" name="items[{{ $index }}][no_batch]" value="{{ old('items.'.$index.'.no_batch', $detail->obat->kode . '-' . date('Ymd')) }}" placeholder="Contoh: {{ $detail->obat->kode . '-' . date('Ymd') }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expired Date <span class="text-red-500">*</span></label>
                            <input type="date" name="items[{{ $index }}][expired_date]" value="{{ old('items.'.$index.'.expired_date', \Carbon\Carbon::now()->addYear()->format('Y-m-d')) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end space-x-4 mt-8 pt-4 border-t">
            <a href="{{ route('pembelian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-3 rounded-lg transition duration-300">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                Simpan & Finalisasi
            </button>
        </div>
    </form>
</div>
@endsection
