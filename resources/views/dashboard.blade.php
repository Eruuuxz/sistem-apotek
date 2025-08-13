@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Dashboard</h1>

<!-- Ringkasan Data -->
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Total Barang</h2>
        <p class="text-3xl font-bold text-blue-600 mt-2">120</p>
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Total Supplier</h2>
        <p class="text-3xl font-bold text-green-600 mt-2">15</p>
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Penjualan Hari Ini</h2>
        <p class="text-3xl font-bold text-purple-600 mt-2">Rp 1.500.000</p>
    </div>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold text-gray-600">Stok Menipis</h2>
        <p class="text-3xl font-bold text-red-600 mt-2">8</p>
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
        <h2 class="text-lg font-semibold text-gray-600 mb-4">Stok Obat Menipis</h2>
        <canvas id="stokChart" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Penjualan Bulanan
    const ctxPenjualan = document.getElementById('penjualanChart').getContext('2d');
    const penjualanChart = new Chart(ctxPenjualan, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu'],
            datasets: [{
                label: 'Penjualan (Rp)',
                data: [1500000, 1800000, 1200000, 2000000, 2500000, 2100000, 3000000, 2800000],
                backgroundColor: 'rgba(37, 99, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Chart Stok Menipis
    const ctxStok = document.getElementById('stokChart').getContext('2d');
    const stokChart = new Chart(ctxStok, {
        type: 'pie',
        data: {
            labels: ['Paracetamol', 'Amoxicillin', 'Vitamin C', 'Ibuprofen', 'CTM'],
            datasets: [{
                label: 'Jumlah Stok',
                data: [5, 3, 2, 4, 1],
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
