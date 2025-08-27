{{-- File: /kasir/pos.blade.php --}}
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
        <form action="{{ route('pos.add') }}" method="POST" class="mb-4 flex gap-2">
            @csrf
            <input type="text" name="kode" placeholder="Scan / Ketik Kode Obat" class="border px-3 py-2 w-full" autofocus>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
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
                <input type="text" name="kasir_nama" id="kasir_nama" class="w-full border px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                <input type="text" id="total" class="w-full border px-3 py-2 text-right font-bold bg-gray-100" value="Rp {{ number_format($total, 0, ',', '.') }}" readonly>
                <input type="hidden" name="total_hidden" value="{{ $total }}"> {{-- Hidden input untuk total --}}
            </div>
            <div class="mb-3">
                <label for="bayar" class="block text-sm font-medium text-gray-700">Bayar</label>
                <input type="number" name="bayar" id="bayar" class="w-full border px-3 py-2 text-right" oninput="hitungKembalian()" min="{{ $total }}" required>
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
        let total = parseFloat(document.getElementById('total').value.replace(/[^0-9,-]+/g,"").replace(",", ".")) || 0; // Parse total dari format IDR
        let bayar = parseFloat(document.getElementById('bayar').value) || 0;
        let kembalian = bayar - total;
        document.getElementById('kembalian').value = 'Rp ' + kembalian.toLocaleString('id-ID');
    }
</script>
@endpush