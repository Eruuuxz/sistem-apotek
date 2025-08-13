@extends('layouts.admin')

@section('title', 'Tambah Data Obat')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Data Obat</h1>

<div class="bg-white p-6 shadow rounded w-full md:w-2/3">
    <form>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Kode Obat</label>
            <input type="text" class="border px-3 py-2 w-full" placeholder="Masukkan kode obat">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Nama Obat</label>
            <input type="text" class="border px-3 py-2 w-full" placeholder="Masukkan nama obat">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Kategori</label>
            <select class="border px-3 py-2 w-full">
                <option value="">-- Pilih Kategori --</option>
                <option value="bebas">Obat Bebas</option>
                <option value="bebas_terbatas">Obat Bebas Terbatas</option>
                <option value="keras">Obat Keras</option>
                <option value="psikotropika">Obat Psikotropika (OTK)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Stok</label>
            <input type="number" class="border px-3 py-2 w-full" placeholder="Jumlah stok tersedia">
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block mb-1 font-semibold">Harga Dasar (Rp)</label>
                <input type="number" id="harga_dasar" class="border px-3 py-2 w-full" placeholder="0" oninput="hitungHargaJual()">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Persentase Untung (%)</label>
                <input type="number" id="persen_untung" class="border px-3 py-2 w-full" placeholder="0" oninput="hitungHargaJual()">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Harga Jual (Rp)</label>
                <input type="number" id="harga_jual" class="border px-3 py-2 w-full font-bold bg-gray-100" readonly>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="/master/obat" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function hitungHargaJual() {
        let hargaDasar = parseFloat(document.getElementById('harga_dasar').value) || 0;
        let persen = parseFloat(document.getElementById('persen_untung').value) || 0;

        let hargaJual = hargaDasar + (hargaDasar * persen / 100);
        document.getElementById('harga_jual').value = hargaJual.toFixed(0);
    }
</script>
@endpush
