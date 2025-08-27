<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-800 text-white min-h-screen">
        <div class="p-4 text-2xl font-bold border-b border-blue-700">
            Apotek XYZ
        </div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('dashboard*') || request()->is('/') ? 'bg-blue-600' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('obat.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('obat*') ? 'bg-blue-600' : '' }}">
                Data Obat
            </a>
            <a href="{{ route('supplier.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('supplier*') ? 'bg-blue-600' : '' }}">
                Data Supplier
            </a>
            <a href="{{ route('pembelian.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('pembelian*') ? 'bg-blue-600' : '' }}">
                Pembelian
            </a>
            <a href="{{ route('penjualan.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('penjualan*') ? 'bg-blue-600' : '' }}">
                Penjualan
            </a>
            <a href="{{ route('retur.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('retur*') ? 'bg-blue-600' : '' }}">
                Retur Barang
            </a>
            <a href="{{ route('laporan.index') }}" class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('laporan*') ? 'bg-blue-600' : '' }}">
                Laporan
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('title', 'Sistem Informasi Apotek')</h1>
            <div>
                <span class="mr-4">Halo, User</span>
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
