@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Ringkasan Data -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Penjualan Hari Ini -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 p-4 rounded-xl shadow text-white">
            <p class="text-xs opacity-80">Penjualan Hari Ini</p>
            <h3 class="text-lg font-bold">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-yellow-100 p-4 rounded-xl shadow">
            <p class="text-xs text-yellow-700">Stok Menipis</p>
            <h3 class="text-lg font-bold text-yellow-600">{{ $stokMenipis }}</h3>
        </div>

        <!-- Stok Habis -->
        <div class="bg-red-100 p-4 rounded-xl shadow">
            <p class="text-xs text-red-700">Stok Habis</p>
            <h3 class="text-lg font-bold text-red-600">{{ $stokHabis }}</h3>
        </div>

        <!-- Hampir Expired -->
        <div class="bg-orange-100 p-4 rounded-xl shadow">
            <p class="text-xs text-orange-700">Hampir Expired</p>
            <h3 class="text-lg font-bold text-orange-600">{{ $obatHampirExpired }}</h3>
        </div>

        <!-- Obat Tersedia -->
        <div class="bg-blue-100 p-4 rounded-xl shadow">
            <p class="text-xs text-blue-700">Obat Tersedia</p>
            <h3 class="text-lg font-bold text-blue-600">{{ $totalObat }}</h3>
        </div>

        <!-- Supplier -->
        <div class="bg-gray-100 p-4 rounded-xl shadow">
            <p class="text-xs text-gray-700">Total Supplier</p>
            <h3 class="text-lg font-bold text-gray-800">{{ $totalSupplier }}</h3>
        </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Penjualan -->
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-sm font-semibold text-gray-700 mb-2">📅 Penjualan 7 Hari Terakhir</h2>
            <canvas id="penjualanHarianChart" height="180"></canvas>
        </div>

        <!-- Obat Terlaris -->
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-sm font-semibold text-gray-700 mb-2">🔥 Obat Terlaris Bulan Ini</h2>
            <canvas id="obatTerlarisChart" height="180"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Penjualan Harian
    const harianLabels = @json($penjualanHarian->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
    const harianData = @json($penjualanHarian->pluck('total'));
    new Chart(document.getElementById('penjualanHarianChart'), {
        type: 'line',
        data: {
            labels: harianLabels,
            datasets: [{
                data: harianData,
                borderColor: 'rgba(99,102,241,1)',
                backgroundColor: 'rgba(99,102,241,0.2)',
                fill: true,
                tension: 0.4,
                pointRadius: 3
            }]
        },
        options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });

    // Obat Terlaris
    const obatLabels = @json($obatTerlaris->pluck('nama'));
    const obatData = @json($obatTerlaris->pluck('total_terjual'));
    new Chart(document.getElementById('obatTerlarisChart'), {
        type: 'bar',
        data: {
            labels: obatLabels,
            datasets: [{ data: obatData, backgroundColor: 'rgba(16,185,129,0.7)', borderRadius: 4 }]
        },
        options: { plugins:{legend:{display:false}}, indexAxis:'y', scales:{x:{beginAtZero:true}} }
    });
</script>
@endpush
