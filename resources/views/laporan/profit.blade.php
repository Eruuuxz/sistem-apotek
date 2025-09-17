@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi Bulanan')

@section('content')

{{-- Navigasi Bulan --}}
<div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-2">
    {{-- Bulan Sebelumnya --}}
    <a href="{{ route('laporan.profit', ['periode' => $prevMonth['tahun'].'-'.str_pad($prevMonth['bulan'],2,'0',STR_PAD_LEFT)]) }}"
       class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">← Bulan Sebelumnya</a>

    {{-- Form pilih bulan --}}
    <form method="GET" class="flex items-center gap-2">
        <span class="font-bold">{{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</span>
        <input type="month" name="periode" value="{{ $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
               max="{{ now()->format('Y-m') }}" class="border px-3 py-2 rounded">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat</button>
    </form>

    {{-- Bulan Berikutnya --}}
    @if(!($bulan == now()->month && $tahun == now()->year))
        <a href="{{ route('laporan.profit', ['periode' => $nextMonth['tahun'].'-'.str_pad($nextMonth['bulan'],2,'0',STR_PAD_LEFT)]) }}"
           class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">Bulan Berikutnya →</a>
    @else
        <span class="px-4 py-2 text-gray-400">Bulan Berikutnya →</span>
    @endif
</div>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Total Penjualan (Subtotal)</span>
        <span class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalPenjualan,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Total harga jual tanpa PPN</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Total Modal (HPP)</span>
        <span class="text-2xl font-bold text-orange-500 mt-2">Rp {{ number_format($totalModal,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Total biaya modal semua barang terjual</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">PPN Penjualan</span>
        <span class="text-2xl font-bold text-teal-600 mt-2">Rp {{ number_format($totalPpnPenjualan,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Jumlah PPN dari semua transaksi</p>
    </div>

    <div class="bg-white shadow-md rounded p-4 flex flex-col items-center">
        <span class="text-gray-500 text-sm">Biaya Operasional</span>
        <span class="text-2xl font-bold text-red-600 mt-2">Rp {{ number_format($totalBiayaOperasional,0,',','.') }}</span>
        <p class="text-xs text-gray-400 mt-1">Jumlah total biaya operasional bulan ini</p>
    </div>
</div>

{{-- Ringkasan Laba --}}
<div class="bg-white shadow-md rounded p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4 border-b pb-2">Ringkasan Laba ({{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }})</h3>
    <ul class="space-y-4">
        <li>
            <span class="font-medium text-gray-700">Laba Kotor (Penjualan - HPP):</span>
            <span class="float-right text-lg font-bold text-green-700">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
        </li>
        <li class="border-t pt-4">
            <span class="text-xl font-bold">Laba Bersih:</span>
            <span class="float-right text-xl font-bold {{ $labaBersih >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
        </li>
    </ul>
</div>

{{-- Grafik (menggunakan Chart.js) --}}
<div class="bg-white shadow-md rounded p-4 mb-6">
    <canvas id="profitChart" height="100"></canvas>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Penjualan', 'Total Modal', 'Laba Kotor', 'Biaya Operasional', 'Laba Bersih'],
            datasets: [{
                label: 'Rp',
                data: [
                    {{ $totalPenjualan ?? 0 }}, 
                    {{ $totalModal ?? 0 }}, 
                    {{ $labaKotor ?? 0 }}, 
                    {{ $totalBiayaOperasional ?? 0 }}, 
                    {{ $labaBersih ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(249, 115, 22, 0.7)',
                    'rgba(22, 163, 74, 0.7)',
                    'rgba(220, 38, 38, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(249, 115, 22, 1)',
                    'rgba(22, 163, 74, 1)',
                    'rgba(220, 38, 38, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush