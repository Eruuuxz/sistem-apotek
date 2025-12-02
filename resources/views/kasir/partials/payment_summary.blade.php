<form action="{{ route('pos.checkout') }}" method="POST" class="space-y-5 h-full flex flex-col">
    @csrf
    
    {{-- INFORMASI PELANGGAN --}}
    <div class="space-y-3 bg-white p-1 rounded-lg">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pelanggan</label>
            <div class="flex gap-2">
                <select id="member_search_select2" name="pelanggan_id" class="flex-1 text-sm border-gray-300 rounded-lg">
                    <option value="">-- Umum --</option>
                </select>
                <button type="button" onclick="openAddPelangganModal()" class="bg-green-100 text-green-600 p-2 rounded-lg hover:bg-green-200 transition">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        {{-- Input Nama Manual --}}
        <div class="grid grid-cols-2 gap-2">
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" 
                class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:bg-white transition" 
                placeholder="Nama Pelanggan *" required value="{{ old('nama_pelanggan') }}">
            
            <input type="text" id="telepon_pelanggan" name="telepon_pelanggan" 
                class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:bg-white transition" 
                placeholder="No. HP" value="{{ old('telepon_pelanggan') }}">
        </div>
        
        <textarea id="alamat_pelanggan" name="alamat_pelanggan" rows="1" 
            class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:bg-white transition resize-none" 
            placeholder="Alamat (Opsional)">{{ old('alamat_pelanggan') }}</textarea>

        <div id="ktp-input-group" style="display: none;" class="animate-fade-in-down">
            <input type="text" id="no_ktp" name="no_ktp" 
                class="w-full border-red-300 text-red-900 placeholder-red-400 rounded-lg text-sm px-3 py-2 bg-red-50 focus:ring-red-500" 
                placeholder="No. KTP (Wajib untuk Psikotropika)" value="{{ old('no_ktp') }}">
        </div>
    </div>

    <hr class="border-dashed border-gray-300">

    {{-- KALKULASI (SUBTOTAL & DISKON) --}}
    <div class="space-y-3">
        <div class="flex justify-between items-center text-sm text-gray-600">
            <span>Subtotal</span>
            <span class="font-bold text-gray-800">Rp {{ number_format($totalSubtotalBersih, 0, ',', '.') }}</span>
            <input type="hidden" id="total_subtotal" value="{{ $totalSubtotalBersih }}">
        </div>

        <div class="flex items-center justify-between gap-2">
            <span class="text-sm text-gray-600">Diskon</span>
            <div class="flex gap-1 w-1/2 justify-end">
                <input type="number" id="diskon_value" name="diskon_value" 
                    class="w-20 border-gray-300 rounded-l-lg text-right text-sm py-1" placeholder="0" 
                    value="{{ old('diskon_value', $diskonValue) }}" oninput="hitungTotal()">
                
                <select id="diskon_type" name="diskon_type" class="border-gray-300 rounded-r-lg bg-gray-100 text-sm py-1" onchange="hitungTotal()">
                    <option value="nominal" {{ old('diskon_type', $diskonType) == 'nominal' ? 'selected' : '' }}>Rp</option>
                    <option value="persen" {{ old('diskon_type', $diskonType) == 'persen' ? 'selected' : '' }}>%</option>
                </select>
            </div>
        </div>

        {{-- TOTAL AKHIR --}}
        <div class="flex justify-between items-center text-lg font-bold text-gray-800 pt-2 border-t border-gray-200">
            <span>Tagihan</span>
            <span id="total_display_small">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</span>
            <input type="hidden" name="total_hidden" id="total_hidden" value="{{ $totalAkhir }}">
        </div>
    </div>

    {{-- INPUT PEMBAYARAN (HIJAU & BESAR) --}}
    <div class="mt-auto pt-4 space-y-4">
        <div>
            <label class="block text-xs font-bold text-green-600 uppercase mb-1">Bayar Tunai (Rp)</label>
            {{-- Input Bayar --}}
            <input type="text" id="bayar_display" 
                class="w-full border-2 border-green-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 rounded-xl px-4 py-3 text-right text-2xl font-bold text-gray-800 tracking-wide transition placeholder-gray-300" 
                placeholder="0" oninput="formatBayar()" required autocomplete="off">
            <input type="hidden" name="bayar" id="bayar" value="0">
        </div>

        {{-- Input Kembalian (Readonly) --}}
        <div class="flex justify-between items-center bg-gray-100 px-4 py-3 rounded-xl">
            <label class="text-sm font-semibold text-gray-600">Kembalian</label>
            <input type="text" id="kembalian" 
                class="w-1/2 bg-transparent border-none text-right font-bold text-xl text-gray-500 focus:ring-0 p-0" 
                value="Rp 0" readonly>
        </div>

        <button type="submit" 
            class="w-full bg-green-600 hover:bg-green-700 text-white text-lg font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
            <i data-feather="check-circle"></i>
            Proses Pembayaran
        </button>
    </div>
