<div class="bg-white p-6 rounded-lg shadow-md border">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Pelanggan Terbaik</h2>

    {{-- Grafik Pelanggan Terbaik --}}
    <div class="mb-6">
        <canvas id="customerChart"></canvas>
    </div>

    {{-- Tabel Pelanggan Terbaik --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-2 text-left">Nama Pelanggan</th>
                    <th class="px-4 py-2 text-center">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($pelangganTerbaik as $pelanggan)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $pelanggan->pelanggan->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $pelanggan->total_transaksi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-gray-700 border-t pt-4">
        <p>Total Pelanggan Terdaftar: <span class="font-bold">{{ $totalPelanggan }}</span></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxCustomer = document.getElementById('customerChart');
    if (ctxCustomer) {
        const pelangganData = @json($pelangganTerbaik);
        const labels = pelangganData.map(p => p.pelanggan ? p.pelanggan.nama : 'N/A');
        const totals = pelangganData.map(p => p.total_transaksi);

        new Chart(ctxCustomer, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Transaksi',
                    data: totals,
                    backgroundColor: 'rgba(22, 163, 74, 0.5)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Membuat grafik menjadi horizontal
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Peringkat Pelanggan Berdasarkan Jumlah Transaksi'
                    }
                }
            }
        });
    }
});
</script>