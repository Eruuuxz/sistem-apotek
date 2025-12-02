<table class="w-full text-sm text-left border-collapse">
    <thead class="bg-gray-100 text-gray-600 font-semibold sticky top-0 z-10 shadow-sm">
        <tr>
            <th class="px-4 py-3 w-1/3">Produk</th>
            <th class="px-4 py-3 text-right">Harga</th>
            <th class="px-4 py-3 text-center w-24">Qty</th>
            <th class="px-4 py-3 text-right">Subtotal</th>
            <th class="px-4 py-3 text-center w-10"><i data-feather="trash-2" class="w-4 h-4"></i></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
        @forelse($cart as $item)
            @php
                $isExpired = !empty($item['batches_used']) && collect($item['batches_used'])->min('expired_date') && \Carbon\Carbon::parse(collect($item['batches_used'])->min('expired_date'))->isPast();
            @endphp
            {{-- Ubah hover:bg-blue-50 menjadi hover:bg-green-50 --}}
            <tr class="hover:bg-green-50 transition duration-75 group {{ $isExpired ? 'bg-red-50' : '' }}">
                
                {{-- Nama & Kode --}}
                <td class="px-4 py-3 align-middle">
                    <div class="font-bold text-gray-800">{{ $item['nama'] }}</div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <span>{{ $item['kode'] }}</span>
                        @if($item['is_psikotropika'])
                            <span class="bg-purple-100 text-purple-700 px-1 rounded text-[10px] font-bold">Psikotropika</span>
                        @endif
                    </div>
                    @if($isExpired)
                        <span class="text-[10px] text-red-600 font-bold bg-red-100 px-1 rounded mt-1 inline-block">Expired!</span>
                    @endif
                </td>

                {{-- Harga --}}
                <td class="px-4 py-3 text-right align-middle text-gray-600">
                    {{ number_format($item['harga'], 0, ',', '.') }}
                </td>

                {{-- Qty (Input Focus Hijau) --}}
                <td class="px-4 py-3 align-middle">
                    <form action="{{ route('pos.update') }}" method="POST" class="flex items-center justify-center">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                        <input type="number" name="qty" value="{{ $item['qty'] }}"
                            class="w-16 text-center border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 font-bold text-gray-800 py-1"
                            onchange="this.form.submit()"
                            min="1" max="{{ $item['stok'] }}">
                    </form>
                    <div class="text-[10px] text-center text-gray-400 mt-1">Stok: {{ $item['stok'] }}</div>
                </td>

                {{-- Subtotal (Teks Hijau) --}}
                <td class="px-4 py-3 text-right align-middle font-bold text-green-600 text-base">
                    {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                </td>

                {{-- Hapus --}}
                <td class="px-4 py-3 text-center align-middle">
                    <form action="{{ route('pos.remove') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $item['kode'] }}">
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition p-1 rounded-full hover:bg-red-50">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-20 text-center text-gray-400 bg-gray-50/50">
                    <div class="flex flex-col items-center justify-center">
                        <i data-feather="shopping-cart" class="w-12 h-12 mb-3 text-gray-300"></i>
                        <p class="text-lg font-medium">Keranjang Kosong</p>
                        <p class="text-sm">Scan barcode atau cari barang untuk memulai.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>