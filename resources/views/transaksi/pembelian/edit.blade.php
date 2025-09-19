@extends('layouts.admin')

@section('title', 'Input Faktur & Batch Pembelian')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-5xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6">Input Faktur & Batch - No. Internal: {{ $pembelian->no_faktur }}</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Faktur PBF <span class="text-red-500">*</span></label>
                <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf', $pembelian->no_faktur_pbf) }}" class="w-full p-2 rounded border" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Faktur <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="tanggal" value="{{ old('tanggal', $pembelian->tanggal->format('Y-m-d\TH:i')) }}" class="w-full p-2 rounded border" required>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <input type="text" value="{{ $pembelian->supplier->nama ?? '' }}" class="w-full p-2 rounded border bg-gray-100" readonly>
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Surat Pesanan</label>
                <input type="text" value="{{ $pembelian->suratPesanan->no_sp ?? 'Manual' }}" class="w-full p-2 rounded border bg-gray-100" readonly>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="font-bold">Detail Obat</h3>
            @foreach($pembelian->detail as $index => $detail)
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <p class="font-semibold">{{ $detail->obat->nama }} (Kode: {{ $detail->obat->kode }})</p>
                    <input type="hidden" name="items[{{ $index }}][pembelian_detail_id]" value="{{ $detail->id }}">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                        <div>
                            <label class="block text-sm">Jumlah Diterima <span class="text-red-500">*</span></label>
                            <input type="number" name="items[{{ $index }}][jumlah]" value="{{ old('items.'.$index.'.jumlah', $detail->jumlah) }}" class="w-full p-2 border rounded" required min="1">
                        </div>
                        <div>
                            <label class="block text-sm">Harga Beli Satuan <span class="text-red-500">*</span></label>
                            <input type="number" name="items[{{ $index }}][harga_beli]" value="{{ old('items.'.$index.'.harga_beli', $detail->harga_beli) }}" class="w-full p-2 border rounded" required step="0.01" min="0">
                        </div>
                        <div>
                            <label class="block text-sm">No. Batch <span class="text-red-500">*</span></label>
                            <input type="text" name="items[{{ $index }}][no_batch]" value="{{ old('items.'.$index.'.no_batch') }}" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm">Expired Date <span class="text-red-500">*</span></label>
                            <input type="date" name="items[{{ $index }}][expired_date]" value="{{ old('items.'.$index.'.expired_date') }}" class="w-full p-2 border rounded" required>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end space-x-4 mt-8">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Simpan & Finalisasi Pembelian</button>
            <a href="{{ route('pembelian.index') }}" class="bg-gray-400 text-white px-6 py-3 rounded hover:bg-gray-500">Batal</a>
        </div>
    </form>
</div>
@endsection