{{-- resources/views/master/pelanggan/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Pelanggan Baru')

@section('content')
    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <h2 class="text-2xl font-bold mb-6">Tambah Pelanggan Baru</h2>
        <form action="{{ route('pelanggan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama') border-red-500 @enderror" value="{{ old('nama') }}" required>
                @error('nama')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telepon" class="block text-gray-700 text-sm font-bold mb-2">Telepon</label>
                <input type="text" name="telepon" id="telepon" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telepon') border-red-500 @enderror" value="{{ old('telepon') }}">
                @error('telepon')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="no_ktp" class="block text-gray-700 text-sm font-bold mb-2">Nomor KTP</label>
                <input type="text" name="no_ktp" id="no_ktp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('no_ktp') border-red-500 @enderror" value="{{ old('no_ktp') }}">
                @error('no_ktp')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="file_ktp" class="block text-gray-700 text-sm font-bold mb-2">Upload File KTP (JPG, PNG, GIF, max 2MB)</label>
                <input type="file" name="file_ktp" id="file_ktp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('file_ktp') border-red-500 @enderror">
                @error('file_ktp')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status_member" class="block text-gray-700 text-sm font-bold mb-2">Status Member</label>
                <select name="status_member" id="status_member" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status_member') border-red-500 @enderror" required>
                    <option value="non_member" {{ old('status_member') == 'non_member' ? 'selected' : '' }}>Non-Member</option>
                    <option value="member" {{ old('status_member', 'member') == 'member' ? 'selected' : '' }}>Member</option>
                </select>
                @error('status_member')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="point" class="block text-gray-700 text-sm font-bold mb-2">Point</label>
                <input type="number" name="point" id="point" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('point') border-red-500 @enderror" value="{{ old('point', 0) }}" min="0">
                @error('point')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Pelanggan
                </button>
                <a href="{{ route('pelanggan.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection