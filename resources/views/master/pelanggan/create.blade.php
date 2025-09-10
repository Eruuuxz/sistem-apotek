@extends(in_array(auth()->user()->role, ['admin', 'kasir']) ? 'layouts.admin' : 'layouts.kasir')

@section('title', 'Tambah Pelanggan Baru')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6">Tambah Pelanggan Baru</h2>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pelanggan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="nama" class="block font-semibold mb-1">Nama Pelanggan <span class="text-red-600">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                class="w-full border rounded px-3 py-2 @error('nama') border-red-500 @enderror">
            @error('nama')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="telepon" class="block font-semibold mb-1">Telepon</label>
            <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}"
                class="w-full border rounded px-3 py-2 @error('telepon') border-red-500 @enderror">
            @error('telepon')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="alamat" class="block font-semibold mb-1">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3"
                class="w-full border rounded px-3 py-2 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
            @error('alamat')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="no_ktp" class="block font-semibold mb-1">Nomor KTP</label>
            <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp') }}"
                class="w-full border rounded px-3 py-2 @error('no_ktp') border-red-500 @enderror">
            @error('no_ktp')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="file_ktp" class="block font-semibold mb-1">Upload File KTP (JPG, PNG, GIF, max 2MB)</label>
            <input type="file" name="file_ktp" id="file_ktp" accept="image/*"
                class="w-full border rounded px-3 py-2 @error('file_ktp') border-red-500 @enderror">
            @error('file_ktp')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="status_member" class="block font-semibold mb-1">Status Member <span class="text-red-600">*</span></label>
            <select name="status_member" id="status_member"
                class="w-full border rounded px-3 py-2 @error('status_member') border-red-500 @enderror">
                <option value="">Pilih Status Member</option>
                <option value="non_member" {{ old('status_member') == 'non_member' ? 'selected' : '' }}>Non-Member</option>
                <option value="member" {{ old('status_member') == 'member' ? 'selected' : '' }}>Member</option>
            </select>
            @error('status_member')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="point" class="block font-semibold mb-1">Point</label>
            <input type="number" name="point" id="point" value="{{ old('point', 0) }}" min="0"
                class="w-full border rounded px-3 py-2 @error('point') border-red-500 @enderror">
            @error('point')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Simpan Pelanggan
            </button>
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
