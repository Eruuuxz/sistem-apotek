<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Sisi Kiri: Rangkuman Teks --}}
    <div class="bg-white p-6 rounded-lg shadow-md border">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Rangkuman Profit</h2>
        <ul class="space-y-3 text-gray-700">
            <li class="flex justify-between items-center">
                <span>Total Penjualan</span>
                <span class="font-bold text-lg text-green-600">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center">
                <span>Total Modal (HPP)</span>
                <span class="font-bold text-lg text-yellow-600">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center border-t pt-3 mt-3">
                <span class="font-semibold">Laba Kotor</span>
                <span class="font-bold text-lg text-blue-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
            </li>
             <li class="flex justify-between items-center">
                <span>Biaya Operasional</span>
                <span class="font-bold text-lg text-red-600">- Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center border-t-2 border-blue-600 pt-3 mt-3">
                <span class="font-semibold text-xl">Laba Bersih</span>
                <span class="font-bold text-xl text-indigo-700">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
            </li>
        </ul>
    </div>

    {{-- Sisi Kanan: Grafik --}}
    <div class="bg-white p-6 rounded-lg shadow-md border flex items-center justify-center">
        <canvas id="profitChart" style="max-height: 250px;"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxProfit = document.getElementById('profitChart');
    if (ctxProfit) {
        const labaKotor = {{ $labaKotor }};
        const totalModal = {{ $totalModal }};

        new Chart(ctxProfit, {
            type: 'doughnut',
            data: {
                labels: ['Laba Kotor', 'Modal (HPP)'],
                datasets: [{
                    label: 'Komposisi Penjualan',
                    data: [labaKotor, totalModal],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)', // Biru untuk Laba
                        'rgba(245, 158, 11, 0.7)'  // Kuning untuk Modal
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 158, 11, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Komposisi dari Total Penjualan'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
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