@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <!-- Ringkasan Data -->
    <div class="grid grid-cols-5 gap-4 mb-8">
        <!-- Total Obat -->
        <a href="{{ route('obat.index', ['filter' => 'tersedia']) }}"
            class="bg-white p-4 shadow rounded hover:shadow-md transition block">
            <h2 class="text-lg font-semibold text-gray-600">Obat Tersedia</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalObat }}</p>
        </a>

        <!-- Total Supplier -->
        <a href="{{ route('supplier.index') }}" class="bg-white p-4 shadow rounded hover:bg-green-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Total Supplier</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalSupplier }}</p>
        </a>

        <!-- Penjualan Hari Ini -->
        <a href="{{ route('laporan.penjualan') }}" class="bg-white p-4 shadow rounded hover:bg-purple-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Penjualan Hari Ini</h2>
            <p class="text-3xl font-bold text-purple-600 mt-2">
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </p>
        </a>

        <!-- Stok Menipis -->
        <a href="{{ route('obat.index', ['filter' => 'menipis']) }}"
            class="bg-white p-4 shadow rounded hover:bg-yellow-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Stok Menipis</h2>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stokMenipis }}</p>
        </a>

        <!-- Stok Habis -->
        <a href="{{ route('obat.index', ['filter' => 'habis']) }}"
            class="bg-white p-4 shadow rounded hover:bg-red-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Stok Habis</h2>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $stokHabis }}</p>
        </a>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-2 gap-4">
        <!-- Grafik Penjualan -->
        <div class="bg-white p-6 shadow rounded">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Grafik Penjualan Bulanan</h2>
            <canvas id="penjualanChart" height="100"></canvas>
        </div>

        <!-- Grafik Stok Menipis -->
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold text-gray-600">Obat Terlaris</h2>
            <canvas id="obatTerlarisChart" height="200"></canvas>
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
                            callback: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        const ctxObatTerlaris = document.getElementById('obatTerlarisChart').getContext('2d');
        new Chart(ctxObatTerlaris, {
            type: 'bar', // bisa ganti 'pie' kalau mau
            data: {
                labels: @json($obatTerlaris->pluck('nama')),
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: @json($obatTerlaris->pluck('total_terjual')),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
@endpush