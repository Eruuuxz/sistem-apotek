@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label>Nama</label>
            <input type="text" name="name" class="w-full border rounded p-2" value="{{ $user->name }}" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="{{ $user->email }}" required>
        </div>
        <div>
            <label>Password (opsional)</label>
            <input type="password" name="password" class="w-full border rounded p-2">
        </div>
        <div>
            <label>Role</label>
            <select name="role" class="w-full border rounded p-2" required>
                <option value="kasir" @if($user->role == 'kasir') selected @endif>Kasir</option>
                <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
            </select>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
    </form>
@endsection
