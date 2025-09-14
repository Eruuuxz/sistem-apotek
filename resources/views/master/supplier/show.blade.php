@extends('layouts.admin')

@section('title', 'Detail Supplier: ' . $supplier->nama)

@section('content')
    <div class="bg-white p-8 shadow-xl rounded-xl max-w-full mx-auto mt-6">
        <h2 class="text-2xl font-bold mb-6">Detail Supplier: {{ $supplier->nama }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-lg font-semibold mb-2">Informasi Supplier</h3>
                <p><strong>Kode:</strong> {{ $supplier->kode }}</p>
                <p><strong>Nama:</strong> {{ $supplier->nama }}</p>
                <p><strong>Alamat:</strong> {{ $supplier->alamat ?? '-' }}</p>
                <p><strong>Kota:</strong> {{ $supplier->kota ?? '-' }}</p>
                <p><strong>Telepon:</strong> {{ $supplier->telepon ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">Obat yang Disuplai</h3>
                @if($supplier->obat->isEmpty())
                    <p class="text-gray-500">Tidak ada obat yang disuplai oleh supplier ini.</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach($supplier->obat as $obat)
                            <li>{{ $obat->nama }} (Stok: {{ $obat->stok }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Riwayat Surat Pesanan --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Riwayat Surat Pesanan</h3>
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">No SP</th>
                            <th class="px-4 py-2 text-left">Tanggal SP</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-left">Obat (Qty Pesan / Qty Terima)</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSuratPesanan as $sp)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-3">{{ $sp->no_sp }}</td>
                                <td class="border px-4 py-3">{{ $sp->tanggal_sp->format('d-m-Y H:i') }}</td>
                                <td class="border px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($sp->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($sp->status == 'parsial') bg-blue-100 text-blue-800
                                        @elseif($sp->status == 'selesai') bg-green-100 text-green-800
                                        @elseif($sp->status == 'dibatalkan') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($sp->status) }}
                                    </span>
                                </td>
                                <td class="border px-4 py-3">
                                    @foreach($sp->details as $detail)
                                        {{ $detail->obat->nama ?? '-' }} ({{ $detail->qty_pesan }} / {{ $detail->qty_terima }})<br>
                                    @endforeach
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <a href="{{ route('surat_pesanan.show', $sp->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border px-4 py-3 text-center text-gray-500">Tidak ada riwayat Surat Pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $riwayatSuratPesanan->links('pagination::tailwind', ['pagination_name' => 'sp_page']) }}
            </div>
        </div>

        {{-- Riwayat Pembelian --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Riwayat Pembelian</h3>
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">No Faktur</th>
                            <th class="px-4 py-2 text-left">No Faktur PBF</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Total</th>
                            <th class="px-4 py-2 text-left">Obat (Jumlah)</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPembelian as $pembelian)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-3">{{ $pembelian->no_faktur }}</td>
                                <td class="border px-4 py-3">{{ $pembelian->no_faktur_pbf ?? '-' }}</td>
                                <td class="border px-4 py-3">{{ $pembelian->tanggal->format('d-m-Y H:i') }}</td>
                                <td class="border px-4 py-3 text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                                <td class="border px-4 py-3">
                                    @foreach($pembelian->detail as $detail)
                                        {{ $detail->obat->nama ?? '-' }} ({{ $detail->jumlah }})<br>
                                    @endforeach
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <a href="{{ route('pembelian.faktur', $pembelian->id) }}" class="text-blue-600 hover:underline">Faktur</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-3 text-center text-gray-500">Tidak ada riwayat pembelian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $riwayatPembelian->links('pagination::tailwind', ['pagination_name' => 'pembelian_page']) }}
            </div>
        </div>

        {{-- Riwayat Retur Pembelian --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Riwayat Retur Pembelian</h3>
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">No Retur</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Total Retur</th>
                            <th class="px-4 py-2 text-left">Obat (Jumlah)</th>
                            <th class="px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatRetur as $retur)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-3">{{ $retur->no_retur }}</td>
                                <td class="border px-4 py-3">{{ $retur->tanggal->format('d-m-Y H:i') }}</td>
                                <td class="border px-4 py-3 text-right">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                                <td class="border px-4 py-3">
                                    @foreach($retur->details as $detail)
                                        {{ $detail->obat->nama ?? '-' }} ({{ $detail->qty }})<br>
                                    @endforeach
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <a href="{{ route('retur.show', $retur->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border px-4 py-3 text-center text-gray-500">Tidak ada riwayat retur pembelian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $riwayatRetur->links('pagination::tailwind', ['pagination_name' => 'retur_page']) }}
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <a href="{{ route('supplier.index') }}" class="bg-gray-400 text-white px-6 py-3 rounded-md hover:bg-gray-500">Kembali</a>
        </div>
    </div>
@endsection