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
            LIZ Farma 02 Admin
        </div>
        <nav class="mt-4">
            @auth {{-- Pastikan user sudah login --}}
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('dashboard*') || request()->is('/') ? 'bg-blue-600' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('obat.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('obat*') ? 'bg-blue-600' : '' }}">
                        Data Obat
                    </a>
                    <a href="{{ route('supplier.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('supplier*') ? 'bg-blue-600' : '' }}">
                        Data Supplier
                    </a>
                    <a href="{{ route('pembelian.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('pembelian*') ? 'bg-blue-600' : '' }}">
                        Pembelian
                    </a>
                    <a href="{{ route('retur.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('retur*') ? 'bg-blue-600' : '' }}">
                        Retur Barang
                    </a>
                    <a href="{{ route('laporan.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('laporan*') ? 'bg-blue-600' : '' }}">
                        Laporan
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="block px-4 py-2 hover:bg-blue-600 {{ request()->is('users*') ? 'bg-blue-600' : '' }}">
                        Management Kasir
                    </a>
                @endif
            @endauth
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('title', 'Sistem Informasi Apotek')</h1>
            <div>
                @auth
                    <span class="mr-4">Halo, {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-blue-500">Login</a>
                @endauth
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