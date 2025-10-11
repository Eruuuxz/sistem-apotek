@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-left">Kode</th>
                    <th class="px-4 py-2 border text-left">Nama Supplier</th>
                    <th class="px-4 py-2 border text-left">Alamat</th>
                    <th class="px-4 py-2 border text-left">Kota</th>
                    <th class="px-4 py-2 border text-left">Telepon</th>
                    <th class="px-4 py-2 border text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $supplier->kode }}</td>
                        <td class="px-4 py-2 border">{{ $supplier->nama }}</td>
                        <td class="px-4 py-2 border">{{ $supplier->alamat }}</td>
                        <td class="px-4 py-2 border">{{ $supplier->kota }}</td>
                        <td class="px-4 py-2 border">{{ $supplier->telepon }}</td>
                        <td class="px-4 py-2 border text-center" x-data="{ open: false }">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('supplier.edit', $supplier->id) }}"
                                    class="text-yellow-500 hover:underline">Edit</a>

                                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"
                                        onclick="return confirm('Yakin ingin hapus?')">Hapus</button>
                                </form>

                                <button @click="open = true" class="text-blue-500 hover:underline">Cek Obat</button>
                            </div>

                            <!-- Modal tabel obat -->
                            <div x-show="open"
                                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                                <div class="bg-white rounded shadow w-96 max-h-[80vh] overflow-auto p-4">
                                    <h2 class="text-lg font-semibold mb-3">Obat Disuplai: {{ $supplier->nama }}</h2>

                                    @php
                                        $obats = $supplier->obat ?? collect();
                                    @endphp

                                    @if($obats->isEmpty())
                                        <p class="text-gray-500">Tidak ada obat</p>
                                    @else
                                        <table class="w-full text-sm border-collapse">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 border">No</th>
                                                    <th class="px-3 py-2 border">Nama Obat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($obats as $index => $obat)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-3 py-2 border">{{ $index + 1 }}</td>
                                                        <td class="px-3 py-2 border">{{ $obat->nama }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif

                                    <button @click="open = false"
                                        class="mt-4 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded w-full">Tutup</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection