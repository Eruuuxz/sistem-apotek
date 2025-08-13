<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Kasir Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar Kasir -->
    <aside class="w-64 bg-green-800 text-white min-h-screen">
        <div class="p-4 text-2xl font-bold border-b border-green-700">
            Kasir Apotek
        </div>
        <nav class="mt-4">
            <a href="/kasir/pos" class="block px-4 py-2 hover:bg-green-600 {{ request()->is('kasir/pos') ? 'bg-green-600' : '' }}">
                POS Penjualan
            </a>
            <a href="/kasir/riwayat" class="block px-4 py-2 hover:bg-green-600 {{ request()->is('kasir/riwayat') ? 'bg-green-600' : '' }}">
                Riwayat Penjualan
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('title', 'Sistem Kasir')</h1>
            <div>
                <span class="mr-4">Halo, Kasir</span>
                <a href="#" class="text-red-500">Logout</a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    
</body>
</html>
