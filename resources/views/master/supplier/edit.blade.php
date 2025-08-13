@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Supplier</h1>

<form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="bg-white p-6 shadow rounded w-1/2">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label class="block mb-1">Kode Supplier</label>
        <input type="text" name="kode" class="w-full border px-3 py-2 @error('kode') border-red-500 @enderror" value="{{ old('kode', $supplier->kode) }}" required>
        @error('kode')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Nama Supplier</label>
        <input type="text" name="nama" class="w-full border px-3 py-2 @error('nama') border-red-500 @enderror" value="{{ old('nama', $supplier->nama) }}" required>
        @error('nama')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Alamat</label>
        <input type="text" name="alamat" class="w-full border px-3 py-2 @error('alamat') border-red-500 @enderror" value="{{ old('alamat', $supplier->alamat) }}">
        @error('alamat')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Kota</label>
        <input type="text" name="kota" class="w-full border px-3 py-2 @error('kota') border-red-500 @enderror" value="{{ old('kota', $supplier->kota) }}">
        @error('kota')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block mb-1">Telepon</label>
        <input type="text" name="telepon" class="w-full border px-3 py-2 @error('telepon') border-red-500 @enderror" value="{{ old('telepon', $supplier->telepon) }}">
        @error('telepon')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    <a href="{{ route('supplier.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
</form>
@endsection

