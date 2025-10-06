<div class="bg-white p-6 rounded-lg shadow-md border">
    <h3 class="text-xl font-semibold mb-4">
        Pergerakan Stok Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
    </h3>
    <div class="overflow-x-auto" style="max-height: 500px;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Obat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($pergerakanStok as $pergerakan)
                    <tr class="{{ $pergerakan['jenis'] === 'Penjualan' ? 'bg-red-50' : 'bg-green-50' }}">
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pergerakan['tanggal'])->translatedFormat('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $pergerakan['obat_nama'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pergerakan['jenis'] === 'Penjualan')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $pergerakan['jenis'] }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $pergerakan['jenis'] }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-bold {{ $pergerakan['qty'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $pergerakan['qty'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $pergerakan['no_referensi'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pergerakan stok di bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>