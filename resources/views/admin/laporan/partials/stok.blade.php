<div class="bg-white p-6 rounded-lg shadow-md border">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Stok Menipis</h2>
        <span class="text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </span>
    </div>

    <div class="space-y-4">
        @forelse($stok as $s)
            <div class="p-3 rounded-lg border hover:bg-gray-50">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-700">{{ $s->nama }}</span>
                    <span class="text-sm font-bold {{ $s->stok > $s->min_stok ? 'text-yellow-600' : 'text-red-600' }}">
                        Stok: {{ $s->stok }} / Min: {{ $s->min_stok }}
                    </span>
                </div>
                {{-- Progress Bar Sederhana --}}
                @php
                    $percentage = ($s->stok / ($s->min_stok * 2)) * 100; // Asumsi batas atas adalah 2x min_stok
                    if ($percentage > 100) $percentage = 100;
                    $bgColor = $percentage < 50 ? 'bg-red-500' : 'bg-yellow-500';
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="{{ $bgColor }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        @empty
            <div class="text-center py-6 text-gray-500">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="mt-2">Tidak ada data stok menipis. Semua aman!</p>
            </div>
        @endforelse
    </div>
</div>