<div class="space-y-4">
    <div>
        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $biayaOperasional->tanggal ?? now()->toDateString()) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('tanggal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <input type="text" name="deskripsi" id="deskripsi" value="{{ old('deskripsi', $biayaOperasional->deskripsi ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('deskripsi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
        <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $biayaOperasional->jumlah ?? '') }}" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('jumlah') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>