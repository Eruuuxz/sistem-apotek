{{-- Modal Akhiri Shift --}}
<div id="endShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl shadow-lg relative w-full max-w-sm">
        <button onclick="closeEndShiftModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg font-bold">✕</button>
        <h2 class="text-xl font-semibold mb-4">Konfirmasi Akhiri Shift</h2>
        <form action="{{ route('shifts.end') }}" method="POST" class="space-y-4">
            @csrf
            <div class="mb-4">
                <p class="text-gray-700 text-sm">Shift aktif: <strong>{{ $activeShift->shift->name }}</strong> dimulai pada {{ $activeShift->start_time }}.</p>
            </div>
            <div>
                <label for="final_cash_display" class="block text-sm font-medium text-gray-700">Modal Akhir Kasir (Rp)</label>
                <input type="text" id="final_cash_display" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                <input type="hidden" name="final_cash" id="final_cash">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition">
                Akhiri Shift & Keluar
            </button>
        </form>
    </div>
</div>