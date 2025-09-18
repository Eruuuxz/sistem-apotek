<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">👥 Pelanggan Terbaik</h2>
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Nama Pelanggan</th>
                <th class="px-4 py-2 text-center">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelangganTerbaik as $pelanggan)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $pelanggan->pelanggan->nama ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">{{ $pelanggan->total_transaksi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-2 text-center text-gray-500">Belum ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-gray-700">
        <p>Total Pelanggan: <span class="font-bold">{{ $totalPelanggan }}</span></p>
    </div>
</div>
