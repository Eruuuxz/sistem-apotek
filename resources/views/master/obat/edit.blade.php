@extends('layouts.admin')

@section('title', 'Edit Data Obat')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-3xl mx-auto mt-6">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Informasi Obat</h2>
            <p class="text-sm text-gray-500">Perbarui detail obat di bawah ini sesuai kebutuhan.</p>
        </div>

        <form action="{{ route('obat.update', $obat->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Informasi Dasar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Kode Obat</label>
                    <input type="text" name="kode" value="{{ old('kode', $obat->kode) }}" required
                        class="w-full border rounded-lg px-4 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('kode') border-red-500 @enderror">
                    @error('kode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Obat</label>
                    <input type="text" name="nama" value="{{ old('nama', $obat->nama) }}" required
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            {{-- Kategori & Sediaan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Kategori</label>
                    <select name="kategori" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('kategori') border-red-500 @enderror">
                        <option value="Obat Bebas" {{ old('kategori', $obat->kategori) == 'Obat Bebas' ? 'selected' : '' }}>Obat Bebas</option>
                        <option value="Obat Bebas Terbatas" {{ old('kategori', $obat->kategori) == 'Obat Bebas Terbatas' ? 'selected' : '' }}>Obat Bebas Terbatas</option>
                        <option value="Obat Keras" {{ old('kategori', $obat->kategori) == 'Obat Keras' ? 'selected' : '' }}>Obat Keras</option>
                        <option value="Psikotropika" {{ old('kategori', $obat->kategori) == 'Psikotropika' ? 'selected' : '' }}>Obat Psikotropika (OTK)</option>
                    </select>
                </div>
                 <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Sediaan</label>
                    <select name="sediaan" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="Tablet" {{ old('sediaan', $obat->sediaan) == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="Kapsul" {{ old('sediaan', $obat->sediaan) == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                        <option value="Sirup" {{ old('sediaan', $obat->sediaan) == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                        <option value="Salep" {{ old('sediaan', $obat->sediaan) == 'Salep' ? 'selected' : '' }}>Salep</option>
                        <option value="Injeksi" {{ old('sediaan', $obat->sediaan) == 'Injeksi' ? 'selected' : '' }}>Injeksi</option>
                    </select>
                </div>
            </div>

            {{-- Kemasan & Satuan --}}
             <div class="border-t pt-6 space-y-6">
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Kemasan Besar</label>
                        <select name="kemasan_besar" id="kemasan_besar" class="w-full border rounded-lg px-4 py-2">
                             <option value="">-- Opsional --</option>
                            <option value="Strip" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Strip' ? 'selected' : '' }}>Strip</option>
                            <option value="Botol" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Botol' ? 'selected' : '' }}>Botol</option>
                            <option value="Box" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Box' ? 'selected' : '' }}>Box</option>
                        </select>
                    </div>
                     <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Satuan Terkecil</label>
                        <input type="text" name="satuan_terkecil" value="{{ old('satuan_terkecil', $obat->satuan_terkecil) }}" required class="w-full border rounded-lg px-4 py-2">
                    </div>
                     <div id="rasio_konversi_group" style="display: none;">
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Rasio Konversi</label>
                        <input type="number" name="rasio_konversi" id="rasio_konversi" value="{{ old('rasio_konversi', $obat->rasio_konversi) }}" min="1" class="w-full border rounded-lg px-4 py-2">
                    </div>
                </div>
            </div>

            {{-- Stok & Harga --}}
             <div class="border-t pt-6 space-y-6">
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Total Stok Saat Ini</label>
                        <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}" required class="w-full border rounded-lg px-4 py-2">
                     </div>
                     <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Tanggal Kadaluarsa Utama</label>
                        <input type="date" name="expired_date" value="{{ old('expired_date', $obat->expired_date ? $obat->expired_date->format('Y-m-d') : '') }}" class="w-full border rounded-lg px-4 py-2">
                    </div>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Harga Dasar (Rp)</label>
                        <input type="number" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar', $obat->harga_dasar) }}" required class="w-full border rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Margin Untung (%)</label>
                        <input type="number" id="persen_untung" name="persen_untung" value="{{ old('persen_untung', $obat->persen_untung) }}" class="w-full border rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Harga Jual (Rp)</label>
                        <input type="number" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $obat->harga_jual) }}" required class="w-full border rounded-lg px-4 py-2 bg-gray-50 font-bold">
                    </div>
                </div>
            </div>
            
            {{-- Supplier --}}
             <div class="border-t pt-6">
                <label class="block mb-2 text-sm font-semibold text-gray-700">Supplier</label>
                <select name="supplier_id" required class="w-full border rounded-lg px-4 py-2">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $obat->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="flex items-center justify-end gap-4 mt-8">
                <a href="{{ route('obat.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">
                    Update Obat
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const hargaDasar = document.getElementById("harga_dasar");
        const persenUntung = document.getElementById("persen_untung");
        const hargaJual = document.getElementById("harga_jual");
        const kemasanBesarSelect = document.getElementById('kemasan_besar');
        const rasioKonversiGroup = document.getElementById('rasio_konversi_group');

        function toggleRasioKonversi() {
            rasioKonversiGroup.style.display = kemasanBesarSelect.value ? 'block' : 'none';
        }

        kemasanBesarSelect.addEventListener('change', toggleRasioKonversi);
        toggleRasioKonversi();

        function updateHargaJual() {
            let dasar = parseFloat(hargaDasar.value) || 0;
            let persen = parseFloat(persenUntung.value) || 0;
            if (dasar > 0) {
                hargaJual.value = Math.round(dasar + (dasar * persen / 100));
            } else {
                hargaJual.value = '';
            }
        }

        function updatePersenUntung() {
            let dasar = parseFloat(hargaDasar.value) || 0;
            let jual = parseFloat(hargaJual.value) || 0;
            if (dasar > 0 && jual > dasar) {
                persenUntung.value = (((jual - dasar) / dasar) * 100).toFixed(2);
            } else {
                 persenUntung.value = '';
            }
        }

        hargaDasar.addEventListener('input', updateHargaJual);
        persenUntung.addEventListener('input', updateHargaJual);
        hargaJual.addEventListener('input', updatePersenUntung);
        
        if(hargaDasar.value) {
            updateHargaJual();
        }
    });
</script>
@endpush