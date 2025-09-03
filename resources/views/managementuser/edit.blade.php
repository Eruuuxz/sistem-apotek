@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')

<div class="max-w-lg mx-auto bg-white shadow-md rounded p-6">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf 
        @method('PUT')

        <div>
            <label class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ $user->name }}" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ $user->email }}" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Password (Opsional)</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" placeholder="Kosongkan jika tidak ingin diubah">
        </div>

        <div>
            <label class="block font-semibold mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2" required>
                <option value="kasir" @if($user->role == 'kasir') selected @endif>Kasir</option>
                <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
            </select>
        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Update</button>
            <a href="{{ route('users.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Batal</a>
        </div>
    </form>
</div>

@endsection
