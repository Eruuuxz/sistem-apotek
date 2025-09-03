{{-- File: /pembelian/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')

<div class="bg-white shadow rounded p-6 space-y-6">
    <h1 class="text-xl font-semibold mb-4">Tambah Pembelian</h1>

    <form action="{{ route('pembelian.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Informasi Faktur --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold mb-1">No Faktur</label>
                <input type="text" name="no_faktur" value="{{ $noFaktur }}" class="border rounded w-full px-3 py-2 bg-gray-100" readonly>
            </div>
            <div>
                <label class="block font-semibold mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold mb-1">Supplier</label>
                <select name="supplier_id" id="supplier-select" class="border rounded w-full px-3 py-2">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Daftar Obat --}}
        <div>
            <label class="block font-semibold mb-2">Daftar Obat</label>
            <div id="obat-list" class="border p-3 rounded bg-gray-50 max-h-64 overflow-y-auto text-sm text-gray-700">
                Pilih supplier terlebih dahulu.
            </div>
        </div>

        {{-- Tabel Item Pembelian --}}
        <div>
            <label class="block font-semibold mb-2">Item Pembelian</label>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 border">Nama Obat</th>
                            <th class="px-2 py-1 border">Jumlah</th>
                            <th class="px-2 py-1 border">Harga Satuan</th>
                            <th class="px-2 py-1 border">Subtotal</th>
                            <th class="px-2 py-1 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-items">
                        {{-- Baris akan ditambahkan otomatis --}}
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-right font-bold text-lg text-blue-600">
                Total: <span id="total-harga">Rp 0</span>
            </div>
        </div>

        <div class="flex gap-2 justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Simpan</button>
            <a href="{{ route('pembelian.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">Batal</a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier-select');
    const obatList = document.getElementById('obat-list');
    const tableItems = document.getElementById('table-items');
    const totalHargaEl = document.getElementById('total-harga');

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function hitungSubtotal(row) {
        let jumlah = parseInt(row.querySelector('.jumlah').value) || 0;
        let harga = parseFloat(row.querySelector('.harga').value) || 0;
        row.querySelector('.subtotal').innerText = formatRupiah(jumlah * harga);
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        tableItems.querySelectorAll('tr').forEach(row => {
            let subtotal = parseFloat(row.querySelector('.subtotal').innerText.replace(/\D/g, '')) || 0;
            total += subtotal;
        });
        totalHargaEl.innerText = formatRupiah(total);
    }

    function tambahItem(obat) {
        if([...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id)) return;

        let row = document.createElement('tr');
        row.dataset.id = obat.id;
        row.classList.add('hover:bg-gray-50');
        row.innerHTML = `
            <td class="px-2 py-1 border">${obat.kode} - ${obat.nama}</td>
            <td class="px-2 py-1 border"><input type="number" name="jumlah[]" value="1" min="1" class="w-full jumlah px-2 py-1 border rounded"></td>
            <td class="px-2 py-1 border"><input type="number" name="harga[]" value="${obat.harga_dasar}" class="w-full harga px-2 py-1 border rounded bg-gray-100" readonly></td>
            <td class="px-2 py-1 border text-right subtotal">${formatRupiah(obat.harga_dasar)}</td>
            <td class="px-2 py-1 border text-center"><button type="button" class="text-red-500 font-bold hover:text-red-700 transition" onclick="this.closest('tr').remove();hitungTotal()">✖</button></td>
            <input type="hidden" name="obat_id[]" value="${obat.id}">
        `;
        tableItems.appendChild(row);

        row.querySelector('.jumlah').addEventListener('input', () => hitungSubtotal(row));

        hitungTotal();
    }

    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;
        obatList.innerHTML = '<div class="text-gray-500 text-sm">Memuat...</div>';

        if(!supplierId) {
            obatList.innerHTML = 'Pilih supplier terlebih dahulu.';
            return;
        }

        fetch(`/supplier/${supplierId}/obat`)
            .then(res => res.json())
            .then(data => {
                if(data.length === 0) {
                    obatList.innerHTML = 'Tidak ada obat untuk supplier ini.';
                    return;
                }

                let html = '<table class="w-full border border-gray-300 table-auto">';
                html += '<thead class="bg-gray-100"><tr><th></th><th class="px-2 py-1">Kode</th><th class="px-2 py-1">Nama</th><th class="px-2 py-1 text-right">Stok Apotek</th><th class="px-2 py-1 text-right">Harga</th></tr></thead><tbody>';

                data.forEach(obat => {
                    html += `<tr class="hover:bg-gray-50">
                        <td class="text-center px-2 py-1"><input type="checkbox" class="obat-checkbox" data-obat='${JSON.stringify(obat)}'></td>
                        <td class="px-2 py-1">${obat.kode}</td>
                        <td class="px-2 py-1">${obat.nama}</td>
                        <td class="px-2 py-1 text-right">${obat.stok}</td>
                        <td class="px-2 py-1 text-right">${formatRupiah(obat.harga_dasar)}</td>
                    </tr>`;
                });

                html += '</tbody></table>';
                obatList.innerHTML = html;

                document.querySelectorAll('.obat-checkbox').forEach(cb => {
                    cb.addEventListener('change', function() {
                        const obat = JSON.parse(this.dataset.obat);
                        if(this.checked) tambahItem(obat);
                        else {
                            const row = tableItems.querySelector(`tr[data-id='${obat.id}']`);
                            if(row) row.remove();
                            hitungTotal();
                        }
                    });
                });
            })
            .catch(err => {
                console.error(err);
                obatList.innerHTML = '<div class="text-red-500 text-sm">Gagal memuat data obat.</div>';
            });
    });
});
</script>
@endpush
