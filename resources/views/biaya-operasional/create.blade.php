@extends('layouts.admin')

@section('title', 'Tambah Biaya Operasional')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-4xl mx-auto mt-6">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Formulir Biaya Operasional</h2>
        <p class="text-sm text-gray-500">Catat satu atau beberapa biaya operasional sekaligus dalam satu tanggal.</p>
    </div>
    
    <form action="{{ route('biaya-operasional.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pencatatan</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full md:w-1/3 border rounded-lg px-3 py-2">
            @error('tanggal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <hr class="my-6">

        <div id="biaya-container" class="space-y-4">
            {{-- Baris dinamis akan ditambahkan di sini oleh Javascript --}}
        </div>
        
        <div class="mt-4">
            <button type="button" id="add-biaya-btn" class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">
                + Tambah Rincian Biaya
            </button>
        </div>

        <div class="mt-8 border-t pt-6 flex justify-end gap-3">
            <a href="{{ route('biaya-operasional.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">
                Simpan Semua
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('biaya-container');
    const addButton = document.getElementById('add-biaya-btn');
    let itemIndex = 0;
    const jenisBiayaOptions = ['Gaji', 'Listrik', 'Sewa', 'Air', 'Internet', 'Perawatan', 'Promosi', 'Lain-lain'];

    const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);
    const parseRupiah = (string) => string.replace(/[^0-9]/g, '');

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
        itemDiv.className = 'biaya-item p-4 border rounded-lg grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-gray-50';

        let optionsHtml = jenisBiayaOptions.map(opt => `<option value="${opt}">${opt}</option>`).join('');

        itemDiv.innerHTML = `
            <div>
                <label class="block text-sm font-medium">Jenis Biaya</label>
                <select name="biaya[${index}][jenis_biaya]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">${optionsHtml}</select>
            </div>
            <div>
                <label class="block text-sm font-medium">Keterangan</label>
                <input type="text" name="biaya[${index}][keterangan]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Gaji bulan ini">
            </div>
            <div>
                <label class="block text-sm font-medium">Jumlah (Rp)</label>
                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm jumlah-display" placeholder="500.000" required>
                <input type="hidden" name="biaya[${index}][jumlah]" class="jumlah-hidden">
            </div>
            <div class="text-right md:text-left">
                <button type="button" class="remove-biaya-btn px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition text-sm font-semibold">Hapus</button>
            </div>
        `;
        container.appendChild(itemDiv);

        itemDiv.querySelector('.remove-biaya-btn').addEventListener('click', () => itemDiv.remove());
    }

    addButton.addEventListener('click', createBiayaItem);
    // Tambah satu item secara default saat halaman dimuat
    createBiayaItem();
});
</script>
@endsection
