@extends('layouts.admin')

@section('title', 'Buat Surat Pesanan Baru')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-5xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6">Buat Surat Pesanan Baru</h2>

    {{-- Flash & Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
            @if(session('sp_id'))
                <div class="mt-2">
                    <a href="{{ route('surat_pesanan.pdf', session('sp_id')) }}" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Cetak PDF</a>
                </div>
            @endif
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat_pesanan.store') }}" method="POST">
        @csrf
        
        {{-- Header SP --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SP</label>
                <input type="text" name="no_sp" value="{{ old('no_sp', $noSp) }}" readonly class="w-full p-2 rounded border bg-gray-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal SP</label>
                <input type="datetime-local" name="tanggal_sp" value="{{ old('tanggal_sp', now()->format('Y-m-d\TH:i')) }}" class="w-full p-2 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full p-2 rounded border">
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
                <select name="jenis_sp" id="jenis_sp" class="w-full p-2 rounded border">
                    <option value="reguler" {{ old('jenis_sp') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="prekursor" {{ old('jenis_sp') == 'prekursor' ? 'selected' : '' }}>Prekursor</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode Input Obat</label>
                <select name="sp_mode" id="sp_mode" class="w-full p-2 rounded border">
                    <option value="dropdown" {{ old('sp_mode') == 'dropdown' ? 'selected' : '' }}>Pilih Obat dari Daftar</option>
                    <option value="manual" {{ old('sp_mode') == 'manual' ? 'selected' : '' }}>Ketik Nama Obat Manual</option>
                    <option value="blank" {{ old('sp_mode') == 'blank' ? 'selected' : '' }}>Cetak Kosong</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" class="w-full p-2 rounded border" rows="3">{{ old('keterangan') }}</textarea>
        </div>

        {{-- Container untuk detail obat --}}
        <div id="obat-details-wrapper">
            <div id="obat-details-container" class="space-y-4 mb-4"></div>
            <div class="flex space-x-2 mb-6" id="add-buttons-container">
                <button type="button" id="add-obat-dropdown" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 hidden">Tambah Obat</button>
                <button type="button" id="add-obat-manual" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 hidden">Tambah Obat Manual</button>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Simpan SP</button>
            <a href="{{ route('surat_pesanan.index') }}" class="bg-gray-400 text-white px-6 py-3 rounded hover:bg-gray-500">Batal</a>
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
    let currentObats = [];

    function filterObats() {
        const jenis = jenisSpSelect.value;
        if (jenis === 'prekursor') {
            currentObats = allObats.filter(o => o.is_prekursor);
        } else {
            currentObats = allObats.filter(o => !o.is_prekursor);
        }
    }

    function updateFormVisibility() {
        container.innerHTML = ''; // Selalu bersihkan item saat mode berubah
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

    function generateObatOptions() {
        // Panggil filter setiap kali membuat opsi untuk memastikan datanya terbaru
        filterObats(); 
        
        if (currentObats.length === 0) {
            return '<option value="">Tidak ada obat untuk jenis SP ini</option>';
        }

        return [
            '<option value="">Pilih Obat</option>',
            ...currentObats.map(o => `<option value="${o.id}" data-harga="${o.harga_dasar}">${o.nama} (${o.kode})</option>`)
        ].join('');
    }

    function addObatDropdownRow() {
        const div = document.createElement('div');
        div.classList.add('flex', 'items-end', 'space-x-2', 'obat-detail-row', 'bg-gray-50', 'p-4', 'rounded-md');
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Obat</label>
                <select name="obat_id[]" class="w-full p-2 border rounded obat-dropdown">
                    ${generateObatOptions()}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="1" min="1" class="w-24 p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="harga_satuan[]" value="0" step="0.01" min="0" class="w-32 p-2 border rounded harga-satuan">
            </div>
            <button type="button" class="remove-obat-detail text-red-600 hover:text-red-800 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        `;
        container.appendChild(div);

        div.querySelector('.remove-obat-detail').addEventListener('click', () => {
            if(container.children.length > 1) div.remove();
            else alert('Minimal harus ada 1 item obat.');
        });

        div.querySelector('.obat-dropdown').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const hargaSatuanInput = div.querySelector('.harga-satuan');
            hargaSatuanInput.value = selectedOption.dataset.harga || '0';
        });
    }

    function addObatManualRow() {
        // Fungsi ini tidak berubah
        const div = document.createElement('div');
        div.classList.add('flex', 'items-end', 'space-x-2', 'obat-detail-row', 'bg-gray-50', 'p-4', 'rounded-md');
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Obat</label>
                <input type="text" name="obat_manual[]" class="w-full p-2 border rounded" placeholder="Ketik nama obat">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qty Pesan</label>
                <input type="number" name="qty_pesan[]" value="1" min="1" class="w-24 p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="harga_satuan[]" step="0.01" min="0" class="w-32 p-2 border rounded" placeholder="Opsional">
            </div>
            <button type="button" class="remove-obat-detail text-red-600 hover:text-red-800 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        `;
        container.appendChild(div);

        div.querySelector('.remove-obat-detail').addEventListener('click', () => {
            if(container.children.length > 1) div.remove();
            else alert('Minimal harus ada 1 item obat.');
        });
    }

    // Event Listeners
    jenisSpSelect.addEventListener('change', updateFormVisibility);
    spModeSelect.addEventListener('change', updateFormVisibility);
    btnDropdown.addEventListener('click', addObatDropdownRow);
    btnManual.addEventListener('click', addObatManualRow);
    
    // Inisialisasi form saat halaman dimuat
    updateFormVisibility();
});
</script>
@endpush