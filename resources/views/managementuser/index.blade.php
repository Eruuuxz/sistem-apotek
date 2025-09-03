@extends('layouts.admin')

@section('title', 'Manajemen Kasir')

@section('content')

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('users.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Tambah Kasir</a>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded">
        <table class="w-full table-auto border-collapse text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-left">Nama</th>
                    <th class="px-4 py-2 border text-left">Email</th>
                    <th class="px-4 py-2 border text-left">Role</th>
                    <th class="px-4 py-2 border text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($user->role) }}</td>
                        <td class="border px-4 py-2 text-center">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500 hover:underline">Edit</a>

                            <!-- Tombol hapus -->
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-600 hover:underline delete-btn">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada kasir.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
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

            // Tombol hapus ditekan
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    formToSubmit = this.closest("form");
                    popup.classList.remove("hidden");
                    popup.classList.add("flex");
                });
            });

            // Batal
            cancelBtn.addEventListener("click", () => {
                popup.classList.add("hidden");
                popup.classList.remove("flex");
                formToSubmit = null;
            });

            // Konfirmasi hapus
            confirmBtn.addEventListener("click", () => {
                if (formToSubmit) formToSubmit.submit();
            });
        });
    </script>

@endsection