{{-- resources/views/master/pelanggan/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Pelanggan')

@section('content')
    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <h2 class="text-2xl font-bold mb-6">Edit Pelanggan: {{ $pelanggan->nama }}</h2>
        <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama') border-red-500 @enderror" value="{{ old('nama', $pelanggan->nama) }}" required>
                @error('nama')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telepon" class="block text-gray-700 text-sm font-bold mb-2">Telepon</label>
                <input type="text" name="telepon" id="telepon" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telepon') border-red-500 @enderror" value="{{ old('telepon', $pelanggan->telepon) }}">
                @error('telepon')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('alamat') border-red-500 @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="no_ktp" class="block text-gray-700 text-sm font-bold mb-2">Nomor KTP</label>
                <input type="text" name="no_ktp" id="no_ktp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('no_ktp') border-red-500 @enderror" value="{{ old('no_ktp', $pelanggan->no_ktp) }}">
                @error('no_ktp')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">File KTP Saat Ini</label>
                @if ($pelanggan->file_ktp)
                    <div class="flex items-center mb-2">
                        <a href="{{ Storage::url($pelanggan->file_ktp) }}" target="_blank" class="text-blue-500 hover:underline mr-4">Lihat KTP</a>
                        <input type="checkbox" name="remove_file_ktp" id="remove_file_ktp" class="mr-2 leading-tight">
                        <label for="remove_file_ktp" class="text-sm">Hapus File KTP</label>
                    </div>
                @else
                    <p class="text-gray-600">Tidak ada file KTP terupload.</p>
                @endif
                <label for="file_ktp" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Upload File KTP Baru (JPG, PNG, GIF, max 2MB)</label>
                <input type="file" name="file_ktp" id="file_ktp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('file_ktp') border-red-500 @enderror">
                @error('file_ktp')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status_member" class="block text-gray-700 text-sm font-bold mb-2">Status Member</label>
                <select name="status_member" id="status_member" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status_member') border-red-500 @enderror" required>
                    <option value="non_member" {{ old('status_member', $pelanggan->status_member) == 'non_member' ? 'selected' : '' }}>Non-Member</option>
                    <option value="member" {{ old('status_member', $pelanggan->status_member) == 'member' ? 'selected' : '' }}>Member</option>
                </select>
                @error('status_member')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="point" class="block text-gray-700 text-sm font-bold mb-2">Point</label>
                <input type="number" name="point" id="point" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('point') border-red-500 @enderror" value="{{ old('point', $pelanggan->point) }}" min="0">
                @error('point')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Pelanggan
                </button>
                <a href="{{ route('pelanggan.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection