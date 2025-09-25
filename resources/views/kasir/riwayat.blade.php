@extends('layouts.kasir')

@section('title', 'Riwayat Penjualan')

@section('content')
    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Penjualan</h2>
                <p class="text-sm text-gray-500">Daftar transaksi penjualan yang telah dilakukan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No Nota</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($data as $row)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                            <td class="px-4 py-3 font-medium">{{ $row->no_nota }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3">{{ $row->kasir->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $row->pelanggan->nama ?? $row->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('penjualan.show', $row->id) }}" class="text-blue-600 hover:underline font-semibold">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                <p>Tidak ada data penjualan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
@endsection

