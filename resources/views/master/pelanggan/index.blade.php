@extends(in_array(auth()->user()->role, ['admin', 'kasir']) ? 'layouts.admin' : 'layouts.kasir')

@section('title', 'Daftar Pelanggan')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Pelanggan</h1>
        <a href="{{ route('pelanggan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Tambah Pelanggan
        </a>
    </div>

    <div class="mb-4 flex gap-2 items-center">
        <span>Filter Status Member:</span>
        <a href="{{ route('pelanggan.index') }}"
            class="px-3 py-1 rounded {{ !$statusMember ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Semua</a>
        <a href="{{ route('pelanggan.index', ['status_member' => 'member']) }}"
            class="px-3 py-1 rounded {{ $statusMember == 'member' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Member</a>
        <a href="{{ route('pelanggan.index', ['status_member' => 'non_member']) }}"
            class="px-3 py-1 rounded {{ $statusMember == 'non_member' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Non-Member</a>
    </div>

    <div class="bg-white p-6 shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Telepon</th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-left">No. KTP</th>
                        <th class="px-4 py-3 text-center">Status Member</th>
                        <th class="px-4 py-3 text-center">Point</th>
                        <th class="px-4 py-3 text-center">File KTP</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelanggans as $pelanggan)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-3">{{ $pelanggan->nama }}</td>
                            <td class="border px-4 py-3">{{ $pelanggan->telepon ?? '-' }}</td>
                            <td class="border px-4 py-3">{{ $pelanggan->alamat ?? '-' }}</td>
                            <td class="border px-4 py-3">{{ $pelanggan->no_ktp ?? '-' }}</td>
                            <td class="border px-4 py-3 text-center">
                                @if ($pelanggan->status_member == 'member')
                                    <span class="bg-green-200 text-green-800 text-xs px-2 py-1 rounded-full">Member</span>
                                @else
                                    <span class="bg-red-200 text-red-800 text-xs px-2 py-1 rounded-full">Non-Member</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-center">{{ $pelanggan->point ?? 0 }}</td>
                            <td class="border px-4 py-3 text-center">
                                @if ($pelanggan->file_ktp)
                                    <a href="{{ Storage::url($pelanggan->file_ktp) }}" target="_blank"
                                        class="text-blue-500 hover:underline">Lihat KTP</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-center">
                                {{-- Tombol CRUD hanya untuk pelanggan yang memiliki ID (yaitu member) --}}
                                @if ($pelanggan->id)
                                    <a href="{{ route('pelanggan.edit', $pelanggan->id) }}"
                                        class="text-yellow-500 hover:underline mr-2">Edit</a>
                                    <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border px-4 py-3 text-center text-gray-500">Tidak ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $pelanggans->links() }}
        </div>
    </div>
@endsection
