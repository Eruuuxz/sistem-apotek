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
            @auth
                @if(in_array(Auth::user()->role, ['kasir', 'admin']))
                    <a href="{{ route('pos.index') }}" class="block px-4 py-2 hover:bg-green-600 {{ request()->is('pos*') ? 'bg-green-600' : '' }}">
                        POS Penjualan
                    </a>
                    <a href="{{ route('penjualan.index') }}" class="block px-4 py-2 hover:bg-green-600 {{ request()->is('penjualan*') ? 'bg-green-600' : '' }}">
                        Riwayat Penjualan
                    </a>
                @endif
            @endauth
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('title', 'Sistem Kasir')</h1>
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