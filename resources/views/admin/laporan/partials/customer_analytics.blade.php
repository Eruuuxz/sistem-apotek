<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Rangkuman Metrik --}}
    <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md border space-y-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Ringkasan Pelanggan</h2>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Total Pelanggan Terdaftar</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalPelanggan) }}</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Pelanggan Baru Periode Ini</p>
            <p class="text-2xl font-bold text-teal-600">{{ number_format($pelangganBaru) }}</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Tingkat Retensi</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($tingkatRetensi, 1) }}%</p>
            <p class="text-xs text-gray-500">Dari pelanggan bulan lalu</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Pelanggan Bertransaksi</p>
            <p class="text-2xl font-bold text-green-600">{{ $pelangganTerbaik->count() }}</p>
            <p class="text-xs text-gray-500">Pada periode ini</p>
        </div>
    </div>

    {{-- Kolom Kanan: Pelanggan Terbaik --}}
    <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md border">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Peringkat Pelanggan Terbaik (Berdasarkan Nilai Belanja)</h2>

        {{-- Grafik Pelanggan Terbaik --}}
        <div class="mb-6 h-64 flex items-center justify-center">
            <canvas id="customerChart"></canvas>
        </div>

        {{-- Tabel Pelanggan Terbaik --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-2 text-left">Nama Pelanggan</th>
                        <th class="px-4 py-2 text-center">Total Transaksi</th>
                        <th class="px-4 py-2 text-right">Total Belanja (Rp)</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($pelangganTerbaik as $pelanggan)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">{{ $pelanggan->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $pelanggan->total_transaksi }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700">{{ number_format($pelanggan->total_belanja, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxCustomer = document.getElementById('customerChart');
    if (ctxCustomer) {
        const pelangganData = @json($pelangganTerbaik);
        const top5Pelanggan = pelangganData.slice(0, 5);
        const labels = top5Pelanggan.map(p => p.pelanggan ? p.pelanggan.nama : 'N/A');
        const totals = top5Pelanggan.map(p => p.total_belanja);

        new Chart(ctxCustomer, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Belanja (Rp)',
                    data: totals,
                    backgroundColor: 'rgba(22, 163, 74, 0.5)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Top 5 Pelanggan Berdasarkan Nilai Belanja' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.x !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>