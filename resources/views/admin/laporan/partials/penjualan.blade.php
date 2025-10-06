<div class="bg-white p-6 rounded-lg shadow-md border">
    <div class="flex flex-col sm:flex-row justify-between items-start mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Ringkasan Penjualan</h2>
            <p class="text-sm text-gray-500">
                Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
            </p>
        </div>
        
        <div class="flex items-center gap-2 mt-3 sm:mt-0">
            <a href="{{ route('laporan.penjualan.export',['pdf','periode'=>$periode]) }}" 
               class="flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-4 py-2 rounded-lg transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                PDF
            </a>
            <a href="{{ route('laporan.penjualan.export',['excel','periode'=>$periode]) }}" 
               class="flex items-center gap-2 bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-4 py-2 rounded-lg transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Excel
            </a>
        </div>
    </div>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
        <p class="text-gray-600 text-sm">Total Penjualan Periode Ini:</p>
        <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        @if($penjualanBulanLalu > 0)
            <div class="mt-2 flex items-center text-sm {{ $persentasePerubahan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($persentasePerubahan >= 0)
                    <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                    <span>{{ number_format($persentasePerubahan, 2, ',', '.') }}%</span>
                @else
                    <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span>{{ number_format(abs($persentasePerubahan), 2, ',', '.') }}%</span>
                @endif
                <span class="ml-2 text-gray-500 font-normal">dibanding bulan lalu (Rp {{ number_format($penjualanBulanLalu, 0, ',', '.') }})</span>
            </div>
        @endif
    </div>

    <div class="mb-6"><canvas id="penjualanChart"></canvas></div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-center">Jml Transaksi</th>
                    <th class="px-4 py-2 text-center">Jml Obat</th>
                    <th class="px-4 py-2 text-right">Total (Rp)</th>
                    <th class="px-4 py-2 text-center">Aksi</th> {{-- TAMBAH KOLOM AKSI --}}
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($penjualanHarian as $tanggal => $row)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['jumlah_transaksi'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['total_qty'] }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-600">{{ number_format($row['total'], 0, ',', '.') }}</td>
                        {{-- TAMBAH TOMBOL DETAIL --}}
                        <td class="px-4 py-3 text-center">
                            <button onclick="openModal('{{ $tanggal }}')"
                                class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-xs shadow">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data penjualan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ======================================================================= --}}
{{-- ======================== MODAL DAN SCRIPT JS ========================== --}}
{{-- ======================================================================= --}}

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-4xl max-h-full overflow-y-auto p-6 rounded-lg shadow-lg relative">
        <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Detail Transaksi</h2>
        <div id="modalContent" class="text-sm">
            {{-- Isi tabel transaksi harian akan dimasukkan oleh JavaScript --}}
        </div>
    </div>
</div>

@php
    // Siapkan data detail untuk digunakan oleh JavaScript
    $dataForJs = $detailPenjualanHarian->map(function ($rows, $tanggal) {
        return $rows->map(function ($row) {
            return [
                'no_nota' => $row->no_nota,
                'kasir' => $row->user->name ?? 'N/A',
                'pelanggan' => $row->pelanggan->nama ?? 'Umum',
                'waktu' => \Carbon\Carbon::parse($row->tanggal)->format('H:i:s'),
                'total' => $row->total,
                'items' => $row->details->map(fn($d) => $d->obat->nama . ' (' . $d->qty . ')')->implode(', '),
            ];
        });
    });
@endphp

<script>
// Data detail transaksi dari PHP
const dataByDate = @json($dataForJs);

function openModal(tanggal) {
    const transactions = dataByDate[tanggal];
    if (!transactions) {
        console.error('No data found for date:', tanggal);
        return;
    }

    // Set judul modal
    const modalTitle = document.getElementById('modalTitle');
    const formattedDate = new Date(tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    modalTitle.innerText = `Detail Transaksi - ${formattedDate}`;

    // Buat HTML tabel untuk isi modal
    let html = `
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border text-left">Waktu</th>
                        <th class="px-4 py-2 border text-left">No. Nota</th>
                        <th class="px-4 py-2 border text-left">Kasir</th>
                        <th class="px-4 py-2 border text-left">Pelanggan</th>
                        <th class="px-4 py-2 border text-left">Items</th>
                        <th class="px-4 py-2 border text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
    `;

    transactions.forEach(trx => {
        html += `
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">${trx.waktu}</td>
                <td class="border px-4 py-2 font-mono">${trx.no_nota}</td>
                <td class="border px-4 py-2">${trx.kasir}</td>
                <td class="border px-4 py-2">${trx.pelanggan}</td>
                <td class="border px-4 py-2">${trx.items}</td>
                <td class="border px-4 py-2 text-right font-semibold">Rp ${Number(trx.total).toLocaleString('id-ID')}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    // Masukkan HTML ke dalam modal dan tampilkan
    document.getElementById('modalContent').innerHTML = html;
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

// Tambahkan event listener untuk menutup modal saat menekan tombol Escape
document.addEventListener('keydown', function (event) {
    if (event.key === "Escape") {
        closeModal();
    }
});
</script>

{{-- Chart Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('penjualanChart');
    if (ctx) {
        const penjualanData = @json($penjualanHarian);
        const labels = Object.keys(penjualanData).map(tgl => new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }));
        const totals = Object.values(penjualanData).map(row => row.total);
        new Chart(ctx, { type: 'bar', data: { labels: labels, datasets: [{ label: 'Total Penjualan (Rp)', data: totals, backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); } } } }, plugins: { tooltip: { callbacks: { label: function(context) { let label = context.dataset.label || ''; if (label) { label += ': '; } if (context.parsed.y !== null) { label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y); } return label; } } } } } });
    }
});
</script>