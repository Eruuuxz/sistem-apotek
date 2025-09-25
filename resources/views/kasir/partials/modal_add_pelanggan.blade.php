{{-- Modal Tambah Pelanggan Cepat --}}
<div id="addPelangganModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-11/12 md:w-1/3 p-6 rounded-2xl shadow-lg relative">
        <button onclick="closeAddPelangganModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
        <h2 class="text-xl font-semibold mb-4">Tambah Pelanggan Baru</h2>
        <form id="add-pelanggan-form" class="space-y-4">
            @csrf
            <div>
                <label for="new_nama_pelanggan" class="block text-sm font-medium text-gray-700">Nama Pelanggan <span class="text-red-600">*</span></label>
                <input type="text" id="new_nama_pelanggan" name="nama" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label for="new_telepon_pelanggan" class="block text-sm font-medium text-gray-700">Telepon</label>
                <input type="text" id="new_telepon_pelanggan" name="telepon" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label for="new_alamat_pelanggan" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea id="new_alamat_pelanggan" name="alamat" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Simpan Pelanggan</button>
        </form>
    </div>
</div>