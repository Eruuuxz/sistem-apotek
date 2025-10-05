@extends('layouts.admin')

@section('title', 'Tambah Pelanggan Tetap')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Formulir Pelanggan Tetap Baru</h2>
        <p class="text-sm text-gray-500">Isi detail di bawah untuk mendaftarkan pelanggan tetap baru.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong class="font-bold">Terjadi kesalahan!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pelanggan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Informasi Pribadi --}}
        <div class="border-t pt-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Pribadi</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block font-semibold mb-1 text-sm">Nama Pelanggan <span class="text-red-600">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="w-full border rounded-lg px-3 py-2 @error('nama') border-red-500 @enderror" placeholder="Masukkan nama lengkap">
                    @error('nama')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="telepon" class="block font-semibold mb-1 text-sm">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}" class="w-full border rounded-lg px-3 py-2 @error('telepon') border-red-500 @enderror" placeholder="Contoh: 08123456789">
                     @error('telepon')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6">
                <label for="alamat" class="block font-semibold mb-1 text-sm">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="w-full border rounded-lg px-3 py-2 @error('alamat') border-red-500 @enderror" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                @error('alamat')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        
        {{-- Informasi Identitas untuk Pelanggan Tetap --}}
         <div class="border-t pt-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Identitas (Wajib untuk Psikotropika)</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                    <label for="no_ktp" class="block font-semibold mb-1 text-sm">Nomor KTP</label>
                    <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp') }}" class="w-full border rounded-lg px-3 py-2 @error('no_ktp') border-red-500 @enderror" placeholder="Masukkan 16 digit NIK">
                     @error('no_ktp')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                 <div>
                    <label for="file_ktp" class="block font-semibold mb-1 text-sm">Upload File KTP</label>
                    <input type="file" name="file_ktp" id="file_ktp" accept="image/*" class="w-full border rounded-lg px-3 py-2 file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('file_ktp') border-red-500 @enderror">
                    @error('file_ktp')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold transition-colors">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold transition-colors">
                Simpan Pelanggan Tetap
            </button>
        </div>
    </form>
</div>
@endsection