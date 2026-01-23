@extends('layouts.admin')

@section('title', 'Finalisasi & Upload Bukti Faktur')

@section('content')
<div class="max-w-5xl mx-auto my-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Finalisasi Pembelian</h2>
            <p class="text-sm text-gray-500">No. Transaksi: {{ $pembelian->no_faktur }}</p>
        </div>
        <a href="{{ route('pembelian.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Periksa Kembali Inputan:</p>
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: INFO FAKTUR & UPLOAD BUKTI --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Card Upload Bukti (Fitur yang Anda Minta) --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Bukti Fisik Faktur
                    </h3>
                    
                    {{-- Area Preview Gambar --}}
                    <div class="mb-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 text-center relative hover:bg-gray-100 transition h-48 flex flex-col items-center justify-center cursor-pointer" onclick="document.getElementById('file_faktur').click()">
                            
                            {{-- Image Preview Container --}}
                            <img id="preview-image" 
                                src="{{ $pembelian->file_faktur ? Storage::url($pembelian->file_faktur) : '' }}" 
                                class="{{ $pembelian->file_faktur ? '' : 'hidden' }} max-h-full max-w-full object-contain rounded" 
                                alt="Preview Faktur">
                            
                            {{-- Placeholder Text --}}
                            <div id="upload-placeholder" class="{{ $pembelian->file_faktur ? 'hidden' : '' }}">
                                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-1 text-xs text-gray-500">Klik untuk import foto</p>
                            </div>
                        </div>
                        
                        {{-- Input File (Hidden tapi bekerja) --}}
                        <input type="file" name="file_faktur" id="file_faktur" class="hidden" accept="image/*" onchange="previewImage(event)">
                        <p class="text-[10px] text-gray-400 mt-1 text-center">*Format: JPG/PNG. Foto bukti asli dari PBF.</p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">No. Faktur PBF <span class="text-red-500">*</span></label>
                            <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf', $pembelian->no_faktur_pbf) }}" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: INV-2026/001" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tanggal" value="{{ old('tanggal', $pembelian->tanggal->format('Y-m-d\TH:i')) }}" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                             <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Supplier</label>
                             <div class="p-2 bg-gray-100 rounded text-sm text-gray-700 font-medium">{{ $pembelian->supplier->nama }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-3">Ringkasan</h3>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600 text-sm">Total Item</span>
                        <span class="font-bold text-gray-800">{{ $pembelian->detail->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 pt-4">
                        <span class="text-gray-600 text-lg font-bold">Total Pembelian</span>
                    </div>
                    <div class="text-right text-2xl font-bold text-blue-600" id="grand-total-display">
                        Rp {{ number_format($pembelian->total, 0, ',', '.') }}
                    </div>
                    
                    <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transform transition hover:-translate-y-0.5">
                        Simpan & Finalisasi Stok
                    </button>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DETAIL OBAT --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700">Detail Item Obat</h3>
                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Pastikan Batch & Expired Sesuai</span>
                    </div>
                    
                    <div class="p-4 space-y-4 max-h-[80vh] overflow-y-auto">
                        @foreach($pembelian->detail as $index => $detail)
                            <div class="flex flex-col sm:flex-row gap-4 p-4 border border-gray-200 rounded-lg hover:bg-blue-50/30 transition">
                                {{-- Icon & Nama Obat --}}
                                <div class="flex items-start gap-3 sm:w-1/4">
                                    <div class="bg-blue-100 text-blue-600 rounded p-2 mt-1">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $detail->obat->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $detail->obat->kode }}</p>
                                        <p class="text-xs text-blue-600 mt-1 font-medium">{{ $detail->obat->satuan_terkecil }}</p>
                                    </div>
                                </div>

                                {{-- Inputan Detail --}}
                                <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <input type="hidden" name="items[{{ $index }}][pembelian_detail_id]" value="{{ $detail->id }}">
                                    
                                    <div>
                                        <label class="block text-[10px] uppercase text-gray-500 font-bold mb-1">Qty Terima</label>
                                        <input type="number" name="items[{{ $index }}][jumlah]" value="{{ old('items.'.$index.'.jumlah', $detail->jumlah) }}" class="w-full text-sm p-2 border border-gray-300 rounded focus:border-blue-500 font-bold text-center" required min="1">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] uppercase text-gray-500 font-bold mb-1">Harga Beli (@)</label>
                                        <input type="number" name="items[{{ $index }}][harga_beli]" value="{{ old('items.'.$index.'.harga_beli', $detail->harga_beli) }}" class="w-full text-sm p-2 border border-gray-300 rounded focus:border-blue-500 text-right" required>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] uppercase text-gray-500 font-bold mb-1">No. Batch</label>
                                        <input type="text" name="items[{{ $index }}][no_batch]" value="{{ old('items.'.$index.'.no_batch', $detail->obat->kode . '-' . date('dmy')) }}" class="w-full text-sm p-2 border border-gray-300 rounded focus:border-blue-500 uppercase" required placeholder="BATCH-001">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] uppercase text-gray-500 font-bold mb-1">Kadaluarsa</label>
                                        <input type="date" name="items[{{ $index }}][expired_date]" value="{{ old('items.'.$index.'.expired_date', \Carbon\Carbon::now()->addYears(2)->format('Y-m-d')) }}" class="w-full text-sm p-2 border border-gray-300 rounded focus:border-blue-500" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById('preview-image');
        const placeholder = document.getElementById('upload-placeholder');
        
        reader.onload = function(){
            if(reader.readyState == 2){
                imageField.src = reader.result;
                imageField.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        }
        
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection