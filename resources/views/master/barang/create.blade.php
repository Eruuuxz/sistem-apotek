@extends('layouts.admin')

@section('title', 'Tambah Barang')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Barang</h1>

<form action="{{ url('/barang') }}" method="POST" class="bg-white p-6 shadow rounded w-1/2">
    @csrf
    <div class="mb-4">
        <label class="block mb-1">Kode Barang</label>
        <input type="text" name="kode" class="w-full border px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block mb-1">Nama Barang</label>
        <input type="text" name="nama" class="w-full border px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block mb-1">Harga Jual</label>
        <input type="number" name="harga_jual" class="w-full border px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block mb-1">Stok</label>
        <input type="number" name="stok" class="w-full border px-3 py-2" required>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
