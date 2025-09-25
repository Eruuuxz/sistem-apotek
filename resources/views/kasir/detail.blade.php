@extends('layouts.kasir')

@section('title', 'Detail Penjualan')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-4xl mx-auto mt-6">
    <div class="flex justify-between items-start mb-6 pb-6 border-b">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Penjualan</h1>
            <p class="text-sm text-gray-500">No. Nota: <span class="font-semibold">{{ $p->no_nota }}</span></p>
        </div>
        <a href="{{ route('pos.print.options', $p->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6 3.189m0 12.341a60.063 60.063 0 0 1-3.422-.385c-.35-.043-.654-.362-.654-.719V6.626c0-.358.304-.676.654-.72a59.953 59.953 0 0 1 3.422-.385m7.5 0v3.75m-3.75 0v3.75m0-3.75h3.75m-3.75 0h-3.75m3.75 0V6.632c0-.358.304-.676.654-.72a59.953 59.953 0 0 1 3.422-.385m-3.422.385L18 3.189m0 12.341a60.063 60.063 0 0 0 3.422-.385c.35-.043.654-.362.654-.719V6.626c0-.358-.304-.676-.654-.72A59.953 59.953 0 0 0 18 3.189m0 0h-3.75m3.75 0h3.75M12 15.75v3.75m0-3.75v-3.75" /></svg>
            Cetak
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6 text-sm">
        <div>
            <h3 class="font-semibold text-gray-600 mb-2">Informasi Transaksi</h3>
            <div class="space-y-1">
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y, H:i') }}</p>
                <p><strong>Kasir:</strong> {{ $p->kasir->name ?? '-' }}</p>
            </div>
        </div>
        <div>
            <h3 class="font-semibold text-gray-600 mb-2">Informasi Pelanggan</h3>
             <div class="space-y-1">
                <p><strong>Nama:</strong> {{ $p->pelanggan->nama ?? $p->nama_pelanggan ?? 'Umum' }}</p>
                <p><strong>Telepon:</strong> {{ $p->pelanggan->telepon ?? $p->telepon_pelanggan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4">Rincian Pembelian</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Obat</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Harga Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($p->details as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $item->obat->nama ?? 'Obat Dihapus' }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                 <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">Subtotal</td>
                        <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($p->details->sum('subtotal'), 0, ',', '.') }}</td>
                    </tr>
                     @if($p->diskon_amount > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">Diskon</td>
                        <td class="px-4 py-2 text-right font-semibold text-red-600">- Rp {{ number_format($p->diskon_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-800 text-lg">Total Akhir</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600 text-lg">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
     <div class="mt-8 text-right">
        <a href="{{ route('kasir.riwayat') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Kembali ke Riwayat</a>
    </div>
</div>
@endsection