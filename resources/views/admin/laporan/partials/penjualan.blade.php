<div class="bg-white p-6 rounded-lg shadow-md border">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Ringkasan Penjualan</h2>

    <div class="mb-6">
        <canvas id="penjualanChart"></canvas>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-center">Jumlah Transaksi</th>
                    <th class="px-4 py-2 text-center">Jumlah Obat</th>
                    <th class="px-4 py-2 text-right">Total (Rp)</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($penjualanHarian as $tanggal => $row)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['jumlah_transaksi'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['total_qty'] }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-600">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada data penjualan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('penjualanChart');
    if (ctx) {
        const penjualanData = @json($penjualanHarian);
        const labels = Object.keys(penjualanData).map(tgl => new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }));
        const totals = Object.values(penjualanData).map(row => row.total);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: totals,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>