@extends('layouts.admin')

@section('title', 'Edit Surat Pesanan')

@section('content')
<div class="bg-white p-6 sm:p-8 shadow-lg rounded-xl max-w-4xl mx-auto my-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Surat Pesanan</h2>
        <p class="text-sm text-gray-500">Perbarui informasi SP dengan nomor: <span class="font-medium text-gray-700">{{ $suratPesanan->no_sp }}</span></p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat_pesanan.update', $suratPesanan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Utama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SP</label>
                    <input type="text" name="no_sp" value="{{ old('no_sp', $suratPesanan->no_sp) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP</label>
                    <input type="datetime-local" name="tanggal_sp" value="{{ old('tanggal_sp', $suratPesanan->tanggal_sp->format('Y-m-d\TH:i')) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $suratPesanan->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis SP</label>
                    <select name="jenis_sp" id="jenis_sp" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="reguler" {{ old('jenis_sp', $suratPesanan->jenis_sp) == 'reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="prekursor" {{ old('jenis_sp', $suratPesanan->jenis_sp) == 'prekursor' ? 'selected' : '' }}>Prekursor</option>
                    </select>
                </div>
                 <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="pending" {{ old('status', $suratPesanan->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="selesai" {{ old('status', $suratPesanan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status', $suratPesanan->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="border border-gray-200 rounded-lg p-4 mb-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Mode Input & Detail Obat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode Input Obat</label>
                    <select name="sp_mode" id="sp_mode" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="dropdown" {{ old('sp_mode', $suratPesanan->sp_mode) == 'dropdown' ? 'selected' : '' }}>Pilih Obat dari Daftar</option>
                        <option value="manual" {{ old('sp_mode', $suratPesanan->sp_mode) == 'manual' ? 'selected' : '' }}>Ketik Nama Obat Manual</option>
                        <option value="blank" {{ old('sp_mode', $suratPesanan->sp_mode) == 'blank' ? 'selected' : '' }}>Cetak Kosong</option>
                    </select>
                </div>
                 <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" rows="3">{{ old('keterangan', $suratPesanan->keterangan) }}</textarea>
                </div>
            </div>
            
            <div id="obat-details-wrapper" class="mt-4">
                <div id="obat-details-container" class="space-y-4 mb-4">
                    <!-- Items will be loaded by JS -->
                </div>
                <div class="flex space-x-2" id="add-buttons-container">
                    <button type="button" id="add-obat-dropdown" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 hidden text-sm">Tambah Obat</button>
                    <button type="button" id="add-obat-manual" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 hidden text-sm">Tambah Obat Manual</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8 pt-4 border-t">
            <a href="{{ route('pembelian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-3 rounded-lg transition duration-300">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                 <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 11.667 0l3.181-3.183m-4.991-2.691v4.992" /></svg>
                Perbarui SP
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSpSelect = document.getElementById('jenis_sp');
    const spModeSelect = document.getElementById('sp_mode');
    const container = document.getElementById('obat-details-container');
    const detailsWrapper = document.getElementById('obat-details-wrapper');
    const btnDropdown = document.getElementById('add-obat-dropdown');
    const btnManual = document.getElementById('add-obat-manual');

    const allObats = @json($obats);
    const existingDetails = @json($suratPesanan->details ?? []);
    let currentObats = [];

    function filterObats() {
        const jenis = jenisSpSelect.value;
        currentObats = (jenis === 'prekursor') ? allObats.filter(o => o.is_prekursor) : allObats.filter(o => !o.is_prekursor);
    }

    function generateObatOptions(selectedId = null) {
        filterObats();
        if (currentObats.length === 0) return '<option value="">Tidak ada obat untuk jenis SP ini</option>';
        return ['<option value="">Pilih Obat</option>', ...currentObats.map(o => `<option value="${o.id}" data-harga="${o.harga_dasar}" ${selectedId == o.id ? 'selected' : ''}>${o.nama} (${o.kode})</option>`)].join('');
    }

    function addObatDropdownRow(detail = {}) {
        const div = document.createElement('div');
        div.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'lg:grid-cols-[1fr,auto,auto,auto]', 'gap-3', 'items-end', 'bg-gray-50', 'p-4', 'rounded-md');
        div.innerHTML = `
            <div class="flex-1 md:col-span-3 lg:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Obat</label>
                <select name="obat_id[]" class="w-full p-2 border border-gray-300 rounded-md shadow-sm obat-dropdown">${generateObatOptions(detail.obat_id)}</select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="${detail.qty_pesan || 1}" min="1" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="harga_satuan[]" value="${detail.harga_satuan || 0}" step="0.01" min="0" class="w-full p-2 border border-gray-300 rounded-md shadow-sm harga-satuan">
            </div>
            <button type="button" class="remove-obat-detail text-red-500 hover:text-red-700 p-2 justify-self-end"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
        `;
        container.appendChild(div);
        div.querySelector('.remove-obat-detail').addEventListener('click', () => { if(container.children.length > 1) div.remove(); });
        div.querySelector('.obat-dropdown').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            div.querySelector('.harga-satuan').value = selectedOption.dataset.harga || '0';
        });
    }

    function addObatManualRow(detail = {}) {
        const div = document.createElement('div');
        div.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'lg:grid-cols-[1fr,auto,auto,auto]', 'gap-3', 'items-end', 'bg-gray-50', 'p-4', 'rounded-md');
        div.innerHTML = `
            <div class="flex-1 md:col-span-3 lg:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Obat</label>
                <input type="text" name="obat_manual[]" value="${detail.nama_manual || ''}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Ketik nama obat">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="${detail.qty_pesan || 1}" min="1" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="harga_satuan[]" value="${detail.harga_satuan || 0}" step="0.01" min="0" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
            </div>
            <button type="button" class="remove-obat-detail text-red-500 hover:text-red-700 p-2 justify-self-end"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
        `;
        container.appendChild(div);
        div.querySelector('.remove-obat-detail').addEventListener('click', () => { if(container.children.length > 1) div.remove(); });
    }
    
    function updateFormVisibility() {
        container.innerHTML = '';
        btnDropdown.classList.add('hidden');
        btnManual.classList.add('hidden');
        const mode = spModeSelect.value;

        if (mode === 'blank') {
            detailsWrapper.classList.add('hidden');
        } else {
            detailsWrapper.classList.remove('hidden');
            if (mode === 'dropdown') {
                btnDropdown.classList.remove('hidden');
                existingDetails.forEach(detail => addObatDropdownRow(detail));
                if (existingDetails.length === 0) addObatDropdownRow();
            } else if (mode === 'manual') {
                btnManual.classList.remove('hidden');
                existingDetails.forEach(detail => addObatManualRow(detail));
                if (existingDetails.length === 0) addObatManualRow();
            }
        }
    }

    jenisSpSelect.addEventListener('change', updateFormVisibility);
    spModeSelect.addEventListener('change', updateFormVisibility);
    btnDropdown.addEventListener('click', () => addObatDropdownRow());
    btnManual.addEventListener('click', () => addObatManualRow());
    
    updateFormVisibility();
});
</script>
@endpush
