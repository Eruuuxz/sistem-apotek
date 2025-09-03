@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')

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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Form Input & Tabel Obat -->
        <div class="md:col-span-2 bg-white shadow-lg rounded-2xl p-5">
            <form action="{{ route('pos.add') }}" method="POST" class="mb-4 relative">
                @csrf
                <input type="text" id="search" name="kode" placeholder="Nama/Kode Obat"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    autocomplete="off">
                <ul id="suggestions"
                    class="absolute bg-white border rounded-lg w-full mt-1 hidden max-h-60 overflow-y-auto z-50 shadow-md">
                </ul>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-200 text-sm">
                    <thead class="bg-gray-100 rounded-t-lg">
                        <tr>
                            <th class="px-3 py-2 text-left">Kode</th>
                            <th class="px-3 py-2 text-left">Nama Obat</th>
                            <th class="px-3 py-2 text-right">Harga</th>
                            <th class="px-3 py-2 text-center">Qty</th>
                            <th class="px-3 py-2 text-right">Stok</th>
                            <th class="px-3 py-2 text-right">Subtotal</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cart as $item)
                            <tr
                                class="hover:bg-gray-50 transition duration-150 {{ $item['stok'] == 0 ? 'bg-red-50' : ($item['stok'] < 10 ? 'bg-yellow-50' : '') }}">
                                <td class="border px-3 py-2">{{ $item['kode'] }}</td>
                                <td class="border px-3 py-2">{{ $item['nama'] }}</td>
                                <td class="border px-3 py-2 text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <form action="{{ route('pos.update') }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                                        <input type="number" name="qty" value="{{ $item['qty'] }}"
                                            class="w-16 border rounded text-center px-1 py-1" onchange="this.form.submit()"
                                            min="1" max="{{ $item['stok'] }}">
                                    </form>
                                </td>
                                <td class="border px-3 py-2 text-right">
                                    {{ $item['stok'] }}
                                    @if($item['stok'] == 0)
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Habis</span>
                                    @elseif($item['stok'] < 10)
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded-full">Menipis</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2 text-right">Rp
                                    {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <form action="{{ route('pos.remove') }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-3 py-2 text-center text-gray-400">Keranjang kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="bg-white shadow-lg rounded-2xl p-5">
            <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
            <form action="{{ route('pos.checkout') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Kasir</label>
                    <input type="text" name="kasir_nama" class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        value="{{ Auth::user()->name }}" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="text" id="total"
                        class="w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100"
                        value="Rp {{ number_format($total, 0, ',', '.') }}" readonly>
                    <input type="hidden" name="total_hidden" value="{{ $total }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bayar</label>
                    <input type="text" id="bayar_display" class="w-full border rounded-lg px-3 py-2 text-right"
                        placeholder="Rp 0" oninput="formatBayar()" required>
                    <input type="hidden" name="bayar" id="bayar">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kembalian</label>
                    <input type="text" id="kembalian"
                        class="w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100" value="0" readonly>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">Simpan & Cetak
                    Struk</button>
            </form>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        document.addEventListener('DOMContentLoaded', function () {
            const searchBox = document.getElementById('search');
            const suggestionBox = document.getElementById('suggestions');
            let timer;
            let selectedIndex = -1;

            function highlightText(text, query) {
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<span class="bg-yellow-200">$1</span>');
            }

            function fetchSuggestions(q) {
                fetch(`/pos/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        suggestionBox.innerHTML = "";
                        selectedIndex = -1;

                        if (data.length === 0) {
                            suggestionBox.classList.add('hidden');
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = "px-3 py-2 hover:bg-gray-100 cursor-pointer";
                            li.innerHTML = `${highlightText(item.nama, q)} <span class="text-sm text-gray-500">(${item.kode})</span>`;

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
            }

            function updateSelection(items) {
                items.forEach((li, idx) => {
                    li.classList.toggle('bg-blue-100', idx === selectedIndex);
                });
            }

            searchBox.addEventListener('keyup', function (e) {
                const q = this.value.trim();

                // Keyboard navigation
                const items = suggestionBox.querySelectorAll('li');
                if (items.length > 0) {
                    if (e.key === "ArrowDown") {
                        selectedIndex = (selectedIndex + 1) % items.length;
                        updateSelection(items);
                        return;
                    } else if (e.key === "ArrowUp") {
                        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                        updateSelection(items);
                        return;
                    } else if (e.key === "Enter") {
                        if (selectedIndex >= 0 && selectedIndex < items.length) {
                            items[selectedIndex].click();
                            return;
                        }
                    }
                }

                // Debounce fetch
                clearTimeout(timer);
                if (q.length < 1) {
                    suggestionBox.innerHTML = "";
                    suggestionBox.classList.add('hidden');
                    return;
                }

                timer = setTimeout(() => fetchSuggestions(q), 300);
            });

            // Klik di luar suggestion -> sembunyikan
            document.addEventListener('click', function (e) {
                if (!searchBox.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.innerHTML = "";
                    suggestionBox.classList.add('hidden');
                }
            });
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