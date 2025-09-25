<div id="obatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 p-6 rounded-2xl shadow-lg relative max-h-[90vh] flex flex-col">
        <button onclick="closeObatModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
        <h2 class="text-xl font-semibold mb-4">Daftar Obat</h2>

        <div class="mb-4">
            <input type="text" id="modal-search" placeholder="Cari obat..." class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div class="overflow-y-auto flex-grow">
            <table class="min-w-full border-collapse border border-gray-200 text-sm" id="obat-table">
                <thead class="bg-gray-100 rounded-t-lg sticky top-0">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Obat</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-right">Harga</th>
                        <th class="px-3 py-2 text-right">Stok</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($obat as $item)
                        @php $isExpired = $item->expired_date && \Carbon\Carbon::parse($item->expired_date)->isPast(); @endphp
                        <tr class="hover:bg-gray-50 transition duration-150 {{ $item->stok == 0 || $isExpired ? 'bg-red-50 opacity-50' : ($item->stok < 10 ? 'bg-yellow-50' : '') }}">
                            <td class="border px-3 py-2">
                                {{ $item->nama }}
                                <span class="block text-xs text-gray-500">{{ $item->kode }}</span>
                                @if($isExpired) <span class="text-xs text-red-600">(Kadaluarsa)</span> @endif
                            </td>
                            <td class="border px-3 py-2">{{ $item->kategori }}</td>
                            <td class="border px-3 py-2 text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="border px-3 py-2 text-right">{{ $item->stok }}</td>
                            <td class="border px-3 py-2 text-center">
                                {{-- PASTIKAN INI ADALAH BUTTON DENGAN CLASS & DATA-KODE --}}
                                <button type="button" class="add-to-cart-btn bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-lg transition text-xs"
                                        data-kode="{{ $item->kode }}"
                                        @if($item->stok == 0 || $isExpired) disabled @endif>
                                    + Tambah
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>