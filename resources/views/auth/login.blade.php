<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @php
        // 1. Tentukan peran (role) dengan fallback ke 'admin' jika tidak ada
        $role = $role ?? 'admin';
        $is_kasir = ($role === 'kasir');

        // 2. Definisikan semua variabel tema di satu tempat
        
        // Panel Samping (Splash Screen)
        $splashGradient = $is_kasir ? 'bg-gradient-to-br from-green-600 to-green-800' : 'bg-gradient-to-br from-blue-600 to-blue-800';
        $splashTextMuted = $is_kasir ? 'text-green-200' : 'text-blue-200';

        // Aksen Teks & Link
        $textAccent = $is_kasir ? 'text-green-600' : 'text-blue-600';

        // Form Input (Focus Ring & Checkbox)
        $focusRing = $is_kasir ? 'focus:border-green-500 focus:ring-1 focus:ring-green-500' : 'focus:border-blue-500 focus:ring-1 focus:ring-blue-500';
        $checkboxClasses = $is_kasir ? 'text-green-600 focus:ring-green-500' : 'text-blue-600 focus:ring-blue-500';

        // Tombol Utama (Button)
        $buttonClasses = $is_kasir 
            ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500 shadow-lg shadow-green-500/20 hover:shadow-xl hover:shadow-green-500/40' 
            : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/40';
    @endphp

    <title>Login {{ ucfirst($role) }} - Sistem Apotek</title>
    @vite('resources/css/app.css')
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-white">

    <div class="flex flex-wrap min-h-screen">
        
        <div class="hidden lg:flex w-1/2 items-center justify-center p-12 text-white {{ $splashGradient }}">
            <div class="w-full max-w-md text-center">
                <a href="{{ route('pilih-login') }}">
                    <img src="{{ asset('images/logoAdmin.png') }}" alt="Logo Apotek" 
                         class="h-24 w-24 mx-auto mb-6 rounded-full ring-4 ring-white/20">
                </a>
                <h1 class="text-4xl font-bold mb-3">
                    {{ $is_kasir ? 'Akses Point of Sale' : 'Selamat Datang Kembali' }}
                </h1>
                <p class="{{ $splashTextMuted }}">
                    {{ $is_kasir ? 'Masuk untuk memulai sesi penjualan dan melayani pelanggan.' : 'Sistem manajemen apotek terpadu untuk mengelola bisnis Anda.' }}
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-sm">

                <div class="text-center mb-8 lg:hidden">
                    <a href="{{ route('pilih-login') }}">
                        <img src="{{ asset('images/logo-apotek.png') }}" alt="Logo Apotek" 
                             class="w-20 h-20 mx-auto mb-4 rounded-full shadow-md">
                    </a>
                </div>
                
                <h2 class="text-3xl font-bold text-slate-900 text-center lg:text-left">
                    Login {{ ucfirst($role) }}
                </h2>
                <p class="text-slate-500 mt-2 text-center lg:text-left mb-8">
                    Silakan masuk untuk melanjutkan
                </p>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="block w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none 
                                      {{ $focusRing }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   class="block w-full px-4 py-3 pr-10 border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none 
                                          {{ $focusRing }}">
                            
                            <button type="button" id="password-toggle" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                <svg id="eye-icon" class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.418-5.522a1.012 1.012 0 011.603 0l4.418 5.522a1.012 1.012 0 010 .639l-4.418 5.522a1.012 1.012 0 01-1.603 0l-4.418-5.522z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eye-slash-icon" class="h-5 w-5 text-slate-400 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember" class="flex items-center gap-2">
                            <input id="remember" type="checkbox" name="remember" 
                                   class="h-4 w-4 rounded border-slate-300 {{ $checkboxClasses }}">
                            <span class="text-slate-600">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-medium {{ $textAccent }} hover:underline">
                            Lupa password?
                        </a>
                    </div>

                    @error('email')
                        <div class="text-red-600 text-sm text-center bg-red-50 p-3 rounded-lg border border-red-200">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="submit"
                            class="w-full text-white py-3 px-4 rounded-lg font-semibold transition-colors duration-200 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2
                                   {{ $buttonClasses }}">
                        Login
                    </button>
                </form>

                <div class="text-center mt-8">
                    <a href="{{ route('pilih-login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">
                        
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        
                        <span>Kembali ke pilihan peran</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('password-toggle');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordToggle && passwordInput && eyeIcon && eyeSlashIcon) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    eyeIcon.classList.toggle('hidden');
                    eyeSlashIcon.classList.toggle('hidden');
                });
            }
        });
    </script>

</body>
</html>