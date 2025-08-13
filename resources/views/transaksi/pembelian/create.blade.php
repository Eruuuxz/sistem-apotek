@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Pembelian</h1>

<form action="#" method="POST" class="bg-white shadow rounded p-6">
    @csrf

    {{-- Informasi Faktur --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">No Faktur</label>
        <input type="text" name="no_faktur" value="FPB-{{ date('Y') }}-001" class="border rounded w-full px-3 py-2" readonly>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="border rounded w-full px-3 py-2">
    </div>

    {{-- Supplier --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Supplier</label>
        <select name="supplier" class="border rounded w-full px-3 py-2">
            <option value="">-- Pilih Supplier --</option>
            <option value="PT Farmasi Sehat">PT Farmasi Sehat</option>
            <option value="CV Obat Jaya">CV Obat Jaya</option>
        </select>
    </div>

    {{-- Tabel Item Pembelian --}}
    <div class="mb-4">
        <label class="block font-semibold mb-2">Daftar Obat</label>
        <table class="w-full border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">Nama Obat</th>
                    <th class="border px-2 py-1">Jumlah</th>
                    <th class="border px-2 py-1">Harga Satuan</th>
                    <th class="border px-2 py-1">Subtotal</th>
                    <th class="border px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-items">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" name="nama_obat[]" class="w-full px-2 py-1 border rounded">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" name="jumlah[]" class="w-full px-2 py-1 border rounded jumlah" value="1">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" name="harga[]" class="w-full px-2 py-1 border rounded harga" value="0">
                    </td>
                    <td class="border px-2 py-1 text-right subtotal">0</td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" class="text-red-500" onclick="hapusRow(this)">✖</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="bg-green-500 text-white px-3 py-1 rounded mt-2" onclick="tambahRow()">+ Tambah Item</button>
    </div>

    {{-- Total --}}
    <div class="mb-4 text-right font-bold">
        Total: <span id="total-harga">0</span>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="/pembelian" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
function hitungSubtotal(row) {
    let jumlah = row.querySelector('.jumlah').value;
    let harga = row.querySelector('.harga').value;
    let subtotal = jumlah * harga;
    row.querySelector('.subtotal').innerText = subtotal.toLocaleString();
    hitungTotal();
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('#table-items tr').forEach(row => {
        let jumlah = row.querySelector('.jumlah').value;
        let harga = row.querySelector('.harga').value;
        total += jumlah * harga;
    });
    document.getElementById('total-harga').innerText = total.toLocaleString();
}

function tambahRow() {
    let tbody = document.getElementById('table-items');
    let row = tbody.rows[0].cloneNode(true);
    row.querySelectorAll('input').forEach(input => input.value = '');
    row.querySelector('.subtotal').innerText = '0';
    tbody.appendChild(row);
}

function hapusRow(btn) {
    let row = btn.closest('tr');
    if (document.querySelectorAll('#table-items tr').length > 1) {
        row.remove();
        hitungTotal();
    }
}

// Event listener untuk input perubahan jumlah & harga
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('jumlah') || e.target.classList.contains('harga')) {
        hitungSubtotal(e.target.closest('tr'));
    }
});
</script>
@endpush
