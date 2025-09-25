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
                <button type="button" onclick="openAddPelangganModal()"
                    class="bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                    + Baru
                </button>
            </div>
        </div>

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

        {{-- Input KTP (hanya muncul jika ada psikotropika) --}}
        <div id="ktp-input-group" class="grid grid-cols-3 items-start gap-2" style="display: none;">
            <label for="no_ktp" class="text-sm font-medium text-gray-700 mt-2">No. KTP <span
                    class="text-red-600">*</span></label>
            <div class="col-span-2">
                <input type="text" id="no_ktp" name="no_ktp" class="w-full border rounded-lg px-3 py-2"
                    placeholder="Nomor KTP Pelanggan" value="{{ old('no_ktp') }}">
                @error('no_ktp')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Total, Diskon, PPN --}}
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
                    class="w-full border rounded-lg px-3 py-2 text-right" placeholder="0"
                    value="{{ old('diskon_value', $diskonValue) }}" oninput="hitungTotal()">
                <select id="diskon_type" name="diskon_type" class="border rounded-lg px-2 py-2"
                    onchange="hitungTotal()">
                    <option value="nominal" {{ old('diskon_type', $diskonType) == 'nominal' ? 'selected' : '' }}>Rp
                    </option>
                    <option value="persen" {{ old('diskon_type', $diskonType) == 'persen' ? 'selected' : '' }}>%
                    </option>
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

        {{-- Pembayaran dan Kembalian --}}
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