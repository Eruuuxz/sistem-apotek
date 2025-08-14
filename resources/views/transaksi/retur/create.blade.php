{{-- File: /retur/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Retur')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Retur</h1>

<form action="{{ route('retur.store') }}" method="POST" class="bg-white shadow rounded p-6">
    @csrf

    <div class="mb-4">
        <label class="block font-semibold mb-1">No Retur</label>
        <input type="text" name="no_retur" value="{{ $noRetur }}" class="border rounded w-full px-3 py-2" readonly>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="border rounded w-full px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Jenis Retur</label>
        <select name="jenis" id="jenis_retur" class="border rounded w-full px-3 py-2">
            <option value="">-- Pilih Jenis Retur --</option>
            <option value="pembelian">Retur Pembelian (ke Supplier)</option>
            <option value="penjualan">Retur Penjualan (dari Customer)</option>
        </select>
    </div>

    <div class="mb-4" id="transaksi_pembelian_div" style="display: none;">
        <label class="block font-semibold mb-1">Pilih Transaksi Pembelian</label>
        <select name="transaksi_pembelian_id" id="transaksi_pembelian_id" class="border rounded w-full px-3 py-2">
            <option value="">-- Pilih Faktur Pembelian --</option>
            @foreach($pembelian as $p)
                <option value="{{ $p->id }}">FPB-{{ $p->no_faktur }} ({{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4" id="transaksi_penjualan_div" style="display: none;">
        <label class="block font-semibold mb-1">Pilih Transaksi Penjualan</label>
        <select name="transaksi_penjualan_id" id="transaksi_penjualan_id" class="border rounded w-full px-3 py-2">
            <option value="">-- Pilih Nota Penjualan --</option>
            @foreach($penjualan as $pj)
                <option value="{{ $pj->id }}">PJ-{{ $pj->no_nota }} ({{ \Carbon\Carbon::parse($pj->tanggal)->format('Y-m-d') }})</option>
            @endforeach
        </select>
    </div>

    <input type="hidden" name="transaksi_id" id="hidden_transaksi_id">

    {{-- Tabel Item Retur --}}
    <div class="mb-4">
        <label class="block font-semibold mb-2">Daftar Item Retur</label>
        <table class="w-full border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">Kode</th>
                    <th class="border px-2 py-1">Nama Item</th>
                    <th class="border px-2 py-1">Harga Satuan</th>
                    <th class="border px-2 py-1">Jumlah Retur</th>
                    <th class="border px-2 py-1">Max Qty</th>
                    <th class="border px-2 py-1">Subtotal</th>
                    <th class="border px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-items">
                {{-- Rows will be added by JavaScript --}}
            </tbody>
        </table>
        <button type="button" class="bg-green-500 text-white px-3 py-1 rounded mt-2" id="add_item_btn" style="display: none;">+ Tambah Item</button>
    </div>

    {{-- Total --}}
    <div class="mb-4 text-right font-bold">
        Total Retur: <span id="total-harga">0</span>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Keterangan (Opsional)</label>
        <textarea name="keterangan" class="border rounded w-full px-3 py-2" rows="3"></textarea>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Retur</button>
        <a href="{{ route('retur.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisReturSelect = document.getElementById('jenis_retur');
    const transaksiPembelianDiv = document.getElementById('transaksi_pembelian_div');
    const transaksiPenjualanDiv = document.getElementById('transaksi_penjualan_div');
    const transaksiPembelianIdSelect = document.getElementById('transaksi_pembelian_id');
    const transaksiPenjualanIdSelect = document.getElementById('transaksi_penjualan_id');
    const hiddenTransaksiIdInput = document.getElementById('hidden_transaksi_id');
    const tableItemsBody = document.getElementById('table-items');
    const addItemBtn = document.getElementById('add_item_btn');
    const totalHargaSpan = document.getElementById('total-harga');

    let availableItems = []; // Menyimpan item yang tersedia dari transaksi sumber

    function toggleTransaksiSelect() {
        const jenis = jenisReturSelect.value;
        transaksiPembelianDiv.style.display = 'none';
        transaksiPenjualanDiv.style.display = 'none';
        transaksiPembelianIdSelect.value = '';
        transaksiPenjualanIdSelect.value = '';
        hiddenTransaksiIdInput.value = '';
        tableItemsBody.innerHTML = ''; // Kosongkan tabel item
        addItemBtn.style.display = 'none'; // Sembunyikan tombol tambah item
        availableItems = []; // Reset available items
        hitungTotal();

        if (jenis === 'pembelian') {
            transaksiPembelianDiv.style.display = 'block';
        } else if (jenis === 'penjualan') {
            transaksiPenjualanDiv.style.display = 'block';
        }
    }

    async function fetchTransaksiItems(jenis, id) {
        if (!jenis || !id) {
            tableItemsBody.innerHTML = '';
            addItemBtn.style.display = 'none';
            availableItems = [];
            hitungTotal();
            return;
        }

        try {
            const response = await fetch(`/retur/sumber/${jenis}/${id}`);
            if (!response.ok) {
                throw new Error('Gagal mengambil data transaksi.');
            }
            const data = await response.json();
            availableItems = data.items;
            renderAvailableItems();
            addItemBtn.style.display = 'block'; // Tampilkan tombol tambah item
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data transaksi: ' + error.message);
            tableItemsBody.innerHTML = '';
            addItemBtn.style.display = 'none';
            availableItems = [];
            hitungTotal();
        }
    }

    function renderAvailableItems() {
        tableItemsBody.innerHTML = '';
        availableItems.forEach(item => {
            addRow(item);
        });
        hitungTotal();
    }

    function addRow(item = null) {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="border px-2 py-1">
                <input type="hidden" name="item_id[]" value="${item ? item.id : ''}">
                <span class="item-kode">${item ? item.kode : ''}</span>
            </td>
            <td class="border px-2 py-1">
                <span class="item-nama">${item ? item.nama : ''}</span>
            </td>
            <td class="border px-2 py-1">
                <input type="number" name="harga[]" class="w-full px-2 py-1 border rounded harga" value="${item ? item.harga : 0}" min="0" readonly>
            </td>
            <td class="border px-2 py-1">
                <input type="number" name="qty[]" class="w-full px-2 py-1 border rounded qty" value="1" min="1" max="${item ? item.max_qty : 1}">
            </td>
            <td class="border px-2 py-1">
                <span class="max-qty">${item ? item.max_qty : 1}</span>
            </td>
            <td class="border px-2 py-1 text-right subtotal">0</td>
            <td class="border px-2 py-1 text-center">
                <button type="button" class="text-red-500" onclick="hapusRow(this)">✖</button>
            </td>
        `;
        tableItemsBody.appendChild(newRow);
        hitungSubtotal(newRow); // Hitung subtotal untuk baris baru
    }

    function hitungSubtotal(row) {
        const qtyInput = row.querySelector('.qty');
        const hargaInput = row.querySelector('.harga');
        const subtotalSpan = row.querySelector('.subtotal');
        const maxQtySpan = row.querySelector('.max-qty');

        let qty = parseInt(qtyInput.value) || 0;
        let harga = parseFloat(hargaInput.value) || 0;
        let maxQty = parseInt(maxQtySpan.innerText) || 1;

        // Validasi qty tidak melebihi max_qty
        if (qty > maxQty) {
            qty = maxQty;
            qtyInput.value = maxQty;
            alert('Jumlah retur tidak boleh melebihi jumlah maksimal (' + maxQty + ').');
        }
        if (qty < 1) {
            qty = 1;
            qtyInput.value = 1;
        }

        const subtotal = qty * harga;
        subtotalSpan.innerText = subtotal.toLocaleString('id-ID');
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('#table-items tr').forEach(row => {
            const qty = parseInt(row.querySelector('.qty').value) || 0;
            const harga = parseFloat(row.querySelector('.harga').value) || 0;
            total += qty * harga;
        });
        totalHargaSpan.innerText = total.toLocaleString('id-ID');
    }

    // Event Listeners
    jenisReturSelect.addEventListener('change', toggleTransaksiSelect);

    transaksiPembelianIdSelect.addEventListener('change', function() {
        const id = this.value;
        hiddenTransaksiIdInput.value = id;
        fetchTransaksiItems('pembelian', id);
    });

    transaksiPenjualanIdSelect.addEventListener('change', function() {
        const id = this.value;
        hiddenTransaksiIdInput.value = id;
        fetchTransaksiItems('penjualan', id);
    });

    addItemBtn.addEventListener('click', function() {
        // Jika ingin menambahkan item secara manual (tidak dari transaksi sumber)
        // Anda perlu logika untuk memilih item dari daftar master barang/obat
        alert('Fitur tambah item manual belum diimplementasikan. Item akan otomatis dimuat dari transaksi yang dipilih.');
    });

    // Delegasi event untuk input qty dan harga di tabel
    tableItemsBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty') || e.target.classList.contains('harga')) {
            hitungSubtotal(e.target.closest('tr'));
        }
    });

    // Fungsi hapusRow (global agar bisa dipanggil dari onclick)
    window.hapusRow = function(btn) {
        let row = btn.closest('tr');
        if (tableItemsBody.querySelectorAll('tr').length > 0) { // Allow deleting all rows
            row.remove();
            hitungTotal();
        }
    };

    // Inisialisasi saat halaman dimuat
    toggleTransaksiSelect();
});
</script>
@endpush