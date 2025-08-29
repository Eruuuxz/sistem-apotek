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
        <a href="{{ route('obat.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Obat</a>
        {{-- <input type="text" placeholder="Cari obat..." class="border px-3 py-2 w-64"> --}} {{-- Fitur search bisa ditambahkan nanti --}}
    </div>

    <table class="w-full text-sm border">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-2 py-1">Kode</th>
                <th class="px-2 py-1">Nama</th>
                <th class="px-2 py-1">Kategori</th>
                <th class="px-2 py-1 text-right">Stok</th>
                <th class="px-2 py-1 text-right">Harga Dasar</th>
                <th class="px-2 py-1 text-right">Untung (%)</th>
                <th class="px-2 py-1 text-right">Harga Jual</th>
                <th class="px-2 py-1 text-right">Keuntungan/Unit</th>
                <th class="px-2 py-1">Supplier</th> {{-- Tambah kolom Supplier --}}
                <th class="px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel_obat">
            @foreach ($obats as $obat)
                <tr>
                    <td class="border px-2 py-1">{{ $obat->kode }}</td>
                    <td class="border px-2 py-1">{{ $obat->nama }}</td>
                    <td class="border px-2 py-1">{{ $obat->kategori }}</td>
                    <td class="border px-2 py-1 text-right">{{ $obat->stok }}</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_dasar, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1 text-right">{{ $obat->persen_untung }}%</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($obat->harga_jual - $obat->harga_dasar, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1">{{ $obat->supplier->nama ?? '-' }}</td> {{-- Tampilkan nama supplier --}}
                    <td class="border px-2 py-1">
                        <a href="{{ route('obat.edit', $obat->id) }}" class="text-blue-500">Edit</a> |
                        <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500" onclick="return confirm('Yakin ingin hapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


