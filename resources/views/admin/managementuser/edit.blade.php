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
                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700">Nama</label>
                    <input type="text" id="name" name="name" class="w-full border rounded-lg px-3 py-2" value="{{ $user->name }}" required>
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full border rounded-lg px-3 py-2" value="{{ $user->email }}" required>
                </div>
            </div>
            
            <div class="border-t pt-6">
                 <p class="text-sm text-gray-500 mb-4">Kosongkan isian password jika Anda tidak ingin mengubahnya.</p>
                 <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block mb-2 text-sm font-semibold text-gray-700">Password Saat Ini</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" class="w-full border rounded-lg px-3 py-2 pr-10">
                            <button type="button" onclick="togglePassword('current_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-7.583A12.03 12.03 0 0 1 12 3c2.405 0 4.636.786 6.534 2.1l4.43 7.583a1.012 1.012 0 0 1 0 .639l-4.43 7.583A12.03 12.03 0 0 1 12 21c-2.405 0-4.636-.786-6.534-2.1L2.036 12.322Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full border rounded-lg px-3 py-2 pr-10">
                            <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-7.583A12.03 12.03 0 0 1 12 3c2.405 0 4.636.786 6.534 2.1l4.43 7.583a1.012 1.012 0 0 1 0 .639l-4.43 7.583A12.03 12.03 0 0 1 12 21c-2.405 0-4.636-.786-6.534-2.1L2.036 12.322Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 pr-10">
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                               <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-7.583A12.03 12.03 0 0 1 12 3c2.405 0 4.636.786 6.534 2.1l4.43 7.583a1.012 1.012 0 0 1 0 .639l-4.43 7.583A12.03 12.03 0 0 1 12 21c-2.405 0-4.636-.786-6.534-2.1L2.036 12.322Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="role" class="block mb-2 text-sm font-semibold text-gray-700">Role</label>
                <select id="role" name="role" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="kasir" @if($user->role == 'kasir') selected @endif>Kasir</option>
                    <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                 <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Update User</button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const eyeIcon = `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-7.583A12.03 12.03 0 0 1 12 3c2.405 0 4.636.786 6.534 2.1l4.43 7.583a1.012 1.012 0 0 1 0 .639l-4.43 7.583A12.03 12.03 0 0 1 12 21c-2.405 0-4.636-.786-6.534-2.1L2.036 12.322Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>`;
            const eyeSlashIcon = `<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L6.228 6.228" /></svg>`;

            if (field.type === "password") {
                field.type = "text";
                button.innerHTML = eyeSlashIcon;
            } else {
                field.type = "password";
                button.innerHTML = eyeIcon;
            }
        }
    </script>

@endsection