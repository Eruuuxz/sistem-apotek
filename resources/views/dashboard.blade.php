@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Ringkasan Data -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Obat Tersedia -->
        <a href="{{ route('obat.index', ['filter' => 'tersedia']) }}"
            class="bg-white p-4 shadow rounded hover:shadow-md transition block">
            <h2 class="text-lg font-semibold text-gray-600">Obat Tersedia</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalObat }}</p>
        </a>

        <!-- Total Supplier -->
        <a href="{{ route('supplier.index') }}"
            class="bg-white p-4 shadow rounded hover:bg-green-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Total Supplier</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalSupplier }}</p>
        </a>

        <!-- Penjualan Hari Ini -->
        <a href="{{ route('laporan.penjualan') }}"
            class="bg-white p-4 shadow rounded hover:bg-purple-50 transition">
            <h2 class="text-lg font-semibold text-gray-600">Penjualan Hari Ini</h2>
            <p class="text-3xl font-bold text-purple-600 mt-2">
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </p>
        </a>

        <!-- Stok Menipis -->
        <a href="{{ route('obat.index', ['filter' => 'menipis']) }}"
            class="bg-white p-4 shadow rounded hover:bg-yellow-50 transition flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-600">Stok Menipis</h2>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stokMenipis }}</p>
            </div>
            @if($stokMenipis > 0)
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">Perhatian</span>
            @endif
        </a>

        <!-- Stok Habis -->
        <a href="{{ route('obat.index', ['filter' => 'habis']) }}"
            class="bg-white p-4 shadow rounded hover:bg-red-50 transition flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-600">Stok Habis</h2>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $stokHabis }}</p>
            </div>
            @if($stokHabis > 0)
                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-semibold">Segera Order</span>
            @endif
        </a>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Penjualan -->
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Grafik Penjualan Bulanan</h2>
            <canvas id="penjualanChart" height="100"></canvas>
        </div>

        <!-- Grafik Obat Terlaris -->
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Obat Terlaris</h2>
            <canvas id="obatTerlarisChart" height="200"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data penjualan
    const penjualanSeries = @json($penjualanBulanan->pluck('total'));
    const penjualanLabels = @json($penjualanBulanan->pluck('ym'));

    const ctxPenjualan = document.getElementById('penjualanChart').getContext('2d');
    const gradientPenjualan = ctxPenjualan.createLinearGradient(0, 0, 0, 400);
    gradientPenjualan.addColorStop(0, 'rgba(37, 99, 235, 0.8)');
    gradientPenjualan.addColorStop(1, 'rgba(37, 99, 235, 0.3)');

    new Chart(ctxPenjualan, {
        type: 'bar',
        data: {
            labels: penjualanLabels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: penjualanSeries,
                backgroundColor: gradientPenjualan,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') } }
            }
        }
    });

    // Data obat terlaris
    const obatLabels = @json($obatTerlaris->pluck('nama'));
    const obatData = @json($obatTerlaris->pluck('total_terjual'));

    const ctxObat = document.getElementById('obatTerlarisChart').getContext('2d');
    const gradientObat = ctxObat.createLinearGradient(0, 0, 0, 400);
    gradientObat.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
    gradientObat.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

    new Chart(ctxObat, {
        type: 'bar',
        data: {
            labels: obatLabels,
            datasets: [{
                label: 'Jumlah Terjual',
                data: obatData,
                backgroundColor: gradientObat,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endpush
