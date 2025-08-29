{{-- File: resources/views/pilih-login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Login - Sistem Apotek</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gradient-to-br from-green-100 via-blue-100 to-indigo-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-lg text-center relative overflow-hidden">
        
        {{-- Logo Apotek --}}
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo-apotek.png') }}" alt="" class="h-20 w-auto">
        </div>

        {{-- Header --}}
        <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Pilih Login</h1>
        <p class="text-gray-600 mb-8">Masuk sebagai Admin atau Kasir untuk melanjutkan</p>

        {{-- Pilihan Login --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Admin --}}
            <a href="{{ url('/login/admin') }}" 
               class="group bg-gradient-to-r from-blue-500 to-blue-700 text-white py-8 px-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition flex flex-col items-center">
                <i data-lucide="settings" class="w-10 h-10 mb-3"></i>
                <span class="text-lg font-semibold">Admin</span>
                <p class="text-xs text-blue-100 mt-1">Kelola sistem & data apotek</p>
            </a>

            {{-- Kasir --}}
            <a href="{{ url('/login/kasir') }}" 
               class="group bg-gradient-to-r from-green-500 to-green-700 text-white py-8 px-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition flex flex-col items-center">
                <i data-lucide="shopping-cart" class="w-10 h-10 mb-3"></i>
                <span class="text-lg font-semibold">Kasir</span>
                <p class="text-xs text-green-100 mt-1">Transaksi penjualan & pembayaran</p>
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
