{{-- File: /views/kasir/pos.blade.php --}}
@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Point of Sale (POS) - Kasir</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-4">
    <!-- Form Input Obat -->
    <div class="col-span-2 bg-white p-4 shadow rounded">
        <form action="{{ route('pos.add') }}" method="POST" class="mb-4 relative">
            @csrf
            <input type="text" id="search" name="kode" placeholder="Scan / Ketik Nama/Kode Obat" 
                   class="border px-3 py-2 w-full" autocomplete="off">
            <ul id="suggestions" 
                class="absolute bg-white border rounded w-full mt-1 hidden max-h-60 overflow-y-auto z-50"></ul>
        </form>


            <table class="w-full text-sm bg-white border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 text-left">Kode</th>
                        <th class="px-2 py-1 text-left">Nama Obat</th>
                        <th class="px-2 py-1 text-right">Harga</th>
                        <th class="px-2 py-1 text-center">Qty</th>
                        <th class="px-2 py-1 text-right">Stok</th>
                        <th class="px-2 py-1 text-right">Subtotal</th>
                        <th class="px-2 py-1 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="daftar_obat">
                    @forelse($cart as $item)
                    <tr>
                        <td class="border px-2 py-1">{{ $item['kode'] }}</td>
                        <td class="border px-2 py-1">{{ $item['nama'] }}</td>
                        <td class="border px-2 py-1 text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-center">
                            <form action="{{ route('pos.update') }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                                <input type="number" name="qty" value="{{ $item['qty'] }}" 
                                    class="w-16 border text-center" 
                                    onchange="this.form.submit()" 
                                    min="1" max="{{ $item['stok'] }}">
                            </form>
                        </td>
                        <td class="border px-2 py-1 text-right">{{ $item['stok'] }}</td>
                        <td class="border px-2 py-1 text-right">Rp {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-center">
                            <form action="{{ route('pos.remove') }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                                <button type="submit" class="text-red-500">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="border px-2 py-1 text-center">Keranjang kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Ringkasan Pembayaran -->
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="kasir_nama" class="block text-sm font-medium text-gray-700">Nama Kasir</label>
                    <input type="text" name="kasir_nama" id="kasir_nama" class="w-full border px-3 py-2" value="{{ Auth::user()->name }}" readonly required>
                </div>
                <div class="mb-3">
                    <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="text" id="total" class="w-full border px-3 py-2 text-right font-bold bg-gray-100" value="Rp {{ number_format($total, 0, ',', '.') }}" readonly>
                    <input type="hidden" name="total_hidden" value="{{ $total }}"> {{-- Hidden input untuk total --}}
                </div>
                <div class="mb-3">
                    <label for="bayar" class="block text-sm font-medium text-gray-700">Bayar</label>
                    <input type="text" id="bayar_display" class="w-full border px-3 py-2 text-right" placeholder="Rp 0"oninput="formatBayar()" required>
                    <input type="hidden" name="bayar" id="bayar"> {{-- nilai asli tanpa format --}}
                </div>
                <div class="mb-3">
                    <label for="kembalian" class="block text-sm font-medium text-gray-700">Kembalian</label>
                    <input type="text" id="kembalian" class="w-full border px-3 py-2 text-right font-bold bg-gray-100" value="0" readonly>
                </div>
                <button type="submit" class="bg-green-600 text-white w-full py-2 rounded">Simpan & Cetak Struk</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi kembalian saat halaman dimuat
        hitungKembalian();
    });

    function hitungKembalian() {
        // Ambil nilai total dari hidden input untuk perhitungan yang akurat
        let total = parseFloat(document.querySelector('input[name="total_hidden"]').value) || 0; 
        let bayar = parseFloat(document.getElementById('bayar').value) || 0;
        let kembalian = bayar - total;

        // Format kembalian ke Rupiah
        document.getElementById('kembalian').value = 'Rp ' + kembalian.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
</script>
<script>
    let searchBox = document.getElementById('search');
    let suggestionBox = document.getElementById('suggestions');

    searchBox.addEventListener('keyup', function() {
        let q = this.value.trim();

        if (q.length < 1) {
            suggestionBox.innerHTML = "";
            suggestionBox.classList.add('hidden');
            return;
        }

        fetch(`/pos/search?q=${q}`)
            .then(res => res.json())
            .then(data => {
                suggestionBox.innerHTML = "";
                if (data.length === 0) {
                    suggestionBox.classList.add('hidden');
                    return;
                }

                data.forEach(item => {
                    let li = document.createElement('li');
                    li.className = "px-3 py-2 hover:bg-gray-100 cursor-pointer";
                    li.innerHTML = `${item.nama} <span class="text-sm text-gray-500">(${item.kode})</span>`;

                    // Klik item suggestion -> isi input & auto submit form
                    li.onclick = () => {
                        searchBox.value = item.kode;
                        suggestionBox.innerHTML = "";
                        suggestionBox.classList.add('hidden');
                        searchBox.form.submit(); 
                    };

                    suggestionBox.appendChild(li);
                });

                suggestionBox.classList.remove('hidden');
            });
    });

    // Kalau klik di luar suggestion -> sembunyikan
    document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target) && !suggestionBox.contains(e.target)) {
            suggestionBox.innerHTML = "";
            suggestionBox.classList.add('hidden');
        }
    });
</script>
@push('scripts')
<script>
function formatBayar() {
    let input = document.getElementById('bayar_display');
    let hidden = document.getElementById('bayar');

    // Ambil hanya angka
    let value = input.value.replace(/\D/g, '');
    if (!value) {
        hidden.value = 0;
        input.value = "";
        hitungKembalian();
        return;
    }

    // Simpan angka asli ke hidden input
    hidden.value = parseInt(value, 10);

    // Format ke Rupiah
    input.value = hidden.value.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });

    // Update kembalian
    hitungKembalian();
}

function hitungKembalian() {
    let total = parseFloat(document.querySelector('input[name="total_hidden"]').value) || 0; 
    let bayar = parseFloat(document.getElementById('bayar').value) || 0;
    let kembalian = bayar - total;

    document.getElementById('kembalian').value = 'Rp ' + 
        (kembalian > 0 ? kembalian.toLocaleString('id-ID') : "0");
}
</script>
@endpush

@endpush