@extends('layouts.admin')

@section('title', 'Edit Barang')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Barang</h1>

<form action="{{ route('barang.update', $barang->id) }}" method="POST" class="bg-white p-6 shadow rounded w-1/2">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label class="block mb-1">Kode Barang</label>
        <input type="text" name="kode" class="w-full border px-3 py-2 @error('kode') border-red-500 @enderror" value="{{ old('kode', $barang->kode) }}" required>
        @error('kode')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Nama Barang</label>
        <input type="text" name="nama" class="w-full border px-3 py-2 @error('nama') border-red-500 @enderror" value="{{ old('nama', $barang->nama) }}" required>
        @error('nama')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Harga Jual</label>
        <input type="number" name="harga_jual" class="w-full border px-3 py-2 @error('harga_jual') border-red-500 @enderror" value="{{ old('harga_jual', $barang->harga_jual) }}" required>
        @error('harga_jual')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Stok</label>
        <input type="number" name="stok" class="w-full border px-3 py-2 @error('stok') border-red-500 @enderror" value="{{ old('stok', $barang->stok) }}" required>
        @error('stok')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Supplier</label>
        <select name="supplier_id" class="w-full border px-3 py-2 @error('supplier_id') border-red-500 @enderror">
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $barang->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
            @endforeach
        </select>
        @error('supplier_id')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    <a href="{{ route('barang.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
</form>
@endsection

