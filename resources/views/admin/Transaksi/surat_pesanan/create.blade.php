@extends('layouts.admin')

@section('title', 'Buat Surat Pesanan Baru')

@section('content')
<div class="bg-white p-6 sm:p-8 shadow-lg rounded-xl max-w-4xl mx-auto my-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Surat Pesanan Baru</h2>
        <p class="text-sm text-gray-500">Lengkapi informasi di bawah ini untuk membuat SP.</p>
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
     @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('surat_pesanan.store') }}" method="POST">
        @csrf
        
        <div class="border border-gray-200 rounded-lg p-4 mb-6">
             <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Utama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SP</label>
                    <input type="text" name="no_sp" value="{{ old('no_sp', $noSp) }}" readonly class="w-full p-2 rounded-md border-gray-300 bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP</label>
                    <input type="datetime-local" name="tanggal_sp" value="{{ old('tanggal_sp', now()->format('Y-m-d\TH:i')) }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis SP</label>
                    <select name="jenis_sp" class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="reguler" {{ old('jenis_sp') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="prekursor" {{ old('jenis_sp') == 'prekursor' ? 'selected' : '' }}>Prekursor</option>
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
                        <option value="dropdown" {{ old('sp_mode', 'dropdown') == 'dropdown' ? 'selected' : '' }}>Pilih Obat dari Daftar</option>
                        <option value="manual" {{ old('sp_mode') == 'manual' ? 'selected' : '' }}>Ketik Nama Obat Manual</option>
                        <option value="blank" {{ old('sp_mode') == 'blank' ? 'selected' : '' }}>Cetak Kosong</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" class="w-full p-2 border border-gray-300 rounded-md shadow-sm" rows="3">{{ old('keterangan') }}</textarea>
                </div>
            </div>
            
            <div id="obat-details-wrapper" class="mt-4">
                <div id="obat-details-container" class="space-y-4 mb-4"></div>
                <div class="flex space-x-2" id="add-buttons-container">
                    <button type="button" id="add-obat-dropdown" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 hidden text-sm">Tambah Obat</button>
                    <button type="button" id="add-obat-manual" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 hidden text-sm">Tambah Obat Manual</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 mt-8 pt-4 border-t">
            <a href="{{ route('pembelian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-3 rounded-lg transition duration-300">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                 <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                Simpan SP
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const spModeSelect = document.getElementById('sp_mode');
    const container = document.getElementById('obat-details-container');
    const detailsWrapper = document.getElementById('obat-details-wrapper');
    const btnDropdown = document.getElementById('add-obat-dropdown');
    const btnManual = document.getElementById('add-obat-manual');

    let obatListCache = [];

    async function fetchObatBySupplier(supplierId) {
        if (!supplierId) {
            obatListCache = [];
            updateAllDropdowns();
            return;
        }
        try {
            const response = await fetch(`/surat-pesanan/get-obat-by-supplier/${supplierId}`);
            if (!response.ok) throw new Error('Network response was not ok.');
            obatListCache = await response.json();
            updateAllDropdowns();
        } catch (error) {
            console.error('Gagal mengambil data obat:', error);
            obatListCache = [];
            updateAllDropdowns();
        }
    }

    function generateObatOptions() {
        if (obatListCache.length === 0) return '<option value="">Pilih supplier atau obat tidak ditemukan</option>';
        return ['<option value="">Pilih Obat</option>', ...obatListCache.map(o => `<option value="${o.id}">${o.nama} (Stok: ${o.stok})</option>`)].join('');
    }

    function updateAllDropdowns() {
        container.querySelectorAll('.obat-dropdown').forEach(select => {
            const selectedValue = select.value;
            select.innerHTML = generateObatOptions();
            if (selectedValue) {
                select.value = selectedValue;
            }
        });
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
                addObatDropdownRow();
            } else if (mode === 'manual') {
                btnManual.classList.remove('hidden');
                addObatManualRow();
            }
        }
    }

    function addObatDropdownRow() {
        const div = document.createElement('div');
        // Layout grid disesuaikan menjadi 3 kolom
        div.className = 'grid grid-cols-1 md:grid-cols-[1fr,auto,auto] gap-3 items-end bg-gray-50 p-4 rounded-md';
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Obat</label>
                <select name="obat_id[]" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm obat-dropdown">${generateObatOptions()}</select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="1" min="1" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="button" class="remove-obat-detail text-red-500 hover:text-red-700 p-2 justify-self-end">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        `;
        container.appendChild(div);
    }
    
    function addObatManualRow() {
        const div = document.createElement('div');
        // Layout grid disesuaikan menjadi 3 kolom
        div.className = 'grid grid-cols-1 md:grid-cols-[1fr,auto,auto] gap-3 items-end bg-gray-50 p-4 rounded-md';
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Obat</label>
                <input type="text" name="obat_manual[]" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Ketik nama obat">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="1" min="1" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="button" class="remove-obat-detail text-red-500 hover:text-red-700 p-2 justify-self-end">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        `;
        container.appendChild(div);
    }

    // Event Listeners
    supplierSelect.addEventListener('change', function() {
        fetchObatBySupplier(this.value);
    });
    
    spModeSelect.addEventListener('change', updateFormVisibility);
    
    btnDropdown.addEventListener('click', addObatDropdownRow);
    btnManual.addEventListener('click', addObatManualRow);
    
    container.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-obat-detail');
        if(removeBtn) {
            if(container.children.length > 1) {
                removeBtn.parentElement.remove();
            } else {
                alert('Minimal harus ada 1 item obat.');
            }
        }
    });
    
    // Inisialisasi awal saat halaman dimuat
    if (supplierSelect.value) {
        fetchObatBySupplier(supplierSelect.value).then(() => {
            updateFormVisibility();
        });
    } else {
        updateFormVisibility();
    }
});
</script>
@endpush