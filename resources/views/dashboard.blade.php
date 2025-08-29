@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Dashboard</h1>

<!-- Ringkasan Data -->
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Total Obat</h2> {{-- Ubah Total Barang menjadi Total Obat --}}
        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalObat }}</p> {{-- Ubah $totalBarang menjadi $totalObat --}}
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Total Supplier</h2>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalSupplier }}</p>
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Penjualan Hari Ini</h2>
        <p class="text-3xl font-bold text-purple-600 mt-2">Rp {{ number_format($penjualanHariIni,0,',','.') }}</p>
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Stok Menipis</h2>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ $stokMenipis }}</p>
    </div>
</div>

<!-- Grafik -->
<div class="grid grid-cols-2 gap-4">
    <!-- Grafik Penjualan -->
    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600 mb-4">Grafik Penjualan Bulanan</h2>
        <canvas id="penjualanChart" height="100"></canvas>
    </div>

    <!-- Grafik Stok Menipis -->
    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600 mb-4">Stok Obat Menipis</h2> {{-- Ubah Stok Barang Menipis menjadi Stok Obat Menipis --}}
        <canvas id="stokChart" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // data dari controller → ke JS
    const penjualanSeries = @json($penjualanBulanan->pluck('total'));
    const penjualanLabels = @json($penjualanBulanan->pluck('ym')); // format YYYY-MM

    // Chart Penjualan Bulanan
    const ctxPenjualan = document.getElementById('penjualanChart').getContext('2d');
    new Chart(ctxPenjualan, {
        type: 'bar',
        data: {
            labels: penjualanLabels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: penjualanSeries,
                backgroundColor: 'rgba(37, 99, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp '+Number(v).toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    const stokLabel = @json($stokLowList->pluck('nama'));
    const stokData = @json($stokLowList->pluck('stok'));

    // Chart Stok Menipis
    const ctxStok = document.getElementById('stokChart').getContext('2d');
    new Chart(ctxStok, {
        type: 'pie',
        data: {
            labels: stokLabel,
            datasets: [{
                label: 'Jumlah Stok',
                data: stokData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush
