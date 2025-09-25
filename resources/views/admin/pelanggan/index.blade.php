@extends('layouts.admin')

@section('title', 'Manajemen Pelanggan')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white p-6 shadow-lg rounded-xl">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Pelanggan</h2>
                <p class="text-sm text-gray-500">Kelola daftar pelanggan dan anggota terdaftar.</p>
            </div>
            <a href="{{ route('pelanggan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Pelanggan
            </a>
        </div>

        <div class="mb-4 flex gap-2 items-center">
            <span class="text-sm font-semibold text-gray-600">Filter Status:</span>
            <a href="{{ route('pelanggan.index') }}" class="px-3 py-1 rounded-full text-sm font-medium {{ !$statusMember ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Semua</a>
            <a href="{{ route('pelanggan.index', ['status_member' => 'member']) }}" class="px-3 py-1 rounded-full text-sm font-medium {{ $statusMember == 'member' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Member</a>
            <a href="{{ route('pelanggan.index', ['status_member' => 'non_member']) }}" class="px-3 py-1 rounded-full text-sm font-medium {{ $statusMember == 'non_member' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Non-Member</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kontak</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Point</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($pelanggans as $pelanggan)
                        <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $pelanggan->nama }}</span>
                                <span class="block text-xs text-gray-500">{{ $pelanggan->no_ktp ?? 'No. KTP tidak ada' }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $pelanggan->telepon ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($pelanggan->status_member == 'member')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-semibold">Member</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full font-semibold">Non-Member</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $pelanggan->point ?? 0 }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                     <a href="{{ route('pelanggan.show', $pelanggan) }}" class="text-gray-500 hover:text-blue-600 p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition" title="Lihat Detail">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-4.43a1.012 1.012 0 0 1 1.431 0l4.43 4.43a1.012 1.012 0 0 1 0 .639l-4.43 4.43a1.012 1.012 0 0 1-1.431 0l-4.43-4.43Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </a>
                                    <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="text-gray-500 hover:text-yellow-600 p-2 rounded-full bg-gray-100 hover:bg-yellow-100 transition" title="Edit Pelanggan">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </a>
                                    <form action="{{ route('pelanggan.destroy', $pelanggan) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-gray-500 hover:text-red-600 p-2 rounded-full bg-gray-100 hover:bg-red-100 transition delete-btn" title="Hapus Pelanggan">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.067-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.14 5.441A3 3 0 0 1 4.5 12.72M18.72 18.72A3.001 3.001 0 0 1 12.72 18.72m-7.14 0A3.001 3.001 0 0 1 12 12.72m-3.741-.479A3 3 0 0 1 12 4.5m0-3.75v3.75m0 10.5a3 3 0 0 1-3 3m3-3a3 3 0 0 0 3 3m-3-3a3 3 0 0 1 3-3m-3 3a3 3 0 0 0-3-3" /></svg>
                                    <h4 class="mt-2 text-lg font-semibold text-gray-700">Belum Ada Data Pelanggan</h4>
                                    <p class="mt-1 text-sm">Mulai dengan menambahkan data pelanggan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $pelanggans->links() }}
        </div>
    </div>
    
    {{-- Popup Konfirmasi Hapus --}}
    <div id="confirm-popup" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm text-center">
            <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data pelanggan ini?</p>
            <div class="flex justify-center gap-4">
                <button id="cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold">Batal</button>
                <button id="confirm-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">Ya, Hapus</button>
            </div>
        </div>
    </div>
    <script>
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
    });
    </script>
@endsection