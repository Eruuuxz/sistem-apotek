@extends('layouts.admin')

@section('title', 'Buat Surat Pesanan Baru')

@section('content')
    <div class="bg-white p-8 shadow-xl rounded-xl max-w-4xl mx-auto mt-6">
        <h2 class="text-2xl font-bold mb-6">Buat Surat Pesanan Baru</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('surat_pesanan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="no_sp" class="block text-sm font-medium text-gray-700 mb-1">Nomor SP</label>
                    <input type="text" name="no_sp" id="no_sp" value="{{ old('no_sp', $noSp) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-gray-100" readonly>
                </div>
                <div>
                    <label for="tanggal_sp" class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP</label>
                    <input type="datetime-local" name="tanggal_sp" id="tanggal_sp" value="{{ old('tanggal_sp', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="supplier_id" id="supplier_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="file_template" class="block text-sm font-medium text-gray-700 mb-1">Upload Template SP (PDF/DOC/DOCX)</label>
                    <input type="file" name="file_template" id="file_template"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>

            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('keterangan') }}</textarea>
            </div>

            <h3 class="text-lg font-semibold mb-3">Detail Obat Pesanan</h3>
            <div id="obat-details-container" class="space-y-4 mb-6">
                @if (old('obat_id'))
                    @foreach (old('obat_id') as $index => $obatId)
                        <div class="flex items-end space-x-2 obat-detail-row">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Obat</label>
                                <select name="obat_id[]" class="w-full border-gray-300 rounded-md shadow-sm obat-select">
                                    <option value="">Pilih Obat</option>
                                    @foreach ($obats as $obat)
                                        <option value="{{ $obat->id }}" data-harga="{{ $obat->harga_dasar }}" {{ $obatId == $obat->id ? 'selected' : '' }}>
                                            {{ $obat->nama }} ({{ $obat->kode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qty Pesan</label>
                                <input type="number" name="qty_pesan[]" value="{{ old('qty_pesan.'.$index) }}" min="1"
                                    class="w-24 border-gray-300 rounded-md shadow-sm qty-pesan-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                                <input type="number" name="harga_satuan[]" value="{{ old('harga_satuan.'.$index) }}" step="0.01" min="0"
                                    class="w-32 border-gray-300 rounded-md shadow-sm harga-satuan-input">
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-800 remove-obat-detail">Hapus</button>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-end space-x-2 obat-detail-row">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Obat</label>
                            <select name="obat_id[]" class="w-full border-gray-300 rounded-md shadow-sm obat-select">
                                <option value="">Pilih Obat</option>
                                @foreach ($obats as $obat)
                                    <option value="{{ $obat->id }}" data-harga="{{ $obat->harga_dasar }}">
                                        {{ $obat->nama }} ({{ $obat->kode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty Pesan</label>
                            <input type="number" name="qty_pesan[]" value="1" min="1"
                                class="w-24 border-gray-300 rounded-md shadow-sm qty-pesan-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                            <input type="number" name="harga_satuan[]" value="0" step="0.01" min="0"
                                class="w-32 border-gray-300 rounded-md shadow-sm harga-satuan-input">
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800 remove-obat-detail">Hapus</button>
                    </div>
                @endif
            </div>
            <button type="button" id="add-obat-detail" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Tambah Obat</button>

            <div class="flex justify-end space-x-4 mt-8">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">Simpan Surat Pesanan</button>
                <a href="{{ route('surat_pesanan.index') }}" class="bg-gray-400 text-white px-6 py-3 rounded-md hover:bg-gray-500">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const obatDetailsContainer = document.getElementById('obat-details-container');
        const addObatDetailButton = document.getElementById('add-obat-detail');
        const allObats = @json($obats); // Data obat dari controller

        function addObatDetailRow(obatId = '', qtyPesan = 1, hargaSatuan = 0) {
            const newRow = document.createElement('div');
            newRow.classList.add('flex', 'items-end', 'space-x-2', 'obat-detail-row');
            newRow.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Obat</label>
                    <select name="obat_id[]" class="w-full border-gray-300 rounded-md shadow-sm obat-select">
                        <option value="">Pilih Obat</option>
                        ${allObats.map(obat => `<option value="${obat.id}" data-harga="${obat.harga_dasar}" ${obatId == obat.id ? 'selected' : ''}>${obat.nama} (${obat.kode})</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qty Pesan</label>
                    <input type="number" name="qty_pesan[]" value="${qtyPesan}" min="1"
                        class="w-24 border-gray-300 rounded-md shadow-sm qty-pesan-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                    <input type="number" name="harga_satuan[]" value="${hargaSatuan}" step="0.01" min="0"
                        class="w-32 border-gray-300 rounded-md shadow-sm harga-satuan-input">
                </div>
                <button type="button" class="text-red-600 hover:text-red-800 remove-obat-detail">Hapus</button>
            `;
            obatDetailsContainer.appendChild(newRow);
            attachEventListeners(newRow);
        }

        function attachEventListeners(row) {
            row.querySelector('.remove-obat-detail').addEventListener('click', function() {
                row.remove();
            });

            row.querySelector('.obat-select').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const hargaDasar = selectedOption.dataset.harga || 0;
                row.querySelector('.harga-satuan-input').value = hargaDasar;
            });
        }

        // Attach event listeners to existing rows (for old input values)
        document.querySelectorAll('.obat-detail-row').forEach(row => {
            attachEventListeners(row);
        });

        addObatDetailButton.addEventListener('click', function() {
            addObatDetailRow();
        });
    });
</script>
@endpush