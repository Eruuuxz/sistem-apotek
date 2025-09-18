<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">📦 Stok Menipis</h2>
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Nama Obat</th>
                <th class="px-4 py-2 text-center">Stok</th>
                <th class="px-4 py-2 text-center">Minimal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stok as $s)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $s->nama }}</td>
                    <td class="px-4 py-2 text-center">{{ $s->stok }}</td>
                    <td class="px-4 py-2 text-center">{{ $s->min_stok }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">Tidak ada data stok menipis</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
