<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>

<body class="bg-gray-100 flex min-h-screen font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col shadow-xl fixed inset-y-0 left-0 z-20">
        <!-- Brand -->
        <div class="p-6 text-2xl font-bold border-b border-blue-800 flex items-center justify-center tracking-wide">
            Apotek <span class="text-blue-300 ml-1">LIZ Farma 02</span>
        </div>
        <nav class="mt-6 flex-1 overflow-auto">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('dashboard*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Dashboard</span>
                    </a>

                    <a href="{{ route('obat.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('obat*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="box" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Data Obat</span>

                        @php
                            $stokHabis = \App\Models\Obat::where('stok', 0)->count();
                            $stokMenipis = \App\Models\Obat::where('stok', '<=', 10)->where('stok', '>', 0)->count();
                        @endphp

                        @if($stokHabis > 0)
                            <span class="ml-2 bg-red-500 text-xs px-2 py-0.5 rounded-full">{{ $stokHabis }}</span>
                        @endif

                        @if($stokMenipis > 0)
                            <span class="ml-2 bg-yellow-500 text-xs px-2 py-0.5 rounded-full">{{ $stokMenipis }}</span>
                        @endif
                    </a>

                    <a href="{{ route('supplier.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('supplier*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Data Supplier</span>
                    </a>

                    <a href="{{ route('pembelian.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('pembelian*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="shopping-cart" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Pembelian</span>
                    </a>

                    <a href="{{ route('retur.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('retur*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="corner-up-left" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Retur Barang</span>
                    </a>

                    <a href="{{ route('laporan.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('laporan*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Laporan</span>
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('users*') ? 'bg-blue-700 font-semibold' : '' }}">
                        <i data-feather="user-plus" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Management Kasir</span>
                    </a>
                @endif
            @endauth
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-64">
        <!-- Header -->
        <header
            class="bg-white shadow-md p-4 flex justify-between items-center border-b border-gray-200 sticky top-0 z-20">
            <!-- Judul Halaman -->
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                @yield('title', 'Dashboard')
            </h1>

            <!-- Info user & aksi -->
            <div class="flex items-center space-x-4">
                <!-- Clock -->
                <div id="clock" class="text-sm text-gray-500 font-medium"></div>

                @auth
                    <!-- Role -->
                    <span
                        class="text-sm text-gray-600 px-3 py-1 bg-gray-100 rounded-full border border-gray-200 hidden md:inline-block">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>

                    <!-- Nama user dengan avatar -->
                    <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                        <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=32"
                            alt="Avatar" class="w-8 h-8 rounded-full">
                    </div>

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition font-medium text-sm">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        <!-- Script Clock -->
        <script>
            function updateClock() {
                const now = new Date();
                document.getElementById('clock').innerText =
                    now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
                    + ' ' + now.toLocaleTimeString('id-ID');
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
        </header>

        <!-- Page Content -->
        <main class="p-6 flex-1 overflow-auto">

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        feather.replace();
    </script>
</body>

</html>