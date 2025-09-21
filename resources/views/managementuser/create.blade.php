@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')

    <div class="max-w-lg mx-auto bg-white shadow-md rounded p-6">

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-semibold mb-1">Nama</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Cabang</label>
                <select name="cabang_id" class="w-full border rounded px-3 py-2">
                    <option value="">Pilih Cabang (Opsional)</option>
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Simpan</button>
                <a href="{{ route('users.index') }}"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Batal</a>
            </div>
        </form>
    </div>

@endsection