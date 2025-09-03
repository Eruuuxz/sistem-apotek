@extends('layouts.admin')

@section('title','Laporan Penjualan (Bulanan)')

@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan Penjualan (Bulanan)</h1>

{{-- Navigasi + Input Bulan --}}
<div class="flex justify-between items-center mb-4">
    <a href="{{ route('laporan.penjualan.bulanan', ['bulan' => $prevMonth['bulan'], 'tahun' => $prevMonth['tahun']]) }}" 
       class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">← Bulan Sebelumnya</a>

    <form method="GET" class="flex items-center gap-2">
        <span class="font-bold">
            {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}
        </span>
        <input type="month" name="periode" value="{{ $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT) }}" 
               max="{{ now()->format('Y-m') }}"
               class="border px-3 py-2 rounded">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Lihat</button>
    </form>

    @if(!($bulan == now()->month && $tahun == now()->year))
        <a href="{{ route('laporan.penjualan.bulanan', ['bulan' => $nextMonth['bulan'], 'tahun' => $nextMonth['tahun']]) }}" 
           class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Bulan Berikutnya →</a>
    @else
        <span class="px-4 py-2 text-gray-400">Bulan Berikutnya →</span>
    @endif
</div>

{{-- Ringkasan + Export --}}
<div class="bg-white p-4 shadow rounded mb-4 flex justify-between items-center">
    <div>
        <span class="font-semibold">Jumlah Transaksi:</span> 
        <span class="font-bold">{{ $jumlahTransaksi }}</span>
        <span class="ml-6 font-semibold">Total Penjualan:</span> 
        <span class="font-bold">Rp {{ number_format($totalAll,0,',','.') }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('laporan.penjualan.bulanan.pdf', ['bulan'=>$bulan,'tahun'=>$tahun]) }}" 
           class="bg-red-600 text-white px-4 py-2 rounded">PDF</a>
        <a href="{{ route('laporan.penjualan.bulanan.excel', ['bulan'=>$bulan,'tahun'=>$tahun]) }}" 
           class="bg-green-600 text-white px-4 py-2 rounded">Excel</a>
    </div>
</div>

{{-- Tabel --}}
<table class="w-full bg-white shadow rounded text-sm">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-3 py-2">No Nota</th>
            <th class="px-3 py-2">Tanggal</th>
            <th class="px-3 py-2">Kasir</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2 text-center">Item</th>
            <th class="px-3 py-2 text-center">Qty Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td class="border px-3 py-2">{{ $row->no_nota }}</td>
            <td class="border px-3 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
            <td class="border px-3 py-2">{{ $row->kasir->name ?? '-' }}</td>
            <td class="border px-3 py-2 text-right">Rp {{ number_format($row->total,0,',','.') }}</td>
            <td class="border px-3 py-2 text-center">{{ $row->detail_count }}</td>
            <td class="border px-3 py-2 text-center">{{ $row->total_qty ?? 0 }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $data->links() }}</div>
@endsection
