@extends('layouts.admin')

@section('title', 'Manajemen Kasir')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Manajemen Kasir</h1>

    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Kasir</a>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 mt-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full mt-4 bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="border px-4 py-2">{{ $user->name }}</td>
                    <td class="border px-4 py-2">{{ $user->email }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($user->role) }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500">Edit</a> |
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600"
                                onclick="return confirm('Yakin hapus user ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endsection
