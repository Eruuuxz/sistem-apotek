@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm-inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Cek apakah ada shift yang aktif --}}
    @if ($activeShift)
        {{-- Tampilan POS utama jika shift aktif --}}
        <div class="alert bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 flex justify-between items-center">
            <div>
                Anda sudah memiliki shift aktif: <strong>{{ $activeShift->shift->name }}</strong> dimulai pada {{ $activeShift->start_time }}.
            </div>
            
            {{-- Tombol Akhiri Shift --}}
            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap" onclick="openEndShiftModal()">
                Akhiri Shift
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <button type="button" onclick="openModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition whitespace-nowrap">
                        List Obat
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 rounded-t-lg">
                            <tr>
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Obat</th>
                                <th class="px-3 py-2 text-left">Kategori</th>
                                <th class="px-3 py-2 text-left">Expired Date</th>
                                <th class="px-3 py-2 text-right">Harga</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">PPN</th>
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
                                    // Hitung PPN per item
                                    $hargaJualBersih = $item['harga'];
                                    if ($item['ppn_included'] && $item['ppn_rate'] > 0) {
                                        $hargaJualBersih = $item['harga'] / (1 + $item['ppn_rate'] / 100);
                                    }
                                    $ppnPerItem = $item['harga'] - $hargaJualBersih;
                                @endphp
                                <tr
                                    class="hover:bg-gray-50 transition duration-150 
                                    {{ $item['stok'] == 0 || $isExpired ? 'bg-red-50' : ($item['stok'] < 10 ? 'bg-yellow-50' : '') }}"
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
                                    <td class="border px-3 py-2 text-right">Rp {{ number_format($ppnPerItem * $item['qty'], 0, ',', '.') }}</td>
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
                                    <td colspan="10" class="border px-3 py-2 text-center text-gray-400">Keranjang kosong.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ringkasan Pembayaran --}}
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Ringkasan</h2>
                <form action="{{ route('pos.checkout') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Nama Kasir</label>
                        <input type="text" name="kasir_nama" class="col-span-2 w-full border rounded-lg px-3 py-2 bg-gray-100"
                            value="{{ Auth::user()->name }}" readonly>
                    </div>

                    {{-- Pilihan Member --}}
                    <div class="grid grid-cols-3 items-start gap-2">
                        <label for="member_search_select2" class="text-sm font-medium text-gray-700 mt-2">Pilih Member</label>
                        <div class="col-span-2 flex gap-2">
                            <select id="member_search_select2" name="pelanggan_id" class="w-full">
                                <option value="">-- Bukan Member --</option>
                                {{-- Options will be loaded via AJAX --}}
                            </select>
                            <button type="button" onclick="openAddPelangganModal()" class="bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                                + Baru
                            </button>
                        </div>
                    </div>
                    {{-- End Pilihan Member --}}

                    {{-- Input data pelanggan --}}
                    <div class="grid grid-cols-3 items-start gap-2">
                        <label for="nama_pelanggan" class="text-sm font-medium text-gray-700 mt-2">Nama Pelanggan <span
                                class="text-red-600">*</span></label>
                        <div class="col-span-2">
                            <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                                class="w-full border rounded-lg px-3 py-2" placeholder="Nama Pelanggan" required
                                value="{{ old('nama_pelanggan') }}">
                            @error('nama_pelanggan')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-start gap-2">
                        <label for="alamat_pelanggan" class="text-sm font-medium text-gray-700 mt-2">Alamat</label>
                        <div class="col-span-2">
                            <textarea id="alamat_pelanggan" name="alamat_pelanggan" rows="2"
                                class="w-full border rounded-lg px-3 py-2"
                                placeholder="Alamat Pelanggan">{{ old('alamat_pelanggan') }}</textarea>
                            @error('alamat_pelanggan')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-start gap-2">
                        <label for="telepon_pelanggan" class="text-sm font-medium text-gray-700 mt-2">Telepon</label>
                        <div class="col-span-2">
                            <input type="text" id="telepon_pelanggan" name="telepon_pelanggan"
                                class="w-full border rounded-lg px-3 py-2" placeholder="No. Telepon Pelanggan"
                                value="{{ old('telepon_pelanggan') }}">
                            @error('telepon_pelanggan')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    {{-- End Input data pelanggan --}}

                    {{-- Input KTP (hanya muncul jika ada psikotropika) --}}
                    <div id="ktp-input-group" class="grid grid-cols-3 items-start gap-2" style="display: none;">
                        <label for="no_ktp" class="text-sm font-medium text-gray-700 mt-2">No. KTP <span class="text-red-600">*</span></label>
                        <div class="col-span-2">
                            <input type="text" id="no_ktp" name="no_ktp"
                                class="w-full border rounded-lg px-3 py-2" placeholder="Nomor KTP Pelanggan"
                                value="{{ old('no_ktp') }}">
                            @error('no_ktp')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Perbaikan: Mengganti variabel $total menjadi $totalSubtotalBersih --}}
                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Total Harga</label>
                        <div class="col-span-2">
                            <input type="text" id="subtotal_display"
                                class="w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100"
                                value="Rp {{ number_format($totalSubtotalBersih, 0, ',', '.') }}" readonly>
                            <input type="hidden" name="total_subtotal" id="total_subtotal" value="{{ $totalSubtotalBersih }}">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Diskon</label>
                        <div class="col-span-2 flex gap-2">
                            <input type="number" id="diskon_value" name="diskon_value"
                                class="w-full border rounded-lg px-3 py-2 text-right"
                                placeholder="0" value="{{ old('diskon_value', $diskonValue) }}" oninput="hitungTotal()">
                            <select id="diskon_type" name="diskon_type"
                                class="border rounded-lg px-2 py-2" onchange="hitungTotal()">
                                <option value="nominal" {{ old('diskon_type', $diskonType) == 'nominal' ? 'selected' : '' }}>Rp</option>
                                <option value="persen" {{ old('diskon_type', $diskonType) == 'persen' ? 'selected' : '' }}>%</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">PPN (11%)</label>
                        <div class="col-span-2">
                            <input type="text" id="ppn_display"
                                class="w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100"
                                value="Rp {{ number_format($totalPpn, 0, ',', '.') }}" readonly>
                            <input type="hidden" name="total_ppn" id="total_ppn" value="{{ $totalPpn }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Total Akhir</label>
                        <div class="col-span-2">
                            <input type="text" id="total_display"
                                class="w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100"
                                value="Rp {{ number_format($totalAkhir, 0, ',', '.') }}" readonly>
                            <input type="hidden" name="total_hidden" id="total_hidden" value="{{ $totalAkhir }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Bayar</label>
                        <div class="col-span-2">
                            <input type="text" id="bayar_display" class="w-full border rounded-lg px-3 py-2 text-right"
                                placeholder="Rp 0" oninput="formatBayar()" required>
                            <input type="hidden" name="bayar" id="bayar">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Kembalian</label>
                        <input type="text" id="kembalian"
                            class="col-span-2 w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100" value="0"
                            readonly>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition font-medium">
                        Simpan & Cetak Struk
                    </button>
                </form>
            </div>
        </div>

        {{-- Modal Akhiri Shift --}}
        <div id="endShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-6 rounded-2xl shadow-lg relative w-full max-w-sm">
                <button onclick="closeEndShiftModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
                <h2 class="text-xl font-semibold mb-4">Konfirmasi Akhiri Shift</h2>
                <form action="{{ route('shifts.end') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm">Shift aktif: <strong>{{ $activeShift->shift->name }}</strong> dimulai pada {{ $activeShift->start_time }}.</p>
                    </div>
                    <div>
                        <label for="final_cash" class="block text-sm font-medium text-gray-700">Modal Akhir Kasir (Rp)</label>
                        <input type="number" name="final_cash" id="final_cash" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required min="0" readonly>
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition">
                        Akhiri Shift & Keluar
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Modal List Obat --}}
        <div id="obatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 p-6 rounded-2xl shadow-lg relative max-h-[90vh] overflow-hidden">
                <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
                <h2 class="text-xl font-semibold mb-4">Daftar Obat</h2>
                
                <div class="mb-4">
                    <input type="text" id="modal-search" placeholder="Cari obat..." class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>

                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full border-collapse border border-gray-200 text-sm" id="obat-table">
                        <thead class="bg-gray-100 rounded-t-lg sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left cursor-pointer sortable" data-sort-type="string" data-sort-dir="asc">
                                    Kode <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-left cursor-pointer sortable" data-sort-type="string" data-sort-dir="asc">
                                    Nama Obat <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-left cursor-pointer sortable" data-sort-type="string" data-sort-dir="asc">
                                    Kategori <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-left cursor-pointer sortable" data-sort-type="date" data-sort-dir="asc">
                                    Kadaluarsa <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-right cursor-pointer sortable" data-sort-type="number" data-sort-dir="asc">
                                    Harga <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-right cursor-pointer sortable" data-sort-type="number" data-sort-dir="asc">
                                    Stok <span class="sort-icon"></span>
                                </th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($obat as $item)
                                @php
                                    $isExpired = \Carbon\Carbon::parse($item->expired_date)->isPast();
                                @endphp
                                <tr class="hover:bg-gray-50 transition duration-150 {{ $item->stok == 0 || $isExpired ? 'bg-red-50' : ($item->stok < 10 ? 'bg-yellow-50' : '') }}">
                                    <td class="border px-3 py-2">{{ $item->kode }}</td>
                                    <td class="border px-3 py-2">{{ $item->nama }}</td>
                                    <td class="border px-3 py-2">{{ $item->kategori }}</td>
                                    <td class="border px-3 py-2" data-value="{{ $item->expired_date }}">
                                        {{ \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') }}
                                        @if($isExpired)
                                            <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Expired</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2 text-right" data-value="{{ $item->harga_jual }}">
                                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="border px-3 py-2 text-right" data-value="{{ $item->stok }}">
                                        {{ $item->stok }}
                                        @if($item->stok == 0)
                                            <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Habis</span>
                                        @elseif($item->stok < 10)
                                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded-full">Menipis</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2 text-center">
                                        <form action="{{ route('pos.add') }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="kode" value="{{ $item->kode }}">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white py-1 px-2 rounded-lg transition text-xs"
                                                @if($item->stok == 0 || $isExpired) disabled @endif>
                                                Tambah
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- Modal Tambah Pelanggan Cepat --}}
        <div id="addPelangganModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white w-11/12 md:w-1/3 p-6 rounded-2xl shadow-lg relative">
                <button onclick="closeAddPelangganModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
                <h2 class="text-xl font-semibold mb-4">Tambah Pelanggan Baru</h2>
                <form id="add-pelanggan-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="new_nama_pelanggan" class="block text-sm font-medium text-gray-700">Nama Pelanggan <span class="text-red-600">*</span></label>
                        <input type="text" id="new_nama_pelanggan" name="nama" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label for="new_telepon_pelanggan" class="block text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" id="new_telepon_pelanggan" name="telepon" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label for="new_alamat_pelanggan" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea id="new_alamat_pelanggan" name="alamat" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Simpan Pelanggan</button>
                </form>
            </div>
        </div>

    @else
        {{-- Tampilan form "Mulai Shift" jika belum ada shift aktif --}}
        <div class="flex items-center justify-center h-screen bg-gray-100">
            <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm text-center">
                <h2 class="text-2xl font-bold mb-4">Mulai Shift Kasir</h2>
                <div class="text-gray-600 mb-6">Anda harus memulai shift kasir sebelum dapat melakukan transaksi.</div>
                
                <form method="POST" action="{{ route('shifts.start') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="shift_id" class="block text-sm font-medium text-gray-700 text-left">Pilih Shift</label>
                        <select name="shift_id" id="shift_id" class="w-full border rounded-lg px-3 py-2 mt-1" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="initial_cash" class="block text-sm font-medium text-gray-700 text-left">Modal Awal Kasir (Rp)</label>
                        <input type="number" name="initial_cash" id="initial_cash" class="w-full border rounded-lg px-3 py-2 mt-1" required min="0">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                        Mulai Shift & Masuk POS
                    </button>
                </form>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi Select2 untuk pencarian pelanggan
            $('#member_search_select2').select2({
                placeholder: "Cari Member...",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('pos.searchPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.nama + (item.telepon ? ' (' + item.telepon + ')' : ''),
                                    id: item.id,
                                    data: item // Simpan seluruh objek item untuk digunakan nanti
                                }
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2 // Minimal 2 karakter untuk mulai mencari
            });

            // Isi otomatis field pelanggan dari member yang dipilih
            $('#member_search_select2').on('select2:select', function (e) {
                const data = e.params.data.data; // Ambil objek data pelanggan
                $('#nama_pelanggan').val(data.nama || "");
                $('#alamat_pelanggan').val(data.alamat || "");
                $('#telepon_pelanggan').val(data.telepon || "");
                // Hidden input pelanggan_id sudah otomatis terisi oleh Select2
            });

            // Clear form pelanggan jika pilihan member dihapus
            $('#member_search_select2').on('select2:clear', function (e) {
                $('#nama_pelanggan').val("");
                $('#alamat_pelanggan').val("");
                $('#telepon_pelanggan').val("");
                // pelanggan_id akan otomatis kosong
            });

            // Panggil fungsi checkPsikotropikaInCart saat halaman dimuat
            checkPsikotropikaInCart();
            hitungTotal(); // Pastikan total dihitung ulang saat halaman dimuat
        });

        // Fungsi bantu untuk membersihkan angka dari format mata uang
        function cleanNumber(str) {
            return parseInt(str.replace(/[^\d]/g,'')) || 0;
        }

        // Fungsi untuk menghitung total belanja termasuk diskon
        function hitungTotal() {
            let subtotal = 0;
            let totalPpn = 0;

            // Iterasi melalui setiap baris di tabel keranjang
            $('table tbody tr').each(function() {
                // Cek apakah ini bukan baris "Keranjang kosong"
                if ($(this).find('td').length > 1) { 
                    const qty = parseInt($(this).find('input[name^="qty"]').val()) || 0;
                    const hargaText = $(this).find('td:nth-child(5)').text().replace('Rp ', '').replace(/\./g, '');
                    const harga = parseFloat(hargaText) || 0;
                    
                    subtotal += qty * harga;
                    // PPN per item (misal 11%)
                    totalPpn += (harga * 0.11) * qty;
                }
            });

            // Logika diskon
            let diskonValue = parseFloat(document.getElementById('diskon_value').value) || 0;
            let diskonType  = document.getElementById('diskon_type').value;
            let diskonAmount = 0;
            if (diskonType === 'persen') {
                diskonAmount = subtotal * (diskonValue / 100);
            } else {
                diskonAmount = diskonValue;
            }

            let finalTotal = Math.max(subtotal + totalPpn - diskonAmount, 0);

            // Update tampilan
            document.getElementById('subtotal_display').value = "Rp " + subtotal.toLocaleString('id-ID');
            document.getElementById('ppn_display').value = "Rp " + totalPpn.toLocaleString('id-ID');
            document.getElementById('total_display').value = "Rp " + finalTotal.toLocaleString('id-ID');

            // Update hidden inputs
            document.getElementById('total_subtotal').value = subtotal;
            document.getElementById('total_ppn').value = totalPpn;
            document.getElementById('total_hidden').value = finalTotal;
            
            // hitung ulang kembalian juga
            hitungKembalian();
        }

        // Fungsi untuk memformat input bayar dan menghitung kembalian
        function formatBayar() {
            let input = document.getElementById('bayar_display');
            let hidden = document.getElementById('bayar');

            let value = input.value.replace(/\D/g, '');
            if (!value) {
                hidden.value = 0;
                input.value = "";
                hitungKembalian();
                return;
            }

            hidden.value = parseInt(value, 10);
            input.value = 'Rp ' + hidden.value.toLocaleString('id-ID');

            hitungKembalian();
        }

        // Fungsi untuk menghitung kembalian
        function hitungKembalian() {
            let total = parseFloat(document.getElementById('total_hidden').value) || 0;
            let bayar = parseFloat(document.getElementById('bayar').value) || 0;
            let kembalian = bayar - total;

            document.getElementById('kembalian').value = 'Rp ' +
                (kembalian > 0 ? kembalian.toLocaleString('id-ID') : "0");
        }

        // Fungsi untuk memeriksa apakah ada obat psikotropika di keranjang
        function checkPsikotropikaInCart() {
            const ktpInputGroup = document.getElementById('ktp-input-group');
            let hasPsikotropika = false;

            $('table tbody tr').each(function() {
                if ($(this).data('is-psikotropika') === true || $(this).data('is-psikotropika') === 'true') {
                    hasPsikotropika = true;
                    return false; // Keluar dari loop each
                }
            });

            if (hasPsikotropika) {
                ktpInputGroup.style.display = 'grid'; // Tampilkan input KTP
                document.getElementById('no_ktp').setAttribute('required', 'required');
            } else {
                ktpInputGroup.style.display = 'none'; // Sembunyikan input KTP
                document.getElementById('no_ktp').removeAttribute('required');
                document.getElementById('no_ktp').value = ''; // Kosongkan nilai KTP
            }
        }

        // --- AUTOCOMPLETE SEARCH OBAT ---
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
                    })
                    .catch(() => suggestionBox.classList.add('hidden'));
            }

            function updateSelection(items) {
                items.forEach((li, idx) => {
                    li.classList.toggle('bg-blue-100', idx === selectedIndex);
                });
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    items[selectedIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            searchBox.addEventListener('keyup', function (e) {
                const q = this.value.trim();
                const items = suggestionBox.querySelectorAll('li');

                if (items.length > 0) {
                    if (e.key === "ArrowDown") {
                        selectedIndex = (selectedIndex + 1) % items.length;
                        updateSelection(items);
                        e.preventDefault();
                        return;
                    } else if (e.key === "ArrowUp") {
                        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                        updateSelection(items);
                        e.preventDefault();
                        return;
                    } else if (e.key === "Enter") {
                        if (selectedIndex >= 0 && selectedIndex < items.length) {
                            items[selectedIndex].click();
                            e.preventDefault();
                            return;
                        }
                        if (q.length > 0) {
                            searchBox.form.submit();
                            e.preventDefault();
                            return;
                        }
                    }
                }

                clearTimeout(timer);
                if (q.length < 1) {
                    suggestionBox.innerHTML = "";
                    suggestionBox.classList.add('hidden');
                    return;
                }

                timer = setTimeout(() => fetchSuggestions(q), 300);
            });

            document.addEventListener('click', function (e) {
                if (!searchBox.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.innerHTML = "";
                    suggestionBox.classList.add('hidden');
                }
            });
        });
        
        // --- MODAL FUNCTIONS (LIST OBAT) ---
        function openModal() {
            document.getElementById('obatModal').classList.remove('hidden');
            document.getElementById('obatModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('obatModal').classList.remove('flex');
            document.getElementById('obatModal').classList.add('hidden');
        }

        // Tutup modal jika klik di luar konten
        document.getElementById('obatModal').addEventListener('click', function(e) {
            if (e.target.id === 'obatModal') {
                closeModal();
            }
        });

        // --- MODAL SEARCH & SORT (LIST OBAT) ---
        document.getElementById('modal-search').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#obat-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const headerIndex = Array.from(this.parentNode.children).indexOf(this);
                const sortType = this.dataset.sortType;
                let sortDir = this.dataset.sortDir;

                const sortedRows = rows.sort((a, b) => {
                    const aValue = a.children[headerIndex].dataset.value || a.children[headerIndex].textContent;
                    const bValue = b.children[headerIndex].dataset.value || b.children[headerIndex].textContent;

                    let comparison = 0;
                    if (sortType === 'number') {
                        comparison = parseFloat(aValue) - parseFloat(bValue);
                    } else if (sortType === 'date') {
                        comparison = new Date(aValue) - new Date(bValue);
                    }
                    else {
                        comparison = aValue.localeCompare(bValue);
                    }
                    
                    return sortDir === 'asc' ? comparison : -comparison;
                });

                // Update sort direction
                document.querySelectorAll('.sortable').forEach(h => {
                    h.dataset.sortDir = 'asc';
                    h.querySelector('.sort-icon').textContent = '';
                });

                if (sortDir === 'asc') {
                    this.dataset.sortDir = 'desc';
                    this.querySelector('.sort-icon').textContent = ' ▼';
                } else {
                    this.dataset.sortDir = 'asc';
                    this.querySelector('.sort-icon').textContent = ' ▲';
                }
                
                sortedRows.forEach(row => tbody.appendChild(row));
            });
        });

        // --- MODAL TAMBAH PELANGGAN CEPAT ---
        function openAddPelangganModal() {
            document.getElementById('addPelangganModal').classList.remove('hidden');
            document.getElementById('addPelangganModal').classList.add('flex');
        }

        function closeAddPelangganModal() {
            document.getElementById('addPelangganModal').classList.remove('flex');
            document.getElementById('addPelangganModal').classList.add('hidden');
            document.getElementById('add-pelanggan-form').reset(); // Reset form
        }

        document.getElementById('addPelangganModal').addEventListener('click', function(e) {
            if (e.target.id === 'addPelangganModal') {
                closeAddPelangganModal();
            }
        });

        document.getElementById('add-pelanggan-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch("{{ route('pos.addPelangganCepat') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    alert('Pelanggan berhasil ditambahkan!');
                    // Tambahkan pelanggan baru ke Select2 dan pilih
                    const newOption = new Option(data.nama + (data.telepon ? ' (' + data.telepon + ')' : ''), data.id, true, true);
                    $('#member_search_select2').append(newOption).trigger('change');
                    // Isi form checkout dengan data pelanggan baru
                    $('#nama_pelanggan').val(data.nama || "");
                    $('#alamat_pelanggan').val(data.alamat || "");
                    $('#telepon_pelanggan').val(data.telepon || "");
                    closeAddPelangganModal();
                } else {
                    alert('Gagal menambahkan pelanggan: ' + (data.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan pelanggan.');
            });
        });

        // --- SCRIPT AKHIRI SHIFT ---
        function openEndShiftModal() {
            // Ambil total penjualan dari controller (sudah dikirimkan sebagai $totalSales)
            const totalSales = {{ $totalSales ?? 0 }};
            // Ambil modal awal dari shift aktif
            const initialCash = {{ $activeShift->initial_cash ?? 0 }};

            // Hitung modal akhir = modal awal + total penjualan
            const finalCash = initialCash + totalSales;

            // Masukkan nilai ke input modal akhir
            document.getElementById('final_cash').value = finalCash;

            // Tampilkan modal
            document.getElementById('endShiftModal').classList.remove('hidden');
            document.getElementById('endShiftModal').classList.add('flex');
        }

        function closeEndShiftModal() {
            document.getElementById('endShiftModal').classList.remove('flex');
            document.getElementById('endShiftModal').classList.add('hidden');
        }

        document.getElementById('endShiftModal').addEventListener('click', function(e) {
            if (e.target.id === 'endShiftModal') {
                closeEndShiftModal();
            }
        });
    </script>
@endpush
