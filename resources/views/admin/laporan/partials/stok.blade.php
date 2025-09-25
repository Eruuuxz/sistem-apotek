<div class="bg-white p-6 rounded-lg shadow-md border">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Stok Menipis</h2>
     <table class="min-w-full">
        <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
            <tr>
                <th class="px-4 py-2 text-left">Nama Obat</th>
                <th class="px-4 py-2 text-center">Stok Saat Ini</th>
                <th class="px-4 py-2 text-center">Stok Minimal</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse($stok as $s)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $s->nama }}</td>
                    <td class="px-4 py-3 text-center font-bold text-red-600">{{ $s->stok }}</td>
                    <td class="px-4 py-3 text-center">{{ $s->min_stok }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">Tidak ada data stok menipis.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>