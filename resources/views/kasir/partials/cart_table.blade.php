{{-- Form Input & Tabel Obat --}}
<div class="md:col-span-2 bg-white shadow-lg rounded-2xl p-5">
    <div class="flex items-center gap-2 mb-4">
        <form action="{{ route('pos.add') }}" method="POST" class="relative flex-grow">
            @csrf
            <input type="text" id="search" name="kode" placeholder="Nama/Kode Obat"
                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                autocomplete="off">
            <ul id="suggestions"
                class="absolute bg-white border rounded-lg w-full mt-1 hidden max-h-60 overflow-y-auto z-50 shadow-md">
            </ul>
        </form>
        {{-- PASTIKAN FUNGSI INI BENAR --}}
        <button type="button" onclick="openObatModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition whitespace-nowrap">
            List Obat
        </button>
    </div>

    <div class="overflow-x-auto">
        <table id="cart-table" class="min-w-full border-collapse border border-gray-200 text-sm">
            <thead class="bg-gray-100 rounded-t-lg">
                <tr>
                    <th class="px-3 py-2 text-left">Kode</th>
                    <th class="px-3 py-2 text-left">Nama Obat</th>
                    <th class="px-3 py-2 text-left">Kategori</th>
                    <th class="px-3 py-2 text-left">Expired Date</th>
                    <th class="px-3 py-2 text-right">Harga</th>
                    <th class="px-3 py-2 text-center">Qty</th>
                    <th class="px-3 py-2 text-right">Stok</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cart as $item)
                    @php
                        $isExpired = false;
                        $expiredDateDisplay = 'N/A';
                        if (!empty($item['batches_used'])) {
                            $expiredDate = collect($item['batches_used'])->min('expired_date');
                            if ($expiredDate) {
                                $expiredDateDisplay = \Carbon\Carbon::parse($expiredDate)->format('d-m-Y');
                                $isExpired = \Carbon\Carbon::parse($expiredDate)->isPast();
                            }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $item['stok'] == 0 || $isExpired ? 'bg-red-50' : ($item['stok'] < 10 ? 'bg-yellow-50' : '') }}"
                        data-is-psikotropika="{{ $item['is_psikotropika'] ? 'true' : 'false' }}">
                        <td class="border px-3 py-2">{{ $item['kode'] }}</td>
                        <td class="border px-3 py-2">{{ $item['nama'] }}</td>
                        <td class="border px-3 py-2">{{ $item['kategori'] }}</td>
                        <td class="border px-3 py-2">
                            {{ $expiredDateDisplay }}
                            @if($isExpired)
                                <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Expired</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-center">
                            <form action="{{ route('pos.update') }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                                <input type="number" name="qty" value="{{ $item['qty'] }}"
                                    class="w-16 border rounded text-center px-1 py-1" onchange="this.form.submit()"
                                    min="0" max="{{ $item['stok'] }}">
                            </form>
                        </td>
                        {{-- <td class="border px-3 py-2 text-right">Rp {{ number_format($ppnPerItem * $item['qty'], 0, ',', '.') }}</td> --}} {{-- PPN Dihapus --}}
                        <td class="border px-3 py-2 text-right">
                            {{ $item['stok'] }}
                            @if($item['stok'] == 0)
                                <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Habis</span>
                            @elseif($item['stok'] < 10)
                                <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded-full">Menipis</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}</td>
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
                        {{-- Colspan diubah dari 10 menjadi 9 --}}
                        <td colspan="9" class="border px-3 py-2 text-center text-gray-400">Keranjang kosong.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>