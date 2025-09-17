@extends('layouts.admin')

@section('title', 'Daftar Surat Pesanan')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Surat Pesanan</h1>
        <a href="{{ route('surat_pesanan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Buat SP Baru
        </a>
    </div>

    <div class="bg-white p-6 shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">No SP</th>
                        <th class="px-4 py-3 text-left">Tanggal SP</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratPesanans as $sp)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-3">{{ $sp->no_sp }}</td>
                            <td class="border px-4 py-3">{{ $sp->tanggal_sp->format('d-m-Y H:i') }}</td>
                            <td class="border px-4 py-3">{{ $sp->supplier->nama ?? '-' }}</td>
                            <td class="border px-4 py-3">{{ $sp->user->name ?? '-' }}</td>
                            <td class="border px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($sp->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($sp->status == 'parsial') bg-blue-100 text-blue-800
                                    @elseif($sp->status == 'selesai') bg-green-100 text-green-800
                                    @elseif($sp->status == 'dibatalkan') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($sp->status) }}
                                </span>
                            </td>
                            <td class="border px-4 py-3 text-center">
                                <a href="{{ route('surat_pesanan.pdf', $sp->id) }}" class="text-blue-600 hover:underline mr-2">Detail</a>
                                <a href="{{ route('surat_pesanan.edit', $sp->id) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                                <form action="{{ route('surat_pesanan.destroy', $sp->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus Surat Pesanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                                @if($sp->file_template)
                                    <a href="{{ route('surat_pesanan.download_template', $sp->id) }}" class="text-green-600 hover:underline ml-2">Download Template</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-3 text-center text-gray-500">Tidak ada Surat Pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $suratPesanans->links() }}
        </div>
    </div>
@endsection