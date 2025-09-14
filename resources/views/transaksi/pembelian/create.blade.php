@extends('layouts.admin')

@section('title', 'Tambah Pembelian')

@section('content')

    <div class="bg-white shadow rounded p-6 space-y-6">

        <form action="{{ route('pembelian.store') }}" method="POST" class="space-y-6">
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
                            @if(old('obat_id'))
                                @foreach(old('obat_id') as $index => $obatId)
                                    @php
                                        $obat = \App\Models\Obat::find($obatId);
                                        $qty = old('jumlah.'.$index);
                                        $harga = old('harga.'.$index);
                                        $subtotal = $qty * $harga;
                                    @endphp
                                    <tr data-id="{{ $obatId }}" data-sp-qty-pesan="{{ old('sp_qty_pesan.'.$index) }}" data-sp-qty-terima="{{ old('sp_qty_terima.'.$index) }}" data-sp-harga-satuan="{{ old('sp_harga_satuan.'.$index) }}">
                                        <td class="px-2 py-1 border">{{ $obat->kode }} - {{ $obat->nama }}</td>
                                        <td class="px-2 py-1 border">
                                            <input type="number" name="jumlah[]" value="{{ $qty }}" min="1"
                                                   class="w-full jumlah px-2 py-1 border rounded">
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="number" name="harga[]" value="{{ $harga }}"
                                                   class="w-full harga px-2 py-1 border rounded">
                                        </td>
                                        <td class="px-2 py-1 border text-right subtotal" data-subtotal="{{ $subtotal }}">
                                            {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="text-red-500 font-bold remove-item">✖</button>
                                        </td>
                                        <input type="hidden" name="obat_id[]" value="{{ $obatId }}">
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

            let selectedObatFromSP = {}; // Menyimpan data obat dari SP yang dipilih

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

            function tambahItem(obat, spData = null) {
                if ([...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id)) {
                    alert('Obat ini sudah ada di daftar pembelian.');
                    return;
                }

                let row = document.createElement('tr');
                row.dataset.id = obat.id;
                row.classList.add('hover:bg-gray-50');

                let hargaDasar = parseFloat(obat.harga_dasar) || 0;
                let maxQty = obat.stok; // Default max qty dari stok obat

                // Jika dari SP, gunakan data SP
                if (spData) {
                    hargaDasar = parseFloat(spData.harga_satuan) || 0;
                    maxQty = spData.qty_pesan - spData.qty_terima; // Sisa yang bisa dibeli dari SP
                    row.dataset.spQtyPesan = spData.qty_pesan;
                    row.dataset.spQtyTerima = spData.qty_terima;
                    row.dataset.spHargaSatuan = spData.harga_satuan;
                }

                row.innerHTML = `
                    <td class="px-2 py-1 border">${obat.kode} - ${obat.nama}</td>
                    <td class="px-2 py-1 border">
                        <input type="number" name="jumlah[]" value="1" min="1" max="${maxQty}"
                               class="w-full jumlah px-2 py-1 border rounded">
                    </td>
                    <td class="px-2 py-1 border">
                        <input type="number" name="harga[]" value="${hargaDasar}" step="0.01" min="0"
                               class="w-full harga px-2 py-1 border rounded">
                    </td>
                    <td class="px-2 py-1 border text-right subtotal" data-subtotal="${hargaDasar}">
                        ${formatRupiah(hargaDasar)}
                    </td>
                    <td class="px-2 py-1 border text-center">
                        <button type="button" class="text-red-500 font-bold remove-item">✖</button>
                    </td>
                    <input type="hidden" name="obat_id[]" value="${obat.id}">
                    <input type="hidden" name="sp_qty_pesan[]" value="${spData ? spData.qty_pesan : ''}">
                    <input type="hidden" name="sp_qty_terima[]" value="${spData ? spData.qty_terima : ''}">
                    <input type="hidden" name="sp_harga_satuan[]" value="${spData ? spData.harga_satuan : ''}">
                `;
                tableItems.appendChild(row);

                row.querySelector('.jumlah').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.harga').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.remove-item').addEventListener('click', function() {
                    row.remove();
                    hitungTotal();
                });
                hitungTotal();
            }

            // Event listener untuk supplier select
            supplierSelect.addEventListener('change', function () {
                const supplierId = this.value;
                suratPesananSelect.value = ''; // Reset SP saat supplier berubah
                selectedObatFromSP = {}; // Clear SP data

                obatList.innerHTML = '<div class="text-gray-500 text-sm">Memuat...</div>';
                tableItems.innerHTML = ''; // Clear item pembelian

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
                                if (this.checked) tambahItem(obat);
                                else {
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
                const spSupplierId = selectedOption.dataset.supplierId;

                supplierSelect.value = spSupplierId; // Otomatis pilih supplier dari SP
                obatList.innerHTML = '<div class="text-gray-500 text-sm">Memuat...</div>';
                tableItems.innerHTML = ''; // Clear item pembelian
                selectedObatFromSP = {}; // Clear SP data

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
                                selectedObatFromSP[spDetail.obat_id] = spDetail; // Simpan data SP detail
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
                                if (this.checked) tambahItem(obat, spDetail);
                                else {
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

            // Initial calculation for old input values
            hitungTotal();
        });
    </script>
@endpush