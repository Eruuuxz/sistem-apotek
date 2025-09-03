@extends('layouts.admin')

@section('title', 'Tambah Supplier')

@section('content')

<div class="bg-white p-6 shadow rounded w-full md:w-1/2 mx-auto">
    <h1 class="text-xl font-semibold mb-6">Tambah Supplier</h1>
    <form action="{{ route('supplier.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-medium text-gray-700">Kode Supplier</label>
            <input type="text" name="kode" class="w-full border px-3 py-2 rounded @error('kode') border-red-500 @enderror" value="{{ old('kode') }}" required>
            @error('kode')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium text-gray-700">Nama Supplier</label>
            <input type="text" name="nama" class="w-full border px-3 py-2 rounded @error('nama') border-red-500 @enderror" value="{{ old('nama') }}" required>
            @error('nama')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium text-gray-700">Alamat</label>
            <input type="text" name="alamat" class="w-full border px-3 py-2 rounded @error('alamat') border-red-500 @enderror" value="{{ old('alamat') }}">
            @error('alamat')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium text-gray-700">Kota</label>
            <input type="text" name="kota" class="w-full border px-3 py-2 rounded @error('kota') border-red-500 @enderror" value="{{ old('kota') }}">
            @error('kota')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium text-gray-700">Telepon</label>
            <input type="text" name="telepon" class="w-full border px-3 py-2 rounded @error('telepon') border-red-500 @enderror" value="{{ old('telepon') }}">
            @error('telepon')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Simpan</button>
            <a href="{{ route('supplier.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Batal</a>
        </div>

    </form>
</div>

@endsection
