{{-- File: resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Apotek</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-green-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md">
        
        {{-- Header --}}
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-apotek.png') }}" 
                 alt="Logo Apotek" 
                 class="w-20 h-20 mx-auto mb-4 rounded-full shadow-md">
            <h1 class="text-2xl font-bold text-gray-800">
                Login {{ isset($role) ? ucfirst($role) : '' }}
            </h1>
            <p class="text-sm text-gray-500">Masukkan email dan password Anda</p>
        </div>

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Simpan role yang dipilih --}}
            @if(isset($role))
                <input type="hidden" name="role" value="{{ $role }}">
            @endif

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required autofocus 
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center">
                    <input id="remember" type="checkbox" name="remember" class="text-blue-600 rounded">
                    <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    Lupa password?
                </a>
            </div>

            {{-- Tombol Login --}}
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Login
            </button>
        </form>
    </div>
</body>
</html>
