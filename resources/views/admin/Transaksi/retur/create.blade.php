@extends('layouts.admin')

@section('title', 'Tambah Retur Baru')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4" x-data="returForm()">
        <div class="bg-white p-8 shadow-xl rounded-xl">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Formulir Retur Barang</h2>
                <p class="text-sm text-gray-500">Pilih jenis retur dan ikuti langkah-langkah selanjutnya.</p>
            </div>

            <form action="{{ route('retur.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Step 1: Informasi Dasar --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Langkah 1: Informasi Dasar Retur</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1 text-sm">No Retur</label>
                            <input type="text" name="no_retur" value="{{ $noRetur }}" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1 text-sm">Tanggal</label>
                            <input type="datetime-local" name="tanggal" value="{{ date('Y-m-d\TH:i') }}" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1 text-sm">Jenis Retur</label>
                            <select name="jenis" x-model="jenisRetur" class="w-full border rounded-lg px-3 py-2">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pembelian">Retur Pembelian (ke Supplier)</option>
                                <option value="penjualan">Retur Penjualan (dari Customer)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Pilih Transaksi --}}
                <div class="border-t pt-6" x-show="jenisRetur" x-transition>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Langkah 2: Pilih Transaksi Sumber</h3>
                    <div x-show="jenisRetur === 'pembelian'">
                        <label class="block font-medium text-gray-700 mb-1">Pilih Faktur Pembelian</label>
                        <select name="transaksi_pembelian_id" @change="fetchItems('pembelian', $event.target.value)" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Pilih Faktur --</option>
                            @foreach($pembelian as $p)
                                <option value="{{ $p->id }}">{{ $p->no_faktur_pbf ?? $p->no_faktur }} ({{ $p->supplier->nama }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="jenisRetur === 'penjualan'">
                        <label class="block font-medium text-gray-700 mb-1">Pilih Nota Penjualan</label>
                        <select name="transaksi_penjualan_id" @change="fetchItems('penjualan', $event.target.value)" class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Pilih Nota --</option>
                            @foreach($penjualan as $pj)
                                <option value="{{ $pj->id }}">{{ $pj->no_transaksi }} ({{ $pj->pelanggan->nama ?? 'Umum' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="transaksi_id" x-model="transaksiId">
                </div>

                {{-- Step 3: Item Retur --}}
                <div class="border-t pt-6" x-show="items.length > 0" x-transition>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Langkah 3: Pilih Item yang Diretur</h3>
                    <div class="overflow-x-auto border rounded-lg bg-gray-50">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-2 py-2 text-left">Nama Item</th>
                                    <th class="px-2 py-2 text-right">Harga Satuan</th>
                                    <th class="px-2 py-2 text-center">Qty Retur</th>
                                    <th class="px-2 py-2 text-center">Qty Maksimal</th>
                                    <th class="px-2 py-2 text-right">Subtotal</th>
                                    <th class="px-2 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-2 py-2">
                                            <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id">
                                            <input type="hidden" :name="'items[' + index + '][harga]'" :value="item.harga">
                                            <span x-text="item.nama"></span>
                                        </td>
                                        <td class="px-2 py-2 text-right" x-text="formatRupiah(item.harga)"></td>
                                        <td class="px-2 py-2 text-center">
                                            <input type="number" :name="'items[' + index + '][qty]'" x-model.number="item.qty" @input="calculateSubtotal(index)" class="w-20 px-2 py-1 border rounded" min="1" :max="item.max_qty">
                                        </td>
                                        <td class="px-2 py-2 text-center" x-text="item.max_qty"></td>
                                        <td class="px-2 py-2 text-right font-semibold" x-text="formatRupiah(item.subtotal)"></td>
                                        <td class="px-2 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times; Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                     <div class="text-right text-lg font-bold mt-4">
                        Total Retur: <span x-text="formatRupiah(total)" class="text-blue-600"></span>
                    </div>
                </div>

                {{-- Keterangan --}}
                 <div class="border-t pt-6">
                    <label class="block font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" class="w-full border rounded-lg px-3 py-2" rows="3"></textarea>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('retur.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Simpan Retur</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function returForm() {
            return {
                jenisRetur: '',
                transaksiId: '',
                items: [],
                total: 0,
                async fetchItems(jenis, id) {
                    if (!jenis || !id) {
                        this.items = [];
                        this.transaksiId = '';
                        this.calculateTotal();
                        return;
                    }
                    this.transaksiId = id;
                    try {
                        const response = await fetch(`/retur/sumber/${jenis}/${id}`);
                        const data = await response.json();
                        this.items = data.items.map(item => ({...item, qty: 1, subtotal: item.harga }));
                        this.calculateTotal();
                    } catch (error) {
                        console.error('Error fetching items:', error);
                        alert('Gagal memuat item dari transaksi.');
                        this.items = [];
                        this.calculateTotal();
                    }
                },
                calculateSubtotal(index) {
                    const item = this.items[index];
                    if (item.qty > item.max_qty) item.qty = item.max_qty;
                    if (item.qty < 1) item.qty = 1;
                    item.subtotal = item.qty * item.harga;
                    this.calculateTotal();
                },
                calculateTotal() {
                    this.total = this.items.reduce((acc, item) => acc + item.subtotal, 0);
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },
                formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
                }
            }
        }
    </script>
@endpush