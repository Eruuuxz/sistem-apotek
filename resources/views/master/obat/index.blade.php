@extends('layouts.admin')

@section('title', 'Daftar Obat')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 shadow rounded-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex gap-3">
                @if(request('filter'))
                    <a href="{{ route('obat.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition flex items-center gap-1">
                        Reset Filter
                    </a>
                @endif
            </div>

            <input type="text" id="searchInput" placeholder="Cari obat..."
                class="border px-3 py-2 w-full md:w-64 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(0)">Kode</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(1)">Nama</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(2)">Kategori</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(3)">Stok</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(4)">Harga Dasar</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(5)">Untung (%)</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(6)">Harga Jual</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(7)">Keuntungan/Unit</th>
                        <th class="px-4 py-3 cursor-pointer" onclick="sortTable(8)">Supplier</th>
                        <th class="px-2 py-1 cursor-pointer" onclick="sortTable(9)">Kadaluarsa</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel_obat">
                    @foreach ($obats as $obat)
                        <tr
                            class="hover:bg-gray-50 transition
                                                @if($obat->stok == 0) bg-red-50 @elseif($obat->stok > 0 && $obat->stok < 10) bg-yellow-50 @endif">
                            <td class="border px-4 py-3">{{ $obat->kode }}</td>
                            <td class="border px-4 py-3">{{ $obat->nama }}</td>
                            <td class="border px-4 py-3">{{ $obat->kategori }}</td>
                            <td class="border px-4 py-3 text-right">
                                {{ $obat->stok }}
                                @if($obat->stok == 0)
                                    <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded-full">Habis</span>
                                @elseif($obat->stok > 0 && $obat->stok < 10)
                                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded-full">Menipis</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-right">Rp {{ number_format($obat->harga_dasar, 0, ',', '.') }}</td>
                            <td class="border px-4 py-3 text-right">{{ $obat->persen_untung }}%</td>
                            <td class="border px-4 py-3 text-right">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                            <td class="border px-4 py-3 text-right">Rp
                                {{ number_format($obat->harga_jual - $obat->harga_dasar, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-3">{{ $obat->supplier->nama ?? '-' }}</td>
                            <td class="border px-2 py-1">
                                {{ $obat->expired_date ? \Carbon\Carbon::parse($obat->expired_date)->format('d-m-Y') : '-' }} <!-- <-- BARU -->
                            </td>
                            <td class="border px-4 py-3 flex gap-2">
                                <a href="{{ route('obat.edit', $obat->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-500 hover:underline delete-btn">Hapus</button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Popup Konfirmasi -->
    <div id="confirm-popup" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-80 text-center">
            <h2 class="text-lg font-semibold mb-4">Yakin ingin menghapus data?</h2>
            <div class="flex justify-center gap-4">
                <button id="cancel-btn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
                <button id="confirm-btn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const popup = document.getElementById("confirm-popup");
            const cancelBtn = document.getElementById("cancel-btn");
            const confirmBtn = document.getElementById("confirm-btn");

            let formToSubmit = null;

            // Saat klik tombol hapus
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    formToSubmit = this.closest("form");
                    popup.classList.remove("hidden");
                    popup.classList.add("flex");
                });
            });

            // Klik batal
            cancelBtn.addEventListener("click", () => {
                popup.classList.add("hidden");
                popup.classList.remove("flex");
                formToSubmit = null;
            });

            // Klik hapus
            confirmBtn.addEventListener("click", () => {
                if (formToSubmit) formToSubmit.submit();
            });
        });
    </script>

    <script>
        let sortDirection = {};
        function sortTable(colIndex) {
            const table = document.querySelector("table");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.querySelectorAll("tr"));
            sortDirection[colIndex] = !sortDirection[colIndex];

            rows.sort((a, b) => {
                let valA = a.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g, "").toLowerCase();
                let valB = b.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g, "").toLowerCase();
                let numA = parseFloat(valA) || valA;
                let numB = parseFloat(valB) || valB;
                return (numA < numB ? -1 : numA > numB ? 1 : 0) * (sortDirection[colIndex] ? 1 : -1);
            });

            rows.forEach(row => tbody.appendChild(row));
        }

        document.getElementById("searchInput").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase();
            document.querySelectorAll("#tabel_obat tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });
    </script>

@endsection