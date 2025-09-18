<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">💰 Rangkuman Profit</h2>
        <ul class="space-y-2 text-gray-700">
            <li>💵 Total Penjualan: <span class="font-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span></li>
            <li>📦 Total Modal: <span class="font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</span></li>
            <li>🧾 Biaya Operasional: <span class="font-bold">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</span></li>
            <li>💹 Laba Kotor: <span class="font-bold text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span></li>
            <li>✅ Laba Bersih: <span class="font-bold text-blue-600">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span></li>
        </ul>
    </div>
</div>
