@extends('layouts.admin')

@section('title', 'Tambah Biaya Operasional Massal')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Tambah Biaya Operasional</h1>
    <div class="bg-white p-6 rounded-lg shadow-md w-full mx-auto">
        <form action="{{ route('biaya-operasional.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="mt-1 block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('tanggal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <hr class="my-6">

            <div id="biaya-container" class="space-y-4">
                {{-- Baris dinamis akan ditambahkan di sini --}}
            </div>
            
            <div class="mt-4">
                <button type="button" id="add-biaya-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    + Tambah Rincian
                </button>
            </div>

            <div class="mt-8 border-t pt-6 flex justify-end gap-3">
                <a href="{{ route('biaya-operasional.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Simpan Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('biaya-container');
    const addButton = document.getElementById('add-biaya-btn');
    let itemIndex = 0;

    const jenisBiayaOptions = ['Gaji', 'Listrik', 'Sewa', 'Air', 'Internet', 'Perawatan', 'Promosi', 'Lain-lain'];

    const formatRupiah = (number) => {
        if (number === null || typeof number === 'undefined' || number === '') return '';
        return new Intl.NumberFormat('id-ID').format(number);
    };

    const parseRupiah = (string) => {
        return string.replace(/[^0-9]/g, '');
    };

    container.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('jumlah-display')) {
            const displayInput = e.target;
            const hiddenInput = displayInput.nextElementSibling;
            const rawValue = parseRupiah(displayInput.value);
            if (hiddenInput) hiddenInput.value = rawValue;
            displayInput.value = formatRupiah(rawValue);
        }
    });

    function createBiayaItem() {
        const index = itemIndex++;
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('biaya-item', 'p-4', 'border', 'rounded-md', 'grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4', 'items-end');

        // Jenis Biaya
        let jenisBiayaHtml = `<div class="w-full"><label class="block text-sm font-medium">Jenis Biaya</label><select name="biaya[${index}][jenis_biaya]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">`;
        jenisBiayaHtml += '<option value="">Pilih Jenis Biaya</option>';
        jenisBiayaOptions.forEach(opt => {
            jenisBiayaHtml += `<option value="${opt}">${opt}</option>`;
        });
        jenisBiayaHtml += `</select></div>`;

        // Keterangan
        const keteranganHtml = `<div class="w-full"><label class="block text-sm font-medium">Keterangan</label><input type="text" name="biaya[${index}][keterangan]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Gaji bulan ini"></div>`;
        
        // Jumlah
        const jumlahHtml = `<div class="w-full"><label class="block text-sm font-medium">Jumlah (Rp)</label><input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm jumlah-display" placeholder="500000" required><input type="hidden" name="biaya[${index}][jumlah]" class="jumlah-hidden"></div>`;

        // Tombol Hapus
        const hapusHtml = `<div class="w-full text-right md:text-left"><button type="button" class="remove-biaya-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button></div>`;

        itemDiv.innerHTML = jenisBiayaHtml + keteranganHtml + jumlahHtml + hapusHtml;
        container.appendChild(itemDiv);

        itemDiv.querySelector('.remove-biaya-btn').addEventListener('click', function() {
            itemDiv.remove();
        });
    }

    addButton.addEventListener('click', createBiayaItem);
    createBiayaItem();
});
</script>
@endsection