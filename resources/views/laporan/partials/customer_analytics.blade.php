<div class="bg-white p-6 rounded-lg shadow-md border">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Pelanggan Terbaik</h2>
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

    <div class="mt-4 text-gray-700 border-t pt-4">
        <p>Total Pelanggan Terdaftar: <span class="font-bold">{{ $totalPelanggan }}</span></p>
    </div>
</div>
