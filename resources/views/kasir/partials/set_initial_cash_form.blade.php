<div class="flex items-center justify-center min-h-[calc(100vh-100px)] bg-gray-50">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm text-center">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Mulai Sesi Kasir</h2>
        <p class="text-gray-600 mb-6">Masukkan jumlah uang tunai (modal) yang ada di laci kasir untuk memulai.</p>

        <form method="POST" action="{{ route('pos.setInitialCash') }}">
            @csrf
            <div class="mb-6">
                <label for="initial_cash" class="block text-sm font-medium text-gray-700 text-left mb-2">Modal Awal Kasir (Rp)</label>
                <input type="number" name="initial_cash" id="initial_cash"
                    class="w-full border rounded-lg px-3 py-2 text-lg text-center" required min="0" placeholder="0" autofocus>
                @error('initial_cash')
                    <p class="text-red-500 text-sm mt-1 text-left">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-medium transition duration-300">
                Mulai Sesi
            </button>
        </form>
    </div>
</div>