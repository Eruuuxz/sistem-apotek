@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')
<h1 class="text-2xl font-bold mb-4">Point of Sale (POS) - Kasir</h1>

<div class="grid grid-cols-3 gap-4">
    <!-- Form Input Barang -->
    <div class="col-span-2 bg-white p-4 shadow rounded">
        <div class="mb-4 flex gap-2">
            <input type="text" id="kode_barang" placeholder="Scan / Ketik Kode Barang" class="border px-3 py-2 w-full">
            <button onclick="tambahBarang()" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
        </div>

        <table class="w-full text-sm bg-white border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-2 py-1">Kode</th>
                    <th class="px-2 py-1">Nama Barang</th>
                    <th class="px-2 py-1">Harga</th>
                    <th class="px-2 py-1">Qty</th>
                    <th class="px-2 py-1">Subtotal</th>
                    <th class="px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody id="daftar_barang">
                <!-- Barang akan ditambahkan lewat JS -->
            </tbody>
        </table>
    </div>

    <!-- Ringkasan Pembayaran -->
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
        <div class="mb-3">
            <label>Total</label>
            <input type="text" id="total" class="w-full border px-3 py-2 text-right font-bold" value="0" readonly>
        </div>
        <div class="mb-3">
            <label>Bayar</label>
            <input type="number" id="bayar" class="w-full border px-3 py-2 text-right" oninput="hitungKembalian()">
        </div>
        <div class="mb-3">
            <label>Kembalian</label>
            <input type="text" id="kembalian" class="w-full border px-3 py-2 text-right font-bold" value="0" readonly>
        </div>
        <button class="bg-green-600 text-white w-full py-2 rounded">Simpan & Cetak Struk</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let daftarBarang = [
        { kode: 'BRG001', nama: 'Paracetamol', harga: 5000 },
        { kode: 'BRG002', nama: 'Amoxicillin', harga: 8000 },
        { kode: 'BRG003', nama: 'Vitamin C', harga: 3000 },
        { kode: 'BRG004', nama: 'Ibuprofen', harga: 7000 }
    ];

    let keranjang = [];

    function tambahBarang() {
        let kode = document.getElementById('kode_barang').value;
        let barang = daftarBarang.find(b => b.kode.toLowerCase() === kode.toLowerCase());

        if (barang) {
            let item = keranjang.find(i => i.kode === barang.kode);
            if (item) {
                item.qty += 1;
            } else {
                keranjang.push({ ...barang, qty: 1 });
            }
            renderKeranjang();
        } else {
            alert('Barang tidak ditemukan');
        }
        document.getElementById('kode_barang').value = '';
    }

    function hapusBarang(kode) {
        keranjang = keranjang.filter(i => i.kode !== kode);
        renderKeranjang();
    }

    function ubahQty(kode, qty) {
        let item = keranjang.find(i => i.kode === kode);
        if (item) {
            item.qty = parseInt(qty) || 1;
            renderKeranjang();
        }
    }

    function renderKeranjang() {
        let tbody = document.getElementById('daftar_barang');
        tbody.innerHTML = '';
        let total = 0;

        keranjang.forEach(item => {
            let subtotal = item.qty * item.harga;
            total += subtotal;
            tbody.innerHTML += `
                <tr>
                    <td class="border px-2 py-1">${item.kode}</td>
                    <td class="border px-2 py-1">${item.nama}</td>
                    <td class="border px-2 py-1 text-right">Rp ${item.harga.toLocaleString()}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="number" value="${item.qty}" class="w-16 border text-center" oninput="ubahQty('${item.kode}', this.value)">
                    </td>
                    <td class="border px-2 py-1 text-right">Rp ${subtotal.toLocaleString()}</td>
                    <td class="border px-2 py-1 text-center">
                        <button onclick="hapusBarang('${item.kode}')" class="text-red-500">Hapus</button>
                    </td>
                </tr>
            `;
        });

        document.getElementById('total').value = total;
        hitungKembalian();
    }

    function hitungKembalian() {
        let total = parseInt(document.getElementById('total').value) || 0;
        let bayar = parseInt(document.getElementById('bayar').value) || 0;
        document.getElementById('kembalian').value = bayar - total;
    }
</script>
@endpush
