@extends('layouts.admin')

@section('title', 'Daftar Obat')

@section('content')

    {{-- Alert --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white p-6 shadow-lg rounded-xl">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Obat</h2>
                <p class="text-sm text-gray-500">Kelola daftar obat, stok, dan harga.</p>
            </div>
            <div class="flex items-center gap-2">
                 <a href="{{ route('obat.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Obat
                </a>
            </div>
        </div>
        
        <div class="flex justify-end mb-4">
             <input type="text" id="searchInput" placeholder="Cari nama atau kode..."
                   class="border px-3 py-2 w-full md:w-auto rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable(0)">Kode</th>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable(1)">Nama</th>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable(2)">Kemasan</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(3)">Stok</th>
                        <th class="px-4 py-3 text-left">Batch / ED</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(4)">Harga Dasar (HPP)</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(5)">Margin (%)</th>
                        <th class="px-4 py-3 text-right cursor-pointer" onclick="sortTable(6)">Harga Jual</th>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable(7)">Supplier</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel_obat" class="text-gray-700">
                    @forelse ($obats as $obat)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50
                            @if($obat->stok == 0) bg-red-50/50 
                            @elseif($obat->stok > 0 && $obat->stok <= $obat->min_stok) bg-yellow-50/50
                            @endif">
                            <td class="px-4 py-3 font-medium">{{ $obat->kode }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $obat->nama }}</span>
                                <span class="block text-xs text-gray-500">{{ $obat->kategori }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $obat->kemasan_besar ?? '-' }}</td>
                            
                            {{-- Stok (Tampilan Baru) --}}
                            <td class="px-4 py-3 text-right">
                                <span class="font-bold text-lg text-blue-600">{{ $obat->stok }}</span>
                                <span class="block text-xs text-gray-500">{{ $obat->satuan_terkecil }}</span>
                                 @if($obat->stok == 0)
                                    <span class="block text-xs text-red-600 font-semibold mt-1">Habis</span>
                                @elseif($obat->stok > 0 && $obat->stok <= $obat->min_stok)
                                     <span class="block text-xs text-yellow-600 font-semibold mt-1">Stok Menipis</span>
                                @endif
                            </td>

                            {{-- Batch --}}
                            <td class="px-4 py-3 text-xs">
                                @if($obat->batches->isNotEmpty())
                                    @foreach($obat->batches as $batch)
                                        <div class="whitespace-nowrap">
                                            <span class="font-semibold">{{ $batch->no_batch }}</span> | 
                                            <span class="text-gray-600">{{ $batch->expired_date ? $batch->expired_date->format('d/m/y') : '-' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            {{-- Kolom Harga Baru --}}
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($obat->harga_dasar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $obat->persen_untung ?? 0 }}%</td>
                            <td class="px-4 py-3 text-right font-bold text-green-600">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $obat->supplier->nama ?? '-' }}</td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <a href="{{ route('obat.edit', $obat->id) }}" class="text-gray-500 hover:text-yellow-600 p-2 rounded-full bg-gray-100 hover:bg-yellow-100 transition" title="Edit Obat">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </a>
                                    <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-gray-500 hover:text-red-600 p-2 rounded-full bg-gray-100 hover:bg-red-100 transition delete-btn" title="Hapus Obat">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.067-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-10 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.563A.562.562 0 0 1 9 14.437V9.564Z" /></svg>
                                    <h4 class="mt-2 text-lg font-semibold text-gray-700">Belum Ada Data Obat</h4>
                                    <p class="mt-1 text-sm">Mulai dengan menambahkan data obat baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Popup Konfirmasi Hapus --}}
    <div id="confirm-popup" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm text-center">
            <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data obat ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-center gap-4">
                <button id="cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold">Batal</button>
                <button id="confirm-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script>
        let sortDirection = {};

        function sortTable(colIndex) {
            const table = document.querySelector("table");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.querySelectorAll("tr"));
            const isAsc = sortDirection[colIndex] = !sortDirection[colIndex];

            // Update header icons
            table.querySelectorAll("th").forEach(th => th.innerHTML = th.innerHTML.replace(/ ▲| ▼/g, ""));
            const currentTh = table.querySelector(`th:nth-child(${colIndex + 1})`);
            if (currentTh.classList.contains('cursor-pointer')) {
                currentTh.innerHTML += isAsc ? " ▲" : " ▼";
            }

            rows.sort((a, b) => {
                let valA = a.cells[colIndex].innerText.trim();
                let valB = b.cells[colIndex].innerText.trim();

                // Numeric sort untuk Stok, HPP, Margin, Harga Jual
                if ([3, 4, 5, 6].includes(colIndex)) {
                    let numA = parseFloat(valA.replace(/[^\d.-]/g, '')) || 0;
                    let numB = parseFloat(valB.replace(/[^\d.-]/g, '')) || 0;
                    return (numA - numB) * (isAsc ? 1 : -1);
                }

                // Default text sort
                return valA.localeCompare(valB) * (isAsc ? 1 : -1);
            });

            rows.forEach(row => tbody.appendChild(row));
        }

        document.addEventListener("DOMContentLoaded", () => {
            const popup = document.getElementById("confirm-popup");
            const cancelBtn = document.getElementById("cancel-btn");
            const confirmBtn = document.getElementById("confirm-btn");
            let formToSubmit = null;

            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    formToSubmit = this.closest("form");
                    popup.classList.remove("hidden");
                    popup.classList.add("flex");
                });
            });

            cancelBtn.addEventListener("click", () => {
                popup.classList.add("hidden");
                popup.classList.remove("flex");
                formToSubmit = null;
            });

            confirmBtn.addEventListener("click", () => {
                if (formToSubmit) formToSubmit.submit();
            });

            document.getElementById("searchInput").addEventListener("keyup", function () {
                const filter = this.value.toLowerCase();
                document.querySelectorAll("#tabel_obat tr").forEach(row => {
                    const kode = row.cells[0].innerText.toLowerCase();
                    const nama = row.cells[1].innerText.toLowerCase();
                    row.style.display = (kode.includes(filter) || nama.includes(filter)) ? "" : "none";
                });
            });
        });
    </script>
@endsection