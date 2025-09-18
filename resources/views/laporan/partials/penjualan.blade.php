<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">📈 Ringkasan Penjualan</h2>
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-center">Jumlah Transaksi</th>
                <th class="px-4 py-2 text-center">Jumlah Obat</th>
                <th class="px-4 py-2 text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualanHarian as $tanggal => $row)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</td>
                    <td class="px-4 py-2 text-center">{{ $row['jumlah_transaksi'] }}</td>
                    <td class="px-4 py-2 text-center">{{ $row['total_qty'] }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($row['total'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
