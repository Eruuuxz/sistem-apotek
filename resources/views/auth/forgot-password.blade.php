{{-- File: resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Apotek</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-green-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-apotek.png') }}" 
                 alt="Logo Apotek" 
                 class="w-16 h-16 mx-auto mb-4 rounded-full shadow-md">
            <h1 class="text-2xl font-bold text-gray-800">Lupa Password</h1>
            <p class="text-sm text-gray-500 mt-2">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
            </p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm mb-4">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form Reset Password --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

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

            {{-- Tombol --}}
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Kirim Link Reset Password
            </button>
        </form>

        {{-- Back to login --}}
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>
