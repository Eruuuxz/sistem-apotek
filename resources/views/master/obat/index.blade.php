@extends('layouts.admin')

@section('title', 'Daftar Obat')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar Obat</h1>

<div class="bg-white p-4 shadow rounded">
    <div class="flex justify-between items-center mb-4">
        <a href="/master/obat/create" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Obat</a>
        <input type="text" placeholder="Cari obat..." class="border px-3 py-2 w-64">
    </div>

    <table class="w-full text-sm border">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-2 py-1">Kode</th>
                <th class="px-2 py-1">Nama</th>
                <th class="px-2 py-1">Kategori</th>
                <th class="px-2 py-1 text-right">Stok</th>
                <th class="px-2 py-1 text-right">Harga Dasar</th>
                <th class="px-2 py-1 text-right">Untung (%)</th>
                <th class="px-2 py-1 text-right">Harga Jual</th>
                <th class="px-2 py-1 text-right">Keuntungan/Unit</th>
                <th class="px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel_obat">
            <!-- Data dummy via JS -->
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    let dataObat = [
        { kode: 'OB001', nama: 'Paracetamol', kategori: 'Obat Bebas', stok: 50, harga_dasar: 5000, persen: 20 },
        { kode: 'OB002', nama: 'Amoxicillin', kategori: 'Obat Keras', stok: 30, harga_dasar: 8000, persen: 25 },
        { kode: 'OB003', nama: 'Vitamin C', kategori: 'Obat Bebas Terbatas', stok: 100, harga_dasar: 3000, persen: 15 },
        { kode: 'OB004', nama: 'Diazepam', kategori: 'Psikotropika', stok: 10, harga_dasar: 15000, persen: 30 }
    ];

    function renderTabel() {
        let tbody = document.getElementById('tabel_obat');
        tbody.innerHTML = '';

        dataObat.forEach(obat => {
            let hargaJual = obat.harga_dasar + (obat.harga_dasar * obat.persen / 100);
            let keuntungan = hargaJual - obat.harga_dasar;

            tbody.innerHTML += `
                <tr>
                    <td class="border px-2 py-1">${obat.kode}</td>
                    <td class="border px-2 py-1">${obat.nama}</td>
                    <td class="border px-2 py-1">${obat.kategori}</td>
                    <td class="border px-2 py-1 text-right">${obat.stok}</td>
                    <td class="border px-2 py-1 text-right">Rp ${obat.harga_dasar.toLocaleString()}</td>
                    <td class="border px-2 py-1 text-right">${obat.persen}%</td>
                    <td class="border px-2 py-1 text-right">Rp ${hargaJual.toLocaleString()}</td>
                    <td class="border px-2 py-1 text-right">Rp ${keuntungan.toLocaleString()}</td>
                    <td class="border px-2 py-1">
                        <a href="#" class="text-blue-500">Edit</a> |
                        <a href="#" class="text-red-500">Hapus</a>
                    </td>
                </tr>
            `;
        });
    }

    renderTabel();
</script>
@endpush
