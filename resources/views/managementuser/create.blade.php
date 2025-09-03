@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label>Nama</label>
            <input type="text" name="name" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label>Role</label>
            <select name="role" class="w-full border rounded p-2" required>
                <option value="kasir">Kasir</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
@endsection
