{{-- Banner informasi sesi --}}
<div class="alert bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 flex justify-between items-center text-sm">
    <div>
        <span class="font-semibold">Sesi Aktif</span> | 
        Modal Awal: <strong class="text-green-800">Rp {{ number_format($initialCash, 0, ',', '.') }}</strong> | 
        Total Penjualan Hari Ini: <strong class="text-green-800">Rp {{ number_format($totalSalesToday, 0, ',', '.') }}</strong>
    </div>
    <form action="{{ route('pos.clearInitialCash') }}" method="POST">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-xs font-bold" onclick="return confirm('Apakah Anda yakin ingin keluar dari sesi kasir? Anda harus memasukkan modal awal lagi untuk masuk.')">
            LOGOUT SESI
        </button>
    </form>
</div>

{{-- Grid utama POS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {{-- Kiri: Pencarian dan Tabel Keranjang --}}
    @include('kasir.partials.cart_table')

    {{-- Kanan: Ringkasan Pembayaran --}}
    @include('kasir.partials.payment_summary')
</div>