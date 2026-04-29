@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')

    <div class="bg-white shadow rounded p-6 space-y-6">

        <form id="purchase-form" action="{{ route('pembelian.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Informasi Faktur --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">No Faktur Internal</label>
                    <input type="text" name="no_faktur" value="{{ $noFaktur }}" class="border border-gray-300 rounded-lg w-full px-3 py-2 bg-gray-100 text-gray-600 focus:outline-none" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">No Faktur PBF</label>
                    <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf') }}" class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="Nomor faktur dari PBF">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Tanggal</label>
                    <input type="datetime-local" name="tanggal" value="{{ old('tanggal', date('Y-m-d\TH:i:s')) }}" class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Supplier</label>
                    <select name="supplier_id" id="supplier-select" class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Surat Pesanan (Opsional)</label>
                    <select name="surat_pesanan_id" id="surat-pesanan-select" class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
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

            function showToast(message, type = 'success') {
                const color = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
                const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : 'info');
                const toast = document.createElement('div');
                toast.className = `fixed top-5 right-5 ${color} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 z-[9999] opacity-0 transition-all duration-300 transform translate-y-[-20px]`;
                toast.innerHTML = `<i data-feather="${icon}" class="w-5 h-5"></i><span class="text-sm font-medium">${message}</span>`;
                document.body.appendChild(toast);
                if (typeof feather !== 'undefined') feather.replace();
                
                setTimeout(() => toast.classList.remove('opacity-0', 'translate-y-[-20px]'), 10);
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-[-20px]');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            function tambahItem(obat, spData = null) {
                if ([...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id)) {
                    showToast('Obat ini sudah ada di daftar pembelian.', 'error');
                    return;
                }

                let row = document.createElement('tr');
                row.dataset.id = obat.id;
                row.dataset.index = itemIndex; // Set the unique index
                row.classList.add('hover:bg-blue-50', 'transition-all', 'duration-300', 'opacity-0');

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
                    <td class="px-3 py-2 border-b border-gray-200 text-gray-800 font-medium">${obat.kode} - ${obat.nama}</td>
                    <td class="px-3 py-2 border-b border-gray-200">
                        <input type="number" name="items[${itemIndex}][jumlah]" value="${qtyAwal}" min="1"
                               class="w-full jumlah px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-center">
                    </td>
                    <td class="px-3 py-2 border-b border-gray-200">
                        <input type="number" name="items[${itemIndex}][harga_beli]" value="${hargaDasar}" step="0.01" min="0"
                               class="w-full harga px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-right">
                    </td>
                    <td class="px-3 py-2 border-b border-gray-200 text-right subtotal font-semibold text-green-600" data-subtotal="${hargaDasar * qtyAwal}">
                        ${formatRupiah(hargaDasar * qtyAwal)}
                    </td>
                    <td class="px-3 py-2 border-b border-gray-200 text-center">
                        <button type="button" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-full transition remove-item"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                    </td>
                    <input type="hidden" name="items[${itemIndex}][obat_id]" value="${obat.id}">
                    ${spDetailId ? `<input type="hidden" name="items[${itemIndex}][sp_detail_id]" value="${spDetailId}">` : ''}
                `;
                tableItems.appendChild(row);
                if (typeof feather !== 'undefined') feather.replace();

                // Animasi masuk
                setTimeout(() => row.classList.remove('opacity-0'), 10);

                addEventListenersToRow(row);
                itemIndex++;
                hitungTotal();
                showToast(`Menambahkan ${obat.nama}`, 'success');
            }

            const spinnerHtml = `<div class="flex items-center justify-center p-8 space-x-3 text-gray-500">
                    <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-medium">Memuat data obat...</span>
                </div>`;

            // Event listener untuk supplier select
            supplierSelect.addEventListener('change', function () {
                const supplierId = this.value;
                suratPesananSelect.value = ''; // Reset SP saat supplier berubah
                obatList.innerHTML = spinnerHtml;
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

                        let html = '<table class="w-full border-collapse table-auto text-sm">';
                        html += '<thead class="bg-gray-50 text-gray-600 border-b border-gray-200"><tr><th class="px-3 py-2 text-center w-10"></th><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Stok Apotek</th><th class="px-3 py-2 text-right">Harga Dasar</th></tr></thead><tbody class="divide-y divide-gray-100">';

                        data.forEach(obat => {
                            html += `<tr class="hover:bg-blue-50 transition-colors">
                                <td class="text-center px-3 py-2"><input type="checkbox" class="obat-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" data-obat='${JSON.stringify(obat)}'></td>
                                <td class="px-3 py-2 text-gray-500">${obat.kode}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">${obat.nama}</td>
                                <td class="px-3 py-2 text-right font-mono">${obat.stok}</td>
                                <td class="px-3 py-2 text-right text-gray-600">${formatRupiah(obat.harga_dasar)}</td>
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
                if (selectedOption && spId) {
                    const spSupplierId = selectedOption.dataset.supplierId;
                    supplierSelect.value = spSupplierId; // Otomatis pilih supplier dari SP
                }
                
                obatList.innerHTML = spinnerHtml;
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

                        let html = '<table class="w-full border-collapse table-auto text-sm">';
                        html += '<thead class="bg-gray-50 text-gray-600 border-b border-gray-200"><tr><th class="px-3 py-2 text-center w-10"></th><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Qty Pesan</th><th class="px-3 py-2 text-right">Qty Terima</th><th class="px-3 py-2 text-right">Sisa Pesan</th><th class="px-3 py-2 text-right">Harga SP</th></tr></thead><tbody class="divide-y divide-gray-100">';

                        sp.details.forEach(spDetail => {
                            const sisaPesan = spDetail.qty_pesan - spDetail.qty_terima;
                            if (sisaPesan > 0) { // Hanya tampilkan obat yang masih ada sisa pesanan
                                html += `<tr class="hover:bg-blue-50 transition-colors">
                                    <td class="text-center px-3 py-2"><input type="checkbox" class="obat-checkbox-sp w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" data-obat='${JSON.stringify(spDetail.obat)}' data-sp-detail='${JSON.stringify(spDetail)}'></td>
                                    <td class="px-3 py-2 text-gray-500">${spDetail.obat.kode}</td>
                                    <td class="px-3 py-2 font-medium text-gray-800">${spDetail.obat.nama}</td>
                                    <td class="px-3 py-2 text-right font-mono">${spDetail.qty_pesan}</td>
                                    <td class="px-3 py-2 text-right font-mono">${spDetail.qty_terima}</td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-blue-600">${sisaPesan}</td>
                                    <td class="px-3 py-2 text-right text-gray-600">${formatRupiah(spDetail.harga_satuan)}</td>
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
                    showToast('Pilih minimal 1 obat untuk pembelian!', 'error');
                    return false;
                }
            });

            // Initial calculation for existing items (if any)
            tableItems.querySelectorAll('tr').forEach(addEventListenersToRow);
            hitungTotal();
        });
    </script>
@endpush