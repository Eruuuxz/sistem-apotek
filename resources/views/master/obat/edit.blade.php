@extends('layouts.admin')

@section('title', 'Edit Data Obat')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Data Obat</h1>

<div class="bg-white p-6 shadow rounded w-full md:w-2/3">
    <form action="{{ route('obat.update', $obat->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Kode Obat</label>
            <input type="text" name="kode" class="border px-3 py-2 w-full @error('kode') border-red-500 @enderror" placeholder="Masukkan kode obat" value="{{ old('kode', $obat->kode) }}" required>
            @error('kode')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Nama Obat</label>
            <input type="text" name="nama" class="border px-3 py-2 w-full @error('nama') border-red-500 @enderror" placeholder="Masukkan nama obat" value="{{ old('nama', $obat->nama) }}" required>
            @error('nama')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Kategori</label>
            <select name="kategori" class="border px-3 py-2 w-full @error('kategori') border-red-500 @enderror" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Obat Bebas" {{ old('kategori', $obat->kategori) == 'Obat Bebas' ? 'selected' : '' }}>Obat Bebas</option>
                <option value="Obat Bebas Terbatas" {{ old('kategori', $obat->kategori) == 'Obat Bebas Terbatas' ? 'selected' : '' }}>Obat Bebas Terbatas</option>
                <option value="Obat Keras" {{ old('kategori', $obat->kategori) == 'Obat Keras' ? 'selected' : '' }}>Obat Keras</option>
                <option value="Psikotropika" {{ old('kategori', $obat->kategori) == 'Psikotropika' ? 'selected' : '' }}>Obat Psikotropika (OTK)</option>
            </select>
            @error('kategori')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Stok</label>
            <input type="number" name="stok" class="border px-3 py-2 w-full @error('stok') border-red-500 @enderror" placeholder="Jumlah stok tersedia" value="{{ old('stok', $obat->stok) }}" required>
            @error('stok')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tanggal Kadaluarsa</label>
            <input type="date" name="expired_date" class="border px-3 py-2 w-full @error('expired_date') border-red-500 @enderror" value="{{ old('expired_date', $obat->expired_date ?? '') }}">
            @error('expired_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block mb-1 font-semibold">Harga Dasar (Rp)</label>
                <input type="number" id="harga_dasar" name="harga_dasar" class="border px-3 py-2 w-full @error('harga_dasar') border-red-500 @enderror" placeholder="0" oninput="hitungHargaJual()" value="{{ old('harga_dasar', $obat->harga_dasar) }}" required>
                @error('harga_dasar')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block mb-1 font-semibold">Persentase Untung (%)</label>
                <input type="number" id="persen_untung" name="persen_untung" class="border px-3 py-2 w-full @error('persen_untung') border-red-500 @enderror" placeholder="0" oninput="hitungHargaJual()" value="{{ old('persen_untung', $obat->persen_untung) }}" required>
                @error('persen_untung')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block mb-1 font-semibold">Harga Jual (Rp)</label>
                <input type="number" id="harga_jual" name="harga_jual" class="border px-3 py-2 w-full font-bold bg-gray-100 @error('harga_jual') border-red-500 @enderror" value="{{ old('harga_jual', $obat->harga_jual) }}" readonly required>
                @error('harga_jual')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Supplier</label>
            <select name="supplier_id" class="w-full border px-3 py-2 @error('supplier_id') border-red-500 @enderror">
                <option value="">-- Pilih Supplier --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $obat->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                @endforeach
            </select>
            @error('supplier_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            <a href="{{ route('obat.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function hitungHargaJual() {
        let hargaDasar = parseFloat(document.getElementById('harga_dasar').value) || 0;
        let persen = parseFloat(document.getElementById('persen_untung').value) || 0;

        let hargaJual = hargaDasar + (hargaDasar * persen / 100);
        document.getElementById('harga_jual').value = hargaJual.toFixed(0);
    }

    // Panggil saat halaman dimuat untuk mengisi harga jual
    document.addEventListener('DOMContentLoaded', hitungHargaJual);
</script>
@endpush

