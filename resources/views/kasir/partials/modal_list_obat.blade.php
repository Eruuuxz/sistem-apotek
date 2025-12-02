<div id="obatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity">
    <div class="bg-white w-11/12 md:w-3/4 lg:w-2/3 p-0 rounded-2xl shadow-2xl relative max-h-[85vh] flex flex-col overflow-hidden">
        
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Katalog Obat</h2>
                <p class="text-xs text-gray-500">Tekan tombol <span class="font-mono bg-gray-200 px-1 rounded">+</span> untuk menambahkan ke keranjang</p>
            </div>
            <button onclick="closeObatModal()" class="bg-white p-2 rounded-full text-gray-500 hover:text-red-500 hover:bg-red-50 transition shadow-sm border">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-4 bg-white border-b border-gray-100">
            <div class="relative">
                <i data-feather="search" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                <input type="text" id="modal-search" placeholder="Cari nama obat, kode, atau kategori..." 
                    class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
            </div>
        </div>

        <div class="overflow-y-auto flex-grow bg-white p-0">
            <table class="min-w-full text-sm text-left" id="obat-table">
                <thead class="bg-gray-100 text-gray-600 sticky top-0 z-10">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama Obat</th>
                        <th class="px-5 py-3 font-semibold">Kategori</th>
                        <th class="px-5 py-3 text-right font-semibold">Harga</th>
                        <th class="px-5 py-3 text-center font-semibold">Stok</th>
                        <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($obat as $item)
                        @php 
                            $isExpired = $item->expired_date && \Carbon\Carbon::parse($item->expired_date)->isPast(); 
                            $isHabis = $item->stok == 0;
                        @endphp
                        <tr class="hover:bg-green-50 transition duration-75 group {{ $isHabis || $isExpired ? 'bg-gray-50 opacity-60 grayscale' : '' }}">
                            <td class="px-5 py-3 align-middle">
                                <div class="font-bold text-gray-800">{{ $item->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $item->kode }}</div>
                                @if($isExpired) <span class="text-[10px] text-red-600 font-bold bg-red-100 px-1 rounded">Expired</span> @endif
                            </td>
                            <td class="px-5 py-3 align-middle text-gray-600">
                                <span class="px-2 py-1 bg-gray-100 rounded-md text-xs">{{ $item->kategori }}</span>
                            </td>
                            <td class="px-5 py-3 text-right align-middle font-medium text-gray-700">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center align-middle">
                                @if($isHabis)
                                    <span class="text-red-500 font-bold text-xs">Habis</span>
                                @else
                                    <span class="font-mono {{ $item->stok < 10 ? 'text-yellow-600' : 'text-green-600' }} font-bold">{{ $item->stok }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center align-middle">
                                <button type="button" class="add-to-cart-btn bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed group-hover:scale-105 transform duration-150"
                                        data-kode="{{ $item->kode }}"
                                        @if($isHabis || $isExpired) disabled @endif>
                                    <i data-feather="plus" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>