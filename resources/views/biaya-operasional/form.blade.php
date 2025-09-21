<div class="space-y-4">
    <div>
        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $biayaOperasional->tanggal ?? now()->toDateString()) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('tanggal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jenis_biaya" class="block text-sm font-medium text-gray-700">Jenis Biaya</label>
        <select name="jenis_biaya" id="jenis_biaya" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @php
                $jenisBiayaOptions = ['Gaji', 'Listrik', 'Sewa', 'Air', 'Internet', 'Perawatan', 'Promosi', 'Lain-lain'];
            @endphp
            <option value="">Pilih Jenis Biaya</option>
            @foreach($jenisBiayaOptions as $jenis)
                <option value="{{ $jenis }}" @if(old('jenis_biaya', $biayaOperasional->jenis_biaya ?? '') == $jenis) selected @endif>{{ $jenis }}</option>
            @endforeach
        </select>
        @error('jenis_biaya') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
        <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan', $biayaOperasional->keterangan ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('keterangan') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_display" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
        <input type="text" id="jumlah_display" value="{{ old('jumlah', $biayaOperasional->jumlah ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah', $biayaOperasional->jumlah ?? '') }}">
        @error('jumlah') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<script>
    if (typeof initRupiahFormatter !== 'function') {
        function initRupiahFormatter(displayElement, hiddenElement) {
            const formatRupiah = (number) => {
                if (number === null || typeof number === 'undefined' || number === '') return '';
                return new Intl.NumberFormat('id-ID').format(number);
            };
            const parseRupiah = (string) => string.replace(/[^0-9]/g, '');
            displayElement.value = formatRupiah(hiddenElement.value);
            displayElement.addEventListener('input', () => {
                const rawValue = parseRupiah(displayElement.value);
                hiddenElement.value = rawValue;
                displayElement.value = formatRupiah(rawValue);
            });
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahDisplay = document.getElementById('jumlah_display');
        const jumlahHidden = document.getElementById('jumlah');
        if (jumlahDisplay && jumlahHidden) {
            initRupiahFormatter(jumlahDisplay, jumlahHidden);
        }
    });
</script>