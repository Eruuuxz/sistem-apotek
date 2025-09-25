@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Ringkasan Data -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-6 rounded-xl shadow-lg text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium opacity-80">Penjualan Hari Ini</p>
                    <p class="text-3xl font-bold mt-1">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/20 p-2 rounded-full">
                     <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                </div>
            </div>
        </div>
        <a href="{{ route('obat.index', ['filter' => 'menipis']) }}" class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stokMenipis ?? 0 }}</p>
            </div>
             <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            </div>
        </a>
        <a href="{{ route('obat.index', ['filter' => 'habis']) }}" class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Habis</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $stokHabis ?? 0 }}</p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
        </a>
         <a href="{{ route('obat.index', ['filter' => 'kadaluarsa']) }}" class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-sm font-medium text-gray-500">Akan Kadaluarsa</p>
                <p class="text-3xl font-bold text-orange-600 mt-1">{{ $obatHampirExpired ?? 0 }}</p>
            </div>
             <div class="bg-orange-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
        </a>
    </div>

    <!-- Grafik & Statistik Umum -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Penjualan 7 Hari Terakhir</h2>
            <canvas id="penjualanHarianChart" height="120"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Obat Terlaris Bulan Ini</h2>
            <canvas id="obatTerlarisChart" height="250"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data penjualan harian
        const harianLabels = @json($penjualanHarian->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')) ?? []);
        const harianData = @json($penjualanHarian->pluck('total') ?? []);
        const ctxHarian = document.getElementById('penjualanHarianChart').getContext('2d');
        const gradientHarian = ctxHarian.createLinearGradient(0, 0, 0, 300);
        gradientHarian.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        gradientHarian.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctxHarian, {
            type: 'line',
            data: {
                labels: harianLabels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: harianData,
                    fill: true,
                    backgroundColor: gradientHarian,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') }
                    }
                }
            }
        });

        // Data obat terlaris
        const obatLabels = @json($obatTerlaris->pluck('nama') ?? []);
        const obatData = @json($obatTerlaris->pluck('total_terjual') ?? []);
        const ctxObat = document.getElementById('obatTerlarisChart').getContext('2d');

        new Chart(ctxObat, {
            type: 'bar',
            data: {
                labels: obatLabels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: obatData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 4,
                    hoverBackgroundColor: 'rgba(5, 150, 105, 0.9)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } }
            }
        });
    });
</script>
@endpush