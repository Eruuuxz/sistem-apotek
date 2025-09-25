@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
       <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Data Supplier</h2>
            <p class="text-sm text-gray-500">Perbarui detail untuk supplier: <span class="font-semibold">{{ $supplier->nama }}</span>.</p>
        </div>

        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Kode Supplier</label>
                    <input type="text" name="kode"
                        class="w-full border rounded-lg px-4 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('kode') border-red-500 @enderror"
                        value="{{ old('kode', $supplier->kode) }}" required>
                    @error('kode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Supplier</label>
                    <input type="text" name="nama"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nama') border-red-500 @enderror"
                        value="{{ old('nama', $supplier->nama) }}" required>
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">Alamat</label>
                <input type="text" name="alamat"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('alamat') border-red-500 @enderror"
                    value="{{ old('alamat', $supplier->alamat) }}">
                @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Kota</label>
                    <input type="text" name="kota"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('kota') border-red-500 @enderror"
                        value="{{ old('kota', $supplier->kota) }}">
                    @error('kota')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Telepon</label>
                    <input type="text" name="telepon"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none @error('telepon') border-red-500 @enderror"
                        value="{{ old('telepon', $supplier->telepon) }}">
                    @error('telepon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('supplier.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Update Supplier</button>
            </div>
        </form>
    </div>

@endsection