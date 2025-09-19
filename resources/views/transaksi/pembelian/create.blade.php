@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')

    <div class="bg-white shadow rounded p-6 space-y-6">

        <form id="purchase-form" action="{{ route('pembelian.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Informasi Faktur --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold mb-1">No Faktur Internal</label>
                    <input type="text" name="no_faktur" value="{{ $noFaktur }}" class="border rounded w-full px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">No Faktur PBF</label>
                    <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf') }}" class="border rounded w-full px-3 py-2" placeholder="Nomor faktur dari PBF">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Tanggal</label>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', date('Y-m-d\TH:i:s')) }}" class="border rounded w-full px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Supplier</label>
                    <select name="supplier_id" id="supplier-select" class="border rounded w-full px-3 py-2">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Surat Pesanan (Opsional)</label>
                    <select name="surat_pesanan_id" id="surat-pesanan-select" class="border rounded w-full px-3 py-2">
                        <option value="">-- Pilih Surat Pesanan --</option>
                        @foreach($suratPesanans as $sp)
                            <option value="{{ $sp->id }}" data-supplier-id="{{ $sp->supplier_id }}" {{ old('surat_pesanan_id') == $sp->id ? 'selected' : '' }}>
                                {{ $sp->no_sp }} ({{ $sp->supplier->nama ?? '-' }}) - Status: {{ ucfirst($sp->status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Daftar Obat --}}
            <div>
                <label class="block font-semibold mb-2">Daftar Obat</label>
                <div id="obat-list" class="border p-3 rounded bg-gray-50 max-h-64 overflow-y-auto text-sm text-gray-700">
                    Pilih supplier atau Surat Pesanan terlebih dahulu.
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
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    @php
                                        $obat = \App\Models\Obat::find($item['obat_id']);
                                        $subtotal = $item['jumlah'] * $item['harga_beli'];
                                    @endphp
                                    <tr data-id="{{ $item['obat_id'] }}" data-index="{{ $index }}">
                                        <td class="px-2 py-1 border">{{ $obat->kode }} - {{ $obat->nama }}</td>
                                        <td class="px-2 py-1 border">
                                            <input type="number" name="items[{{ $index }}][jumlah]" value="{{ $item['jumlah'] }}" min="1"
                                                   class="w-full jumlah px-2 py-1 border rounded">
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="number" name="items[{{ $index }}][harga_beli]" value="{{ $item['harga_beli'] }}"
                                                   class="w-full harga px-2 py-1 border rounded">
                                        </td>
                                        <td class="px-2 py-1 border text-right subtotal" data-subtotal="{{ $subtotal }}">
                                            {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="text-red-500 font-bold remove-item">✖</button>
                                        </td>
                                        <input type="hidden" name="items[{{ $index }}][obat_id]" value="{{ $item['obat_id'] }}">
                                        @if(isset($item['sp_detail_id']))
                                            <input type="hidden" name="items[{{ $index }}][sp_detail_id]" value="{{ $item['sp_detail_id'] }}">
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-right font-bold text-lg text-blue-600">
                    Total: <span id="total-harga">Rp 0</span>
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Simpan</button>
                <a href="{{ route('pembelian.index') }}"
                   class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">Batal</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const supplierSelect = document.getElementById('supplier-select');
            const suratPesananSelect = document.getElementById('surat-pesanan-select');
            const obatList = document.getElementById('obat-list');
            const tableItems = document.getElementById('table-items');
            const totalHargaEl = document.getElementById('total-harga');
            const form = document.getElementById('purchase-form');

            // Set a global counter to ensure unique names for new inputs
            let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

            function formatRupiah(angka) {
                if (isNaN(angka) || angka === null || angka === undefined) {
                    return 'Rp 0';
                }
                let num = parseFloat(angka);
                if (num < 0) {
                    return '- Rp ' + Math.abs(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
                return 'Rp ' + num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }

            function hitungSubtotal(row) {
                let jumlah = parseInt(row.querySelector('.jumlah').value) || 0;
                let harga = parseFloat(row.querySelector('.harga').value) || 0;
                let subtotal = jumlah * harga;

                row.querySelector('.subtotal').dataset.subtotal = subtotal;
                row.querySelector('.subtotal').innerText = formatRupiah(subtotal);

                hitungTotal();
            }

            function hitungTotal() {
                let total = 0;
                tableItems.querySelectorAll('tr').forEach(row => {
                    let subtotalEl = row.querySelector('.subtotal');
                    if (subtotalEl && subtotalEl.dataset.subtotal) {
                        let subtotal = parseFloat(subtotalEl.dataset.subtotal) || 0;
                        total += subtotal;
                    }
                });
                totalHargaEl.innerText = formatRupiah(total);
            }

            function addEventListenersToRow(row) {
                row.querySelector('.jumlah').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.harga').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.remove-item').addEventListener('click', function() {
                    const id = row.dataset.id;
                    const checkbox = document.querySelector(`.obat-checkbox[data-obat*='"id":${id}']`) || 
                                     document.querySelector(`.obat-checkbox-sp[data-obat*='"id":${id}']`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                    row.remove();
                    hitungTotal();
                });
            }

            function tambahItem(obat, spData = null) {
                if ([...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id)) {
                    alert('Obat ini sudah ada di daftar pembelian.');
                    return;
                }

                let row = document.createElement('tr');
                row.dataset.id = obat.id;
                row.dataset.index = itemIndex; // Set the unique index
                row.classList.add('hover:bg-gray-50');

                let hargaDasar = parseFloat(obat.harga_dasar) || 0;
                let qtyAwal = 1;
                let spDetailId = '';

                if (spData) {
                    hargaDasar = parseFloat(spData.harga_satuan) || 0;
                    const sisaPesan = spData.qty_pesan - spData.qty_terima;
                    qtyAwal = sisaPesan;
                    spDetailId = spData.id;
                }

                row.innerHTML = `
                    <td class="px-2 py-1 border">${obat.kode} - ${obat.nama}</td>
                    <td class="px-2 py-1 border">
                        <input type="number" name="items[${itemIndex}][jumlah]" value="${qtyAwal}" min="1"
                               class="w-full jumlah px-2 py-1 border rounded">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="number" name="items[${itemIndex}][harga_beli]" value="${hargaDasar}" step="0.01" min="0"
                               class="w-full harga px-2 py-1 border rounded">
                    </td>
                    <td class="px-2 py-1 border text-right subtotal" data-subtotal="${hargaDasar * qtyAwal}">
                        ${formatRupiah(hargaDasar * qtyAwal)}
                    </td>
                    <td class="px-2 py-1 border text-center">
                        <button type="button" class="text-red-500 font-bold remove-item">✖</button>
                    </td>
                    <input type="hidden" name="items[${itemIndex}][obat_id]" value="${obat.id}">
                    ${spDetailId ? `<input type="hidden" name="items[${itemIndex}][sp_detail_id]" value="${spDetailId}">` : ''}
                `;
                tableItems.appendChild(row);

                addEventListenersToRow(row);
                itemIndex++;
                hitungTotal();
            }

            // Event listener untuk supplier select
            supplierSelect.addEventListener('change', function () {
                const supplierId = this.value;
                suratPesananSelect.value = ''; // Reset SP saat supplier berubah
                obatList.innerHTML = '<div class="text-gray-500 text-sm">Memuat...</div>';
                tableItems.innerHTML = ''; // Clear item pembelian
                itemIndex = 0; // Reset counter

                if (!supplierId) {
                    obatList.innerHTML = 'Pilih supplier atau Surat Pesanan terlebih dahulu.';
                    hitungTotal();
                    return;
                }

                fetch(`/supplier/${supplierId}/obat`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            obatList.innerHTML = 'Tidak ada obat untuk supplier ini.';
                            return;
                        }

                        let html = '<table class="w-full border border-gray-300 table-auto">';
                        html += '<thead class="bg-gray-100"><tr><th></th><th class="px-2 py-1">Kode</th><th class="px-2 py-1">Nama</th><th class="px-2 py-1 text-right">Stok Apotek</th><th class="px-2 py-1 text-right">Harga Dasar</th></tr></thead><tbody>';

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
                            cb.addEventListener('change', function () {
                                const obat = JSON.parse(this.dataset.obat);
                                if (this.checked) {
                                    tambahItem(obat);
                                } else {
                                    const row = tableItems.querySelector(`tr[data-id='${obat.id}']`);
                                    if (row) row.remove();
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

            // Event listener untuk surat pesanan select
            suratPesananSelect.addEventListener('change', function () {
                const spId = this.value;
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption) {
                    const spSupplierId = selectedOption.dataset.supplierId;
                    supplierSelect.value = spSupplierId; // Otomatis pilih supplier dari SP
                }
                
                obatList.innerHTML = '<div class="text-gray-500 text-sm">Memuat...</div>';
                tableItems.innerHTML = ''; // Clear item pembelian
                itemIndex = 0; // Reset counter

                if (!spId) {
                    obatList.innerHTML = 'Pilih supplier atau Surat Pesanan terlebih dahulu.';
                    hitungTotal();
                    return;
                }

                fetch(`/surat_pesanan/${spId}/details`)
                    .then(res => res.json())
                    .then(sp => {
                        if (sp.details.length === 0) {
                            obatList.innerHTML = 'Tidak ada obat di Surat Pesanan ini.';
                            return;
                        }

                        let html = '<table class="w-full border border-gray-300 table-auto">';
                        html += '<thead class="bg-gray-100"><tr><th></th><th class="px-2 py-1">Kode</th><th class="px-2 py-1">Nama</th><th class="px-2 py-1 text-right">Qty Pesan</th><th class="px-2 py-1 text-right">Qty Terima</th><th class="px-2 py-1 text-right">Sisa Pesan</th><th class="px-2 py-1 text-right">Harga SP</th></tr></thead><tbody>';

                        sp.details.forEach(spDetail => {
                            const sisaPesan = spDetail.qty_pesan - spDetail.qty_terima;
                            if (sisaPesan > 0) { // Hanya tampilkan obat yang masih ada sisa pesanan
                                html += `<tr class="hover:bg-gray-50">
                                    <td class="text-center px-2 py-1"><input type="checkbox" class="obat-checkbox-sp" data-obat='${JSON.stringify(spDetail.obat)}' data-sp-detail='${JSON.stringify(spDetail)}'></td>
                                    <td class="px-2 py-1">${spDetail.obat.kode}</td>
                                    <td class="px-2 py-1">${spDetail.obat.nama}</td>
                                    <td class="px-2 py-1 text-right">${spDetail.qty_pesan}</td>
                                    <td class="px-2 py-1 text-right">${spDetail.qty_terima}</td>
                                    <td class="px-2 py-1 text-right font-bold text-blue-600">${sisaPesan}</td>
                                    <td class="px-2 py-1 text-right">${formatRupiah(spDetail.harga_satuan)}</td>
                                </tr>`;
                            }
                        });

                        html += '</tbody></table>';
                        obatList.innerHTML = html;

                        document.querySelectorAll('.obat-checkbox-sp').forEach(cb => {
                            cb.addEventListener('change', function () {
                                const obat = JSON.parse(this.dataset.obat);
                                const spDetail = JSON.parse(this.dataset.spDetail);
                                if (this.checked) {
                                    tambahItem(obat, spDetail);
                                } else {
                                    const row = tableItems.querySelector(`tr[data-id='${obat.id}']`);
                                    if (row) row.remove();
                                    hitungTotal();
                                }
                            });
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        obatList.innerHTML = '<div class="text-red-500 text-sm">Gagal memuat data Surat Pesanan.</div>';
                    });
            });
            
            // Validasi formulir sebelum pengiriman
            form.addEventListener('submit', function(e) {
                const items = tableItems.querySelectorAll('tr');
                if (items.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 obat untuk pembelian!');
                    return false;
                }
            });

            // Initial calculation for existing items (if any)
            tableItems.querySelectorAll('tr').forEach(addEventListenersToRow);
            hitungTotal();
        });
    </script>
@endpush