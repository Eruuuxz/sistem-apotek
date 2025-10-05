@extends('layouts.admin')

@section('title', 'Tambah User Baru')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Formulir User Baru</h2>
            <p class="text-sm text-gray-500">Isi detail di bawah untuk menambahkan user baru.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Nama</label>
                    <input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">Role</label>
                <select name="role" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Simpan User</button>
            </div>
        </form>
    </div>

@endsection