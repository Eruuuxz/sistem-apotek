@extends('layouts.admin')

@section('title', 'Tambah Retur')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4">

        <form action="{{ route('retur.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            {{-- Informasi Retur --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">No Retur</label>
                    <input type="text" name="no_retur" value="{{ $noRetur }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Jenis Retur</label>
                    <select name="jenis" id="jenis_retur" class="w-full border rounded px-3 py-2">
                        <option value="">-- Pilih Jenis Retur --</option>
                        <option value="pembelian">Retur Pembelian (ke Supplier)</option>
                        <option value="penjualan">Retur Penjualan (dari Customer)</option>
                    </select>
                </div>
            </div>

            {{-- Pilih Transaksi --}}
            <div id="transaksi_pembelian_div" class="space-y-2 hidden">
                <label class="block font-medium text-gray-700">Pilih Transaksi Pembelian</label>
                <select name="transaksi_pembelian_id" id="transaksi_pembelian_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Faktur Pembelian --</option>
                    @foreach($pembelian as $p)
                        <option value="{{ $p->id }}">FPB-{{ $p->no_faktur }}
                            ({{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }})</option>
                    @endforeach
                </select>
            </div>

            <div id="transaksi_penjualan_div" class="space-y-2 hidden">
                <label class="block font-medium text-gray-700">Pilih Transaksi Penjualan</label>
                <select name="transaksi_penjualan_id" id="transaksi_penjualan_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Nota Penjualan --</option>
                    @foreach($penjualan as $pj)
                        <option value="{{ $pj->id }}">PJ-{{ $pj->no_nota }}
                            ({{ \Carbon\Carbon::parse($pj->tanggal)->format('Y-m-d') }})</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="transaksi_id" id="hidden_transaksi_id">

            {{-- Tabel Item Retur --}}
            <div>
                <label class="block font-medium text-gray-700 mb-2">Daftar Item Retur</label>
                <div class="overflow-x-auto border rounded bg-gray-50">
                    <table class="w-full table-auto border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Kode</th>
                                <th class="border px-2 py-1">Nama Item</th>
                                <th class="border px-2 py-1">Harga Satuan</th>
                                <th class="border px-2 py-1">Jumlah Retur</th>
                                <th class="border px-2 py-1">Max Qty</th>
                                <th class="border px-2 py-1 text-right">Subtotal</th>
                                <th class="border px-2 py-1 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-items">
                            {{-- Rows akan ditambahkan JS --}}
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add_item_btn"
                    class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition hidden">+ Tambah
                    Item</button>
            </div>

            {{-- Total --}}
            <div class="text-right text-lg font-bold">
                Total Retur: <span id="total-harga" class="text-blue-600">0</span>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="keterangan" class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>

            {{-- Aksi --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Simpan
                    Retur</button>
                <a href="{{ route('retur.index') }}"
                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jenisReturSelect = document.getElementById('jenis_retur');
            const transaksiPembelianDiv = document.getElementById('transaksi_pembelian_div');
            const transaksiPenjualanDiv = document.getElementById('transaksi_penjualan_div');
            const transaksiPembelianIdSelect = document.getElementById('transaksi_pembelian_id');
            const transaksiPenjualanIdSelect = document.getElementById('transaksi_penjualan_id');
            const hiddenTransaksiIdInput = document.getElementById('hidden_transaksi_id');
            const tableItemsBody = document.getElementById('table-items');
            const addItemBtn = document.getElementById('add_item_btn');
            const totalHargaSpan = document.getElementById('total-harga');

            let availableItems = [];

            function toggleTransaksiSelect() {
                const jenis = jenisReturSelect.value;
                transaksiPembelianDiv.style.display = 'none';
                transaksiPenjualanDiv.style.display = 'none';
                transaksiPembelianIdSelect.value = '';
                transaksiPenjualanIdSelect.value = '';
                hiddenTransaksiIdInput.value = '';
                tableItemsBody.innerHTML = '';
                addItemBtn.style.display = 'none';
                availableItems = [];
                hitungTotal();

                if (jenis === 'pembelian') transaksiPembelianDiv.style.display = 'block';
                else if (jenis === 'penjualan') transaksiPenjualanDiv.style.display = 'block';
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
                    const res = await fetch(`/retur/sumber/${jenis}/${id}`);
                    const data = await res.json();
                    availableItems = data.items;
                    renderAvailableItems();
                    addItemBtn.style.display = 'block';
                } catch (err) {
                    console.error(err);
                    alert('Gagal memuat data transaksi.');
                    tableItemsBody.innerHTML = '';
                    addItemBtn.style.display = 'none';
                    availableItems = [];
                    hitungTotal();
                }
            }

            function renderAvailableItems() {
                tableItemsBody.innerHTML = '';
                availableItems.forEach(item => addRow(item));
                hitungTotal();
            }

            function addRow(item) {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                row.innerHTML = `
                <td class="border px-2 py-1">
                    <input type="hidden" name="item_id[]" value="${item.id}">
                    <span class="item-kode">${item.kode}</span>
                </td>
                <td class="border px-2 py-1">
                    <span class="item-nama">${item.nama}</span>
                </td>
                <td class="border px-2 py-1">
                    <input type="number" name="harga[]" class="w-full px-2 py-1 border rounded harga" value="${item.harga}" readonly>
                </td>
                <td class="border px-2 py-1">
                    <input type="number" name="qty[]" class="w-full px-2 py-1 border rounded qty" value="1" min="1" max="${item.max_qty}">
                </td>
                <td class="border px-2 py-1 text-center max-qty">${item.max_qty}</td>
                <td class="border px-2 py-1 text-right subtotal">0</td>
                <td class="border px-2 py-1 text-center">
                    <button type="button" class="text-red-500" onclick="hapusRow(this)">✖</button>
                </td>
            `;
                tableItemsBody.appendChild(row);
                hitungSubtotal(row);

                row.querySelector('.qty').addEventListener('input', () => hitungSubtotal(row));
            }

            function hitungSubtotal(row) {
                const qty = Math.min(Math.max(parseInt(row.querySelector('.qty').value) || 0, 1), parseInt(row.querySelector('.max-qty').innerText));
                row.querySelector('.qty').value = qty;
                const harga = parseFloat(row.querySelector('.harga').value) || 0;
                row.querySelector('.subtotal').innerText = (qty * harga).toLocaleString('id-ID');
                hitungTotal();
            }

            function hitungTotal() {
                let total = 0;
                tableItemsBody.querySelectorAll('tr').forEach(row => {
                    total += (parseInt(row.querySelector('.qty').value) || 0) * (parseFloat(row.querySelector('.harga').value) || 0);
                });
                totalHargaSpan.innerText = total.toLocaleString('id-ID');
            }

            jenisReturSelect.addEventListener('change', toggleTransaksiSelect);

            transaksiPembelianIdSelect.addEventListener('change', function () {
                hiddenTransaksiIdInput.value = this.value;
                fetchTransaksiItems('pembelian', this.value);
            });
            transaksiPenjualanIdSelect.addEventListener('change', function () {
                hiddenTransaksiIdInput.value = this.value;
                fetchTransaksiItems('penjualan', this.value);
            });

            addItemBtn.addEventListener('click', function () {
                alert('Item akan otomatis dimuat dari transaksi yang dipilih.');
            });

            window.hapusRow = function (btn) {
                btn.closest('tr').remove();
                hitungTotal();
            };

            toggleTransaksiSelect();
        });
    </script>
@endpush