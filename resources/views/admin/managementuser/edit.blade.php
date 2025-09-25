@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')

    <div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Edit Data User</h2>
            <p class="text-sm text-gray-500">Perbarui detail untuk user <span class="font-semibold">{{ $user->name }}</span>.</p>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Nama</label>
                    <input type="text" name="name" class="w-full border rounded-lg px-3 py-2" value="{{ $user->name }}" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full border rounded-lg px-3 py-2" value="{{ $user->email }}" required>
                </div>
            </div>
            
            <div class="border-t pt-6">
                 <p class="text-sm text-gray-500 mb-4">Kosongkan isian password jika Anda tidak ingin mengubahnya.</p>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Password Baru</label>
                        <input type="password" name="password" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Role</label>
                    <select name="role" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="kasir" @if($user->role == 'kasir') selected @endif>Kasir</option>
                        <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Cabang</label>
                    <select name="cabang_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Pilih Cabang (Opsional)</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" @if($user->cabang_id == $cabang->id) selected @endif>{{ $cabang->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                 <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Update User</button>
            </div>
        </form>
    </div>

@endsection