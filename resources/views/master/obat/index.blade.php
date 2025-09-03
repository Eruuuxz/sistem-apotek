{{-- File: resources/views/master/obat/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Daftar Obat')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar Obat</h1>

@if (session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white p-4 shadow rounded">
    <div class="flex justify-between items-center mb-4">
        <div class="flex gap-2">
            <a href="{{ route('obat.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                + Tambah Obat
            </a>

            {{-- Tombol Reset Filter hanya tampil kalau filter aktif --}}
            @if(request('filter'))
                <a href="{{ route('obat.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Reset Filter
                </a>
            @endif
        </div>

        {{-- Search --}}
        <input type="text" id="searchInput" placeholder="Cari obat..." 
               class="border px-3 py-2 w-64 rounded">
    </div>

    <table class="w-full text-sm border" id="tabelObat">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-2 py-1 cursor-pointer" onclick="sortTable(0)">Kode</th>
                <th class="px-2 py-1 cursor-pointer" onclick="sortTable(1)">Nama</th>
                <th class="px-2 py-1 cursor-pointer" onclick="sortTable(2)">Kategori</th>
                <th class="px-2 py-1 text-right cursor-pointer" onclick="sortTable(3)">Stok</th>
                <th class="px-2 py-1 text-right cursor-pointer" onclick="sortTable(4)">Harga Dasar</th>
                <th class="px-2 py-1 text-right cursor-pointer" onclick="sortTable(5)">Untung (%)</th>
                <th class="px-2 py-1 text-right cursor-pointer" onclick="sortTable(6)">Harga Jual</th>
                <th class="px-2 py-1 text-right cursor-pointer" onclick="sortTable(7)">Keuntungan/Unit</th>
                <th class="px-2 py-1 cursor-pointer" onclick="sortTable(8)">Supplier</th>
                <th class="px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel_obat">
            @foreach ($obats as $obat)
                <tr 
                    @if($obat->stok == 0)
                        class="bg-red-200"
                    @elseif($obat->stok > 0 && $obat->stok < 10)
                        class="bg-yellow-200"
                    @endif
                >
                    <td class="border px-2 py-1">{{ $obat->kode }}</td>
                    <td class="border px-2 py-1">{{ $obat->nama }}</td>
                    <td class="border px-2 py-1">{{ $obat->kategori }}</td>
                    <td class="border px-2 py-1 text-right">
                        {{ $obat->stok }}
                        @if($obat->stok == 0)
                            <span class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded">Habis</span>
                        @elseif($obat->stok > 0 && $obat->stok < 10)
                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-500 text-white rounded">Menipis</span>
                        @endif
                    </td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_dasar, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1 text-right">{{ $obat->persen_untung }}%</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_jual - $obat->harga_dasar, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1">{{ $obat->supplier->nama ?? '-' }}</td>
                    <td class="border px-2 py-1">
                        <a href="{{ route('obat.edit', $obat->id) }}" class="text-blue-500">Edit</a> |
                        <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500" onclick="return confirm('Yakin ingin hapus?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Script Search + Sort --}}
<script>
    let sortDirection = {};

    function sortTable(colIndex) {
        const table = document.getElementById("tabelObat");
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.querySelectorAll("tr"));

        sortDirection[colIndex] = !sortDirection[colIndex];

        rows.sort((a, b) => {
            let valA = a.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g, "").toLowerCase();
            let valB = b.cells[colIndex].innerText.trim().replace(/[Rp.,%]/g, "").toLowerCase();

            let numA = parseFloat(valA) || valA;
            let numB = parseFloat(valB) || valB;

            if (numA < numB) return sortDirection[colIndex] ? -1 : 1;
            if (numA > numB) return sortDirection[colIndex] ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#tabel_obat tr");

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>
@endsection
