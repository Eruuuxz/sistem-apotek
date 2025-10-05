@extends('layouts.admin')

@section('title', 'Edit Pelanggan')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Edit Data Pelanggan</h2>
        <p class="text-sm text-gray-500">Perbarui detail untuk <span class="font-semibold">{{ $pelanggan->nama }}</span>.</p>
    </div>
    
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong class="font-bold">Terjadi kesalahan!</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Informasi Pribadi --}}
        <div class="border-t pt-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Pribadi</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block font-semibold mb-1 text-sm">Nama Pelanggan <span class="text-red-600">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $pelanggan->nama) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label for="telepon" class="block font-semibold mb-1 text-sm">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $pelanggan->telepon) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            <div class="mt-6">
                <label for="alamat" class="block font-semibold mb-1 text-sm">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('alamat', $pelanggan->alamat) }}</textarea>
            </div>
        </div>

        {{-- Informasi Identitas --}}
         <div class="border-t pt-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Identitas</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                    <label for="no_ktp" class="block font-semibold mb-1 text-sm">Nomor KTP</label>
                    <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp', $pelanggan->no_ktp) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                 <div>
                    <label for="file_ktp" class="block font-semibold mb-1 text-sm">Ganti File KTP</label>
                    @if($pelanggan->file_ktp)
                        <div class="mb-2">
                            <a href="{{ Storage::url($pelanggan->file_ktp) }}" target="_blank" class="text-blue-500 text-xs hover:underline">Lihat file saat ini</a>
                        </div>
                    @endif
                    <input type="file" name="file_ktp" id="file_ktp" accept="image/*" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4 border-t mt-8">
            <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Update Pelanggan</button>
        </div>
    </form>
</div>
@endsection