</form>

<script>
    // --- FUNGSI HELPER (Didefinisikan di window agar global & aman) ---

    // 1. Format ke Rupiah
    window.formatRupiah = function(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(angka);
    }

    // 2. Parse String ke Angka Murni
    window.parseNumber = function(val) {
        if (!val) return 0;
        let bersih = val.toString().replace(/[^0-9]/g, ''); // Hapus semua kecuali angka
        return parseFloat(bersih) || 0;
    }

    // --- LOGIKA UTAMA ---

    window.hitungTotal = function() {
        let subtotal = parseFloat(document.getElementById('total_subtotal').value) || 0;
        let diskonVal = parseFloat(document.getElementById('diskon_value').value) || 0;
        let diskonType = document.getElementById('diskon_type').value;
        let totalAkhir = subtotal;

        // Hitung Diskon
        if (diskonType === 'persen') {
            totalAkhir = subtotal - (subtotal * (diskonVal / 100));
        } else {
            totalAkhir = subtotal - diskonVal;
        }

        totalAkhir = Math.max(0, totalAkhir); // Tidak boleh minus

        // Update UI Total
        document.getElementById('total_hidden').value = totalAkhir;
        document.getElementById('total_display_small').innerText = window.formatRupiah(totalAkhir);
        
        // Update Total Besar di Sidebar Kiri (jika ada)
        let bigTotal = document.getElementById('total_display_big');
        if (bigTotal) bigTotal.innerText = window.formatRupiah(totalAkhir);

        // Hitung ulang kembalian karena total tagihan berubah
        window.hitungKembalian();
    }

    window.formatBayar = function() {
        let input = document.getElementById('bayar_display');
        let hidden = document.getElementById('bayar');
        
        // Ambil angka murni
        let rawValue = window.parseNumber(input.value);
        
        // Simpan ke hidden input
        hidden.value = rawValue;
        
        // Format tampilan input (tambah titik ribuan)
        if (input.value.trim() !== '') {
            input.value = new Intl.NumberFormat('id-ID').format(rawValue);
        } else {
            hidden.value = 0;
        }
        
        window.hitungKembalian();
    }

    window.hitungKembalian = function() {
        let total = parseFloat(document.getElementById('total_hidden').value) || 0;
        let bayar = parseFloat(document.getElementById('bayar').value) || 0;
        
        let kembalian = bayar - total;
        let elKembalian = document.getElementById('kembalian');

        // Update nilai pada INPUT kembalian
        if (kembalian < 0) {
            // Uang Kurang
            elKembalian.value = window.formatRupiah(kembalian); 
            elKembalian.style.color = "#dc2626"; // Merah (Tailwind red-600)
        } else {
            // Uang Pas / Lebih
            elKembalian.value = window.formatRupiah(kembalian);
            elKembalian.style.color = "#16a34a"; // Hijau (Tailwind green-600)
        }
    }

    // Inisialisasi saat load
    document.addEventListener("DOMContentLoaded", function() {
        window.hitungTotal();
    });
</script>