@extends('layouts.admin')

@section('title', 'Edit Data Obat')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <form action="{{ route('obat.update', $obat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Kode Obat</label>
                <input type="text" name="kode" placeholder="Masukkan kode obat" value="{{ old('kode', $obat->kode) }}"
                    required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('kode') border-red-500 @enderror">
                @error('kode')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Nama Obat</label>
                <input type="text" name="nama" placeholder="Masukkan nama obat" value="{{ old('nama', $obat->nama) }}"
                    required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Kategori</label>
                <select name="kategori" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('kategori') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Obat Bebas" {{ old('kategori', $obat->kategori) == 'Obat Bebas' ? 'selected' : '' }}>Obat
                        Bebas</option>
                    <option value="Obat Bebas Terbatas" {{ old('kategori', $obat->kategori) == 'Obat Bebas Terbatas' ? 'selected' : '' }}>Obat Bebas Terbatas</option>
                    <option value="Obat Keras" {{ old('kategori', $obat->kategori) == 'Obat Keras' ? 'selected' : '' }}>Obat
                        Keras</option>
                    <option value="Psikotropika" {{ old('kategori', $obat->kategori) == 'Psikotropika' ? 'selected' : '' }}>
                        Obat Psikotropika (OTK)</option>
                </select>
                @error('kategori')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Sediaan</label>
                <select name="sediaan"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('sediaan') border-red-500 @enderror">
                    <option value="">-- Pilih Sediaan --</option>
                    <option value="Tablet" {{ old('sediaan', $obat->sediaan) == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                    <option value="Kapsul" {{ old('sediaan', $obat->sediaan) == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                    <option value="Sirup" {{ old('sediaan', $obat->sediaan) == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                    <option value="Salep" {{ old('sediaan', $obat->sediaan) == 'Salep' ? 'selected' : '' }}>Salep</option>
                    <option value="Injeksi" {{ old('sediaan', $obat->sediaan) == 'Injeksi' ? 'selected' : '' }}>Injeksi</option>
                    <option value="Tetes Mata" {{ old('sediaan', $obat->sediaan) == 'Tetes Mata' ? 'selected' : '' }}>Tetes Mata</option>
                    <option value="Tetes Telinga" {{ old('sediaan', $obat->sediaan) == 'Tetes Telinga' ? 'selected' : '' }}>Tetes Telinga</option>
                    <option value="Suppositoria" {{ old('sediaan', $obat->sediaan) == 'Suppositoria' ? 'selected' : '' }}>Suppositoria</option>
                    <option value="Suspensi" {{ old('sediaan', $obat->sediaan) == 'Suspensi' ? 'selected' : '' }}>Suspensi</option>
                    <option value="Cream" {{ old('sediaan', $obat->sediaan) == 'Cream' ? 'selected' : '' }}>Cream</option>
                    <option value="Gel" {{ old('sediaan', $obat->sediaan) == 'Gel' ? 'selected' : '' }}>Gel</option>
                    <option value="Serbuk" {{ old('sediaan', $obat->sediaan) == 'Serbuk' ? 'selected' : '' }}>Serbuk</option>
                    <option value="Lain-lain" {{ old('sediaan', $obat->sediaan) == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                </select>
                @error('sediaan')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Kemasan Besar (Opsional)</label>
                <select name="kemasan_besar" id="kemasan_besar"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('kemasan_besar') border-red-500 @enderror">
                    <option value="">-- Pilih Kemasan Besar --</option>
                    <option value="Strip" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Strip' ? 'selected' : '' }}>Strip</option>
                    <option value="Botol" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Botol' ? 'selected' : '' }}>Botol</option>
                    <option value="Tube" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Tube' ? 'selected' : '' }}>Tube</option>
                    <option value="Box" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Box' ? 'selected' : '' }}>Box</option>
                    <option value="Dus" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Dus' ? 'selected' : '' }}>Dus</option>
                    <option value="Pcs" {{ old('kemasan_besar', $obat->kemasan_besar) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                </select>
                @error('kemasan_besar')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Satuan Terkecil</label>
                <input type="text" name="satuan_terkecil" placeholder="Contoh: Tablet, Kapsul, ml" value="{{ old('satuan_terkecil', $obat->satuan_terkecil) }}" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('satuan_terkecil') border-red-500 @enderror">
                @error('satuan_terkecil')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5" id="rasio_konversi_group" style="display: none;">
                <label class="block mb-2 text-gray-700 font-semibold">Rasio Konversi (1 <span id="label_kemasan_besar">Kemasan</span> = <span id="label_rasio_konversi">...</span> <span id="label_satuan_terkecil">Satuan</span>)</label>
                <input type="number" name="rasio_konversi" id="rasio_konversi" placeholder="Contoh: 10 (untuk 1 Strip = 10 Tablet)" value="{{ old('rasio_konversi', $obat->rasio_konversi) }}" min="1"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('rasio_konversi') border-red-500 @enderror">
                @error('rasio_konversi')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Stok (dalam satuan terkecil)</label>
                <input type="number" name="stok" placeholder="Jumlah stok tersedia" value="{{ old('stok', $obat->stok) }}"
                    required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('stok') border-red-500 @enderror">
                @error('stok')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold">Tanggal Kadaluarsa</label>
                <input type="date" name="expired_date" class="border px-3 py-2 w-full @error('expired_date') border-red-500 @enderror" value="{{ old('expired_date', $obat->expired_date ?? '') }}">
                @error('expired_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div class="mb-5">
                    <label class="block mb-2 text-gray-700 font-semibold">Harga dasar</label>
                    <input type="number" id="harga_dasar" name="harga_dasar"
                        step="0.01"
                        value="{{ old('harga_dasar', $obat->harga_dasar ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-gray-700 font-semibold">Persen Untung (%)</label>
                    <input type="number" id="persen_untung" name="persen_untung"
                        step="0.01"
                        value="{{ old('persen_untung', $obat->persen_untung ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-gray-700 font-semibold">Harga Jual</label>
                    <input type="number" id="harga_jual" name="harga_jual"
                        step="0.01"
                        value="{{ old('harga_jual', $obat->harga_jual ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Supplier</label>
                <select name="supplier_id"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('supplier_id') border-red-500 @enderror">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $obat->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col md:flex-row gap-4 mt-6">
                <button type="submit"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl shadow hover:from-blue-600 hover:to-blue-700 transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </button>
                <a href="{{ route('obat.index') }}"
                    class="bg-gray-400 text-white px-6 py-3 rounded-xl shadow hover:bg-gray-500 transition flex items-center justify-center gap-2">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // NEW: Elemen untuk kemasan dan rasio konversi
        const kemasanBesarSelect = document.getElementById('kemasan_besar');
        const satuanTerkecilInput = document.querySelector('input[name="satuan_terkecil"]');
        const rasioKonversiGroup = document.getElementById('rasio_konversi_group');
        const labelKemasanBesar = document.getElementById('label_kemasan_besar');
        const labelSatuanTerkecil = document.getElementById('label_satuan_terkecil');

        function updateRasioKonversiLabels() {
            const kemasan = kemasanBesarSelect.value;
            const satuan = satuanTerkecilInput.value;

            if (kemasan && satuan) {
                labelKemasanBesar.textContent = kemasan;
                labelSatuanTerkecil.textContent = satuan;
                rasioKonversiGroup.style.display = 'block';
            } else {
                rasioKonversiGroup.style.display = 'none';
            }
        }

        kemasanBesarSelect.addEventListener('change', updateRasioKonversiLabels);
        satuanTerkecilInput.addEventListener('input', updateRasioKonversiLabels);

        // Panggil saat halaman dimuat untuk inisialisasi
        updateRasioKonversiLabels();

        const hargaDasar = document.getElementById('harga_dasar');
        const persenUntung = document.getElementById('persen_untung');
        const hargaJual = document.getElementById('harga_jual');

        // Hitung Harga Jual kalau Harga Dasar & Persen Untung diisi
        function updateHargaJual() {
            let dasar = parseFloat(hargaDasar.value) || 0;
            let persen = parseFloat(persenUntung.value) || 0;
            if (dasar > 0) {
                hargaJual.value = Math.round(dasar + (dasar * persen / 100));
            } else {
                hargaJual.value = '';
            }
        }

        // Hitung Persen Untung kalau Harga Jual diubah manual
        function updatePersenUntung() {
            let dasar = parseFloat(hargaDasar.value) || 0;
            let jual = parseFloat(hargaJual.value) || 0;
            if (dasar > 0 && jual > 0) {
                persenUntung.value = ((jual - dasar) / dasar * 100).toFixed(2);
            }
        }

        hargaDasar.addEventListener('input', updateHargaJual);
        persenUntung.addEventListener('input', updateHargaJual);
        hargaJual.addEventListener('input', updatePersenUntung);

        // supaya langsung terisi saat halaman load (edit mode)
        if (hargaDasar.value) {
            updateHargaJual();
        }
    });
</script>
@endpush