{{-- File: /views/kasir/detail.blade.php --}}
@extends('layouts.kasir')

@section('title', 'Detail Penjualan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Penjualan - {{ $p->no_nota }}</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Informasi Transaksi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <p><strong>No. Nota:</strong> {{ $p->no_nota }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($p->tanggal)->format('d-m-Y H:i:s') }}</p>
                <p><strong>Kasir:</strong> {{ $p->kasir->name ?? '-' }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($p->total, 0, ',', '.') }}</p>
                <p><strong>Bayar:</strong> Rp {{ number_format($p->bayar, 0, ',', '.') }}</p>
                <p><strong>Kembalian:</strong> Rp {{ number_format($p->kembalian, 0, ',', '.') }}</p>
                @if($p->diskon_amount > 0)
                    <p><strong>Diskon:</strong> {{ $p->diskon_type == 'persen' ? $p->diskon_value . '%' : 'Rp ' . number_format($p->diskon_value, 0, ',', '.') }} (Rp {{ number_format($p->diskon_amount, 0, ',', '.') }})</p>
                @endif
            </div>
            <div>
                <h3 class="font-semibold mb-2">Informasi Pelanggan:</h3>
                <p><strong>Nama:</strong> {{ $p->pelanggan->nama ?? $p->nama_pelanggan ?? '-' }}</p>
                <p><strong>Telepon:</strong> {{ $p->pelanggan->telepon ?? $p->telepon_pelanggan ?? '-' }}</p>
                <p><strong>Alamat:</strong> {{ $p->pelanggan->alamat ?? $p->alamat_pelanggan ?? '-' }}</p>
                @if($p->pelanggan)
                    <p><strong>Status Member:</strong> {{ ucfirst($p->pelanggan->status_member) }}</p>
                    <p><strong>Poin Member:</strong> {{ number_format($p->pelanggan->point, 0, ',', '.') }}</p>
                @endif
                @if($p->details->firstWhere('no_ktp'))
                    <p><strong>No. KTP:</strong> {{ $p->details->firstWhere('no_ktp')->no_ktp }}</p>
                @endif
            </div>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-4">Detail Obat</h2>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-sm border-collapse border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-left">No</th>
                    <th class="px-4 py-2 border text-left">Nama Obat</th>
                    <th class="px-4 py-2 border text-center">Qty</th>
                    <th class="px-4 py-2 border text-left">Expired Date</th>
                    <th class="px-4 py-2 border text-right">Harga Satuan</th>
                    <th class="px-4 py-2 border text-right">HPP</th>
                    <th class="px-4 py-2 border text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($p->details ?? [] as $index => $item)
                    <tr>
                        <td class="border px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border px-4 py-2">{{ $item->obat->nama ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $item->qty }}</td>
                        <td class="border px-4 py-2">
                            @if($item->obat->expired_date)
                                {{ \Carbon\Carbon::parse($item->obat->expired_date)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2 text-right">Rp {{ number_format($item->hpp, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-2 text-center text-gray-500">Tidak ada item penjualan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection