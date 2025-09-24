@extends('layouts.admin')

@section('title', 'Edit Biaya Operasional')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Edit Biaya Operasional</h2>
        <p class="text-sm text-gray-500">Perbarui rincian biaya operasional di bawah ini.</p>
    </div>

    <form action="{{ route('biaya-operasional.update', $biayaOperasional->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $biayaOperasional->tanggal->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                @error('tanggal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="jenis_biaya" class="block text-sm font-semibold text-gray-700">Jenis Biaya</label>
                <select name="jenis_biaya" id="jenis_biaya" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @php
                        $jenisBiayaOptions = ['Gaji', 'Listrik', 'Sewa', 'Air', 'Internet', 'Perawatan', 'Promosi', 'Lain-lain'];
                    @endphp
                    <option value="">Pilih Jenis Biaya</option>
                    @foreach($jenisBiayaOptions as $jenis)
                        <option value="{{ $jenis }}" @if(old('jenis_biaya', $biayaOperasional->jenis_biaya) == $jenis) selected @endif>{{ $jenis }}</option>
                    @endforeach
                </select>
                @error('jenis_biaya') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan', $biayaOperasional->keterangan) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                @error('keterangan') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="jumlah_display" class="block text-sm font-semibold text-gray-700">Jumlah (Rp)</label>
                <input type="text" id="jumlah_display" value="{{ old('jumlah', $biayaOperasional->jumlah) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah', $biayaOperasional->jumlah) }}">
                @error('jumlah') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="mt-8 border-t pt-6 flex justify-end gap-3">
            <a href="{{ route('biaya-operasional.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">
                Update Biaya
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);
    const parseRupiah = (string) => string.replace(/[^0-9]/g, '');
    
    const jumlahDisplay = document.getElementById('jumlah_display');
    const jumlahHidden = document.getElementById('jumlah');

    if(jumlahDisplay && jumlahHidden) {
        jumlahDisplay.value = formatRupiah(jumlahHidden.value);
        jumlahDisplay.addEventListener('input', () => {
            const rawValue = parseRupiah(jumlahDisplay.value);
            hiddenInput.value = rawValue;
            jumlahDisplay.value = formatRupiah(rawValue);
        });
    }
});
</script>
@endsection