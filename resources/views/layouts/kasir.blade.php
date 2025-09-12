<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Kasir Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
        /* Efek fade untuk halaman */
        body {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        body.loaded {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100 flex min-h-screen font-sans">

<!-- Sidebar -->
<aside class="w-64 bg-green-900 text-white flex flex-col shadow-xl fixed inset-y-0 left-0 z-20">
    <!-- Brand -->
    <div class="p-6 text-2xl font-bold border-b border-green-800 flex items-center justify-center tracking-wide">
        Apotek <span class="text-green-300 ml-1">LIZ Farma 02</span>
    </div>

    <!-- Navigation -->
<nav class="mt-6 flex-1 px-2 space-y-2" x-data="{ activeDropdown: '{{ request()->is('pelanggan*') ? 'pelanggan' : null }}' }">
    @auth
        {{-- Sidebar untuk Kasir --}}
        @if(Auth::user()->role === 'kasir')

            {{-- POS --}}
            <a href="{{ route('pos.index') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors
               {{ request()->routeIs('pos.*') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-800 text-gray-200' }}">
                <i data-feather="shopping-cart" class="w-5 h-5 mr-3"></i>
                <span>POS Penjualan</span>
            </a>

            {{-- Riwayat --}}
            <a href="{{ route('kasir.riwayat') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors
               {{ request()->routeIs('kasir.riwayat') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-800 text-gray-200' }}">
                <i data-feather="clock" class="w-5 h-5 mr-3"></i>
                <span>Riwayat Penjualan</span>
            </a>

            {{-- Pelanggan (Dropdown) --}}
            <div>
                <button @click="activeDropdown = activeDropdown === 'pelanggan' ? null : 'pelanggan'"
                    class="flex items-center w-full px-4 py-3 rounded-lg transition-colors
                    {{ request()->is('pelanggan*') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-700 text-gray-200' }}">
                    <i data-feather="users" class="w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">Pelanggan</span>
                    <svg :class="{'rotate-180': activeDropdown === 'pelanggan'}"
                        class="w-4 h-4 transition-transform duration-300 ml-auto text-gray-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="activeDropdown === 'pelanggan'" x-transition x-cloak class="ml-10 mt-1 space-y-1">
                    <a href="{{ route('pelanggan.index') }}"
                       class="block px-4 py-2 rounded transition-colors
                       {{ request()->is('pelanggan') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-700 text-gray-300' }}">
                        Daftar Pelanggan
                    </a>
                    <a href="{{ route('pelanggan.create') }}"
                       class="block px-4 py-2 rounded transition-colors
                       {{ request()->is('pelanggan/create') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-700 text-gray-300' }}">
                        Tambah Pelanggan (Member)
                    </a>
                </div>
            </div>

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


        <!-- Page Content -->
        <main class="p-6 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <script>
        // Tambah class "loaded" setelah halaman selesai dimuat
        document.addEventListener("DOMContentLoaded", function() {
            document.body.classList.add("loaded");

            // Tambah animasi saat klik link internal
            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", function(e) {
                    const target = this.getAttribute("href");
                    if (target && target.startsWith("http") === false && !this.hasAttribute("target")) {
                        e.preventDefault();
                        document.body.classList.remove("loaded"); // fade out
                        setTimeout(() => {
                            window.location.href = target;
                        }, 300); // sesuai durasi transition
                    }
                });
            });
        });
    </script>
</body>

</html>