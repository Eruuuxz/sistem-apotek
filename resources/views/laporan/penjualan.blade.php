@extends('layouts.admin')

@section('title','Laporan Penjualan')

@section('content')

{{-- Navigasi + Input Date --}}
<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <a href="{{ route('laporan.penjualan', ['day' => ($offset ?? 0) + 1]) }}" 
       class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">← Hari Sebelumnya</a>

    <form method="GET" class="flex items-center gap-3 bg-white p-3 rounded shadow-md">
        <span class="font-semibold text-gray-700">
            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        </span>
        <input type="date" name="tanggal" value="{{ $tanggal }}" 
               max="{{ now()->toDateString() }}"
               class="border px-3 py-2 rounded">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat</button>
    </form>

    @if(($offset ?? 0) > 0)
        <a href="{{ route('laporan.penjualan', ['day' => ($offset ?? 0) - 1]) }}" 
           class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">Hari Berikutnya →</a>
    @else
        <span class="px-4 py-2 text-gray-400">Hari Berikutnya →</span>
    @endif
</div>

{{-- Ringkasan + Export --}}
<div class="bg-white p-6 shadow-md rounded-xl mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex flex-wrap gap-6 text-gray-700 font-medium">
        <div>Jumlah Transaksi: <span class="font-bold text-blue-600">{{ $jumlahTransaksi }}</span></div>
        <div>Total Penjualan: <span class="font-bold text-green-600">Rp {{ number_format($totalAll,0,',','.') }}</span></div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('laporan.penjualan.pdf', ['tanggal' => $tanggal]) }}" 
           class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 transition">PDF</a>
        <a href="{{ route('laporan.penjualan.excel', ['tanggal' => $tanggal]) }}" 
           class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 transition">Excel</a>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left border-b">No Nota</th>
                <th class="px-4 py-3 text-left border-b">Tanggal</th>
                <th class="px-4 py-3 text-left border-b">Kasir</th>
                <th class="px-4 py-3 text-right border-b">Total</th>
                <th class="px-4 py-3 text-center border-b">Item</th>
                <th class="px-4 py-3 text-center border-b">Qty Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">{{ $row->no_nota }}</td>
                <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i') }}</td>
                <td class="px-4 py-2 border-b">{{ $row->kasir->name ?? '-' }}</td>
                <td class="px-4 py-2 border-b text-right font-medium text-blue-600">Rp {{ number_format($row->total,0,',','.') }}</td>
                <td class="px-4 py-2 border-b text-center">{{ $row->detail_count }}</td>
                <td class="px-4 py-2 border-b text-center">{{ $row->total_qty ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-400">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $data->links() }}
</div>

@endsection
