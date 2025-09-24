<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md border">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Rangkuman Profit</h2>
        <ul class="space-y-3 text-gray-700">
            <li class="flex justify-between items-center">
                <span>Total Penjualan</span>
                <span class="font-bold text-lg">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center">
                <span>Total Modal (HPP)</span>
                <span class="font-bold text-lg">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center border-t pt-3 mt-3">
                <span class="font-semibold">Laba Kotor</span>
                <span class="font-bold text-lg text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
            </li>
             <li class="flex justify-between items-center">
                <span>Biaya Operasional</span>
                <span class="font-bold text-lg text-red-600">- Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</span>
            </li>
            <li class="flex justify-between items-center border-t pt-3 mt-3">
                <span class="font-semibold text-xl">Laba Bersih</span>
                <span class="font-bold text-xl text-blue-600">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
            </li>
        </ul>
    </div>
</div>