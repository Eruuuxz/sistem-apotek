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
            <a href="{{ route('obat.create') }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2 rounded-lg shadow hover:from-blue-600 hover:to-blue-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Obat
            </a>
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
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel_obat">
                @foreach ($obats as $obat)
                    <tr class="hover:bg-gray-50 transition
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
                        <td class="border px-4 py-3 text-right">Rp {{ number_format($obat->harga_jual - $obat->harga_dasar, 0, ',', '.') }}</td>
                        <td class="border px-4 py-3">{{ $obat->supplier->nama ?? '-' }}</td>
                        <td class="border px-4 py-3 flex gap-2">
                            <a href="{{ route('obat.edit', $obat->id) }}" class="text-blue-500 hover:underline">Edit</a>
                            <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
let sortDirection = {};
function sortTable(colIndex) {
    const table = document.querySelector("table");
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.querySelectorAll("tr"));
    sortDirection[colIndex] = !sortDirection[colIndex];

    rows.sort((a,b) => {
        let valA = a.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g,"").toLowerCase();
        let valB = b.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g,"").toLowerCase();
        let numA = parseFloat(valA) || valA;
        let numB = parseFloat(valB) || valB;
        return (numA < numB ? -1 : numA > numB ? 1 : 0) * (sortDirection[colIndex] ? 1 : -1);
    });

    rows.forEach(row => tbody.appendChild(row));
}

document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#tabel_obat tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>

@endsection
