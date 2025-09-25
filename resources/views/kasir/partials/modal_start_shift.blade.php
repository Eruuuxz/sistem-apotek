{{-- Tampilan form "Mulai Shift" jika belum ada shift aktif --}}
<div class="flex items-center justify-center h-full">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm text-center">
        <h2 class="text-2xl font-bold mb-4">Mulai Shift Kasir</h2>
        <div class="text-gray-600 mb-6">Anda harus memulai shift kasir sebelum dapat melakukan transaksi.</div>

        <form method="POST" action="{{ route('shifts.start') }}">
            @csrf
            <div class="mb-4">
                <label for="shift_id" class="block text-sm font-medium text-gray-700 text-left">Pilih Shift</label>
                <select name="shift_id" id="shift_id" class="w-full border rounded-lg px-3 py-2 mt-1" required>
                    <option value="">-- Pilih Shift --</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label for="initial_cash" class="block text-sm font-medium text-gray-700 text-left">Modal Awal Kasir (Rp)</label>
                <input type="number" name="initial_cash" id="initial_cash" class="w-full border rounded-lg px-3 py-2 mt-1" required min="0">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                Mulai Shift & Masuk POS
            </button>
        </form>
    </div>
</div>