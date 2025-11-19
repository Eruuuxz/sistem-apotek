@extends('layouts.admin')

@section('title', 'Input Pembelian Baru')

@section('content')

    <div class="max-w-6xl mx-auto">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('pembelian.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
            </a>
        </div>

        <form id="purchase-form" action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Bagian Kiri: Informasi Faktur & Pilihan Obat --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Card 1: Info Utama --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Faktur</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">No Faktur Internal</label>
                                <input type="text" name="no_faktur" value="{{ $noFaktur }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-mono text-sm focus:outline-none" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No Faktur PBF</label>
                                <input type="text" name="no_faktur_pbf" value="{{ old('no_faktur_pbf') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-gray-300" placeholder="Contoh: INV-2023/001">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="datetime-local" name="tanggal" value="{{ old('tanggal', date('Y-m-d\TH:i:s')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                                <select name="supplier_id" id="supplier-select" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ambil dari SP (Opsional)</label>
                                <select name="surat_pesanan_id" id="surat-pesanan-select" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-sm">
                                    <option value="">-- Pilih Surat Pesanan --</option>
                                    @foreach($suratPesanans as $sp)
                                        <option value="{{ $sp->id }}" data-supplier-id="{{ $sp->supplier_id }}" {{ old('surat_pesanan_id') == $sp->id ? 'selected' : '' }}>
                                            {{ $sp->no_sp }} ({{ $sp->supplier->nama ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: List Obat --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex-1">
                        <h3 class="text-sm font-bold text-gray-800 mb-3 flex justify-between items-center">
                            <span>Pilih Item Obat</span>
                            <span class="text-xs font-normal text-gray-400 bg-gray-50 px-2 py-1 rounded">Centang untuk memilih</span>
                        </h3>
                        <div id="obat-list" class="border border-gray-200 rounded-lg bg-gray-50 h-64 overflow-y-auto p-2 space-y-1">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm text-center px-4">
                                <i data-feather="search" class="w-8 h-8 mb-2 opacity-50"></i>
                                <p>Pilih Supplier atau SP di atas untuk memuat daftar obat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Kanan: Tabel Item --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 min-h-[500px] flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                            <i data-feather="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                            Keranjang Pembelian
                        </h3>

                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-y border-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Nama Obat</th>
                                        <th class="px-4 py-3 font-semibold w-24">Jumlah</th>
                                        <th class="px-4 py-3 font-semibold w-32">Harga Satuan</th>
                                        <th class="px-4 py-3 font-semibold text-right">Subtotal</th>
                                        <th class="px-4 py-3 font-semibold text-center w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="table-items" class="divide-y divide-gray-100">
                                    {{-- JS akan mengisi ini --}}
                                    @if(old('items'))
                                        {{-- Logic old inputs here if needed --}}
                                    @endif
                                </tbody>
                            </table>
                            
                            {{-- Empty State Tabel --}}
                            <div id="empty-cart-msg" class="py-12 text-center text-gray-400">
                                <p>Belum ada item yang dipilih.</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                             <div class="text-right sm:text-left w-full">
                                <span class="text-sm text-gray-500 block">Total Estimasi</span>
                                <span id="total-harga" class="text-2xl font-bold text-gray-800">Rp 0</span>
                            </div>
                            <div class="flex gap-3 w-full sm:w-auto">
                                <a href="{{ route('pembelian.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 text-center transition-colors">
                                    Batal
                                </a>
                                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                                    Simpan Pembelian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();
            
            const supplierSelect = document.getElementById('supplier-select');
            const suratPesananSelect = document.getElementById('surat-pesanan-select');
            const obatList = document.getElementById('obat-list');
            const tableItems = document.getElementById('table-items');
            const emptyCartMsg = document.getElementById('empty-cart-msg');
            const totalHargaEl = document.getElementById('total-harga');
            const form = document.getElementById('purchase-form');

            let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0);
            }

            function toggleEmptyCart() {
                if (tableItems.children.length === 0) {
                    emptyCartMsg.classList.remove('hidden');
                } else {
                    emptyCartMsg.classList.add('hidden');
                }
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
                    let subtotal = parseFloat(row.querySelector('.subtotal').dataset.subtotal) || 0;
                    total += subtotal;
                });
                totalHargaEl.innerText = formatRupiah(total);
            }

            function addEventListenersToRow(row) {
                row.querySelector('.jumlah').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.harga').addEventListener('input', () => hitungSubtotal(row));
                row.querySelector('.remove-item').addEventListener('click', function() {
                    const id = row.dataset.id;
                    // Uncheck checkbox di list kiri
                    const checkbox = document.querySelector(`.obat-checkbox[data-id="${id}"]`);
                    if (checkbox) checkbox.checked = false;
                    
                    row.remove();
                    toggleEmptyCart();
                    hitungTotal();
                });
            }

            function tambahItem(obat, spData = null) {
                if ([...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id)) return;

                let hargaDasar = parseFloat(obat.harga_dasar) || 0;
                let qtyAwal = 1;
                let spDetailId = '';

                if (spData) {
                    hargaDasar = parseFloat(spData.harga_satuan) || 0;
                    qtyAwal = spData.qty_pesan - spData.qty_terima;
                    spDetailId = spData.id;
                }

                let row = document.createElement('tr');
                row.dataset.id = obat.id;
                row.className = 'bg-white hover:bg-gray-50 transition-colors group';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <span class="block font-medium text-gray-800">${obat.nama}</span>
                        <span class="text-xs text-gray-500">${obat.kode}</span>
                        <input type="hidden" name="items[${itemIndex}][obat_id]" value="${obat.id}">
                        ${spDetailId ? `<input type="hidden" name="items[${itemIndex}][sp_detail_id]" value="${spDetailId}">` : ''}
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${itemIndex}][jumlah]" value="${qtyAwal}" min="1" class="jumlah w-full px-2 py-1 text-sm border border-gray-200 rounded focus:ring-blue-500 focus:border-blue-500 text-center">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${itemIndex}][harga_beli]" value="${hargaDasar}" min="0" class="harga w-full px-2 py-1 text-sm border border-gray-200 rounded focus:ring-blue-500 focus:border-blue-500 text-right">
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-700 subtotal" data-subtotal="${hargaDasar * qtyAwal}">
                        ${formatRupiah(hargaDasar * qtyAwal)}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="remove-item p-1 text-gray-400 hover:text-red-500 transition-colors">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </td>
                `;
                
                tableItems.appendChild(row);
                feather.replace();
                addEventListenersToRow(row);
                toggleEmptyCart();
                hitungTotal();
                itemIndex++;
            }

            // Logic Fetch Obat (Sama seperti sebelumnya, hanya update styling HTML string)
            supplierSelect.addEventListener('change', function () {
                const supplierId = this.value;
                suratPesananSelect.value = '';
                obatList.innerHTML = '<div class="flex items-center justify-center h-full text-blue-500"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div></div>';
                
                if (!supplierId) {
                     obatList.innerHTML = '<div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm text-center px-4"><i data-feather="search" class="w-8 h-8 mb-2 opacity-50"></i><p>Pilih Supplier untuk memuat obat.</p></div>';
                     feather.replace();
                     return;
                }

                fetch(`/supplier/${supplierId}/obat`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            obatList.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Tidak ada obat terdaftar untuk supplier ini.</div>';
                            return;
                        }

                        let html = '';
                        data.forEach(obat => {
                            const isChecked = [...tableItems.querySelectorAll('tr')].some(tr => tr.dataset.id == obat.id) ? 'checked' : '';
                            html += `
                                <label class="flex items-center p-3 rounded hover:bg-blue-50 cursor-pointer transition-colors border border-transparent hover:border-blue-100 group">
                                    <input type="checkbox" class="obat-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" data-id="${obat.id}" data-obat='${JSON.stringify(obat)}' ${isChecked}>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700">${obat.nama}</p>
                                        <p class="text-xs text-gray-500">Stok: ${obat.stok} | Harga: ${formatRupiah(obat.harga_dasar)}</p>
                                    </div>
                                </label>
                            `;
                        });
                        obatList.innerHTML = html;

                        document.querySelectorAll('.obat-checkbox').forEach(cb => {
                            cb.addEventListener('change', function () {
                                const obat = JSON.parse(this.dataset.obat);
                                if (this.checked) {
                                    tambahItem(obat);
                                } else {
                                    const row = tableItems.querySelector(`tr[data-id='${obat.id}']`);
                                    if (row) {
                                        row.remove();
                                        toggleEmptyCart();
                                        hitungTotal();
                                    }
                                }
                            });
                        });
                    });
            });

             // Logic Fetch SP (Sama, sesuaikan HTML string listnya)
            suratPesananSelect.addEventListener('change', function () {
                const spId = this.value;
                if(this.options[this.selectedIndex].dataset.supplierId) {
                     supplierSelect.value = this.options[this.selectedIndex].dataset.supplierId;
                }
                obatList.innerHTML = '<div class="flex items-center justify-center h-full text-blue-500"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div></div>';

                if (!spId) return;

                fetch(`/surat_pesanan/${spId}/details`)
                    .then(res => res.json())
                    .then(sp => {
                         if (sp.details.length === 0) {
                            obatList.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">SP Kosong.</div>';
                            return;
                        }
                        let html = '';
                        sp.details.forEach(spDetail => {
                            const sisaPesan = spDetail.qty_pesan - spDetail.qty_terima;
                            if (sisaPesan > 0) {
                                 html += `
                                    <label class="flex items-center p-3 rounded hover:bg-blue-50 cursor-pointer transition-colors border border-transparent hover:border-blue-100 group">
                                        <input type="checkbox" class="obat-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" data-id="${spDetail.obat.id}" data-obat='${JSON.stringify(spDetail.obat)}' data-sp-detail='${JSON.stringify(spDetail)}'>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700">${spDetail.obat.nama}</p>
                                            <p class="text-xs text-gray-500">Sisa Pesan: <span class="font-bold text-blue-600">${sisaPesan}</span> | Harga SP: ${formatRupiah(spDetail.harga_satuan)}</p>
                                        </div>
                                    </label>
                                `;
                            }
                        });
                        obatList.innerHTML = html;
                        
                        document.querySelectorAll('.obat-checkbox').forEach(cb => {
                            cb.addEventListener('change', function () {
                                const obat = JSON.parse(this.dataset.obat);
                                const spDetail = JSON.parse(this.dataset.spDetail);
                                if (this.checked) {
                                    tambahItem(obat, spDetail);
                                } else {
                                    const row = tableItems.querySelector(`tr[data-id='${obat.id}']`);
                                    if (row) {
                                        row.remove();
                                        toggleEmptyCart();
                                        hitungTotal();
                                    }
                                }
                            });
                        });
                    });
            });

            form.addEventListener('submit', function(e) {
                if (tableItems.children.length === 0) {
                    e.preventDefault();
                    alert('Keranjang pembelian masih kosong!');
                }
            });
        });
    </script>
@endpush