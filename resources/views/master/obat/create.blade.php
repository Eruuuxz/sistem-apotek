@extends('layouts.admin')

@section('title', 'Tambah Data Obat')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <form action="{{ route('obat.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Kode Obat</label>
                <input type="text" name="kode" placeholder="Masukkan kode obat" value="{{ old('kode') }}" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('kode') border-red-500 @enderror">
                @error('kode')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Nama Obat</label>
                <input type="text" name="nama" placeholder="Masukkan nama obat" value="{{ old('nama') }}" required
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
                    <option value="Obat Bebas" {{ old('kategori') == 'Obat Bebas' ? 'selected' : '' }}>Obat Bebas</option>
                    <option value="Obat Bebas Terbatas" {{ old('kategori') == 'Obat Bebas Terbatas' ? 'selected' : '' }}>Obat
                        Bebas Terbatas</option>
                    <option value="Obat Keras" {{ old('kategori') == 'Obat Keras' ? 'selected' : '' }}>Obat Keras</option>
                    <option value="Psikotropika" {{ old('kategori') == 'Psikotropika' ? 'selected' : '' }}>Obat Psikotropika
                        (OTK)</option>
                </select>
                @error('kategori')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Stok</label>
                <input type="number" name="stok" placeholder="Jumlah stok tersedia" value="{{ old('stok') }}" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('stok') border-red-500 @enderror">
                @error('stok')
                    <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block mb-2 text-gray-700 font-semibold">Harga Dasar (Rp)</label>
                    <input type="number" id="harga_dasar" name="harga_dasar" placeholder="0"
                        value="{{ old('harga_dasar') }}" required
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('harga_dasar') border-red-500 @enderror"
                        oninput="hitungHargaJual()">
                    @error('harga_dasar')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-gray-700 font-semibold">Persentase Untung (%)</label>
                    <input type="number" id="persen_untung" name="persen_untung" placeholder="0"
                        value="{{ old('persen_untung') }}" required
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('persen_untung') border-red-500 @enderror"
                        oninput="hitungHargaJual()">
                    @error('persen_untung')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-gray-700 font-semibold">Harga Jual (Rp)</label>
                    <input type="number" id="harga_jual" name="harga_jual" readonly value="{{ old('harga_jual') }}"
                        class="w-full border rounded-lg px-4 py-2 font-bold bg-gray-100 focus:outline-none">
                    @error('harga_jual')
                        <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-700 font-semibold">Supplier</label>
                <select name="supplier_id"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none focus:shadow-md transition @error('supplier_id') border-red-500 @enderror">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                    Simpan
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
        function hitungHargaJual() {
            let hargaDasar = parseFloat(document.getElementById('harga_dasar').value) || 0;
            let persen = parseFloat(document.getElementById('persen_untung').value) || 0;
            document.getElementById('harga_jual').value = (hargaDasar + (hargaDasar * persen / 100)).toFixed(0);
        }
        document.addEventListener('DOMContentLoaded', hitungHargaJual);
    </script>
@endpush