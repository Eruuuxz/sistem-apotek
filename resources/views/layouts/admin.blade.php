<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
    <aside class="w-64 bg-blue-900 text-white flex flex-col shadow-xl fixed inset-y-0 left-0 z-20">
        <!-- Brand -->
        <div class="p-6 text-2xl font-bold border-b border-blue-800 flex items-center justify-center tracking-wide">
            Apotek <span class="text-blue-300 ml-1">LIZ Farma 02</span>
        </div>

        <nav class="mt-6 flex-1 overflow-auto" x-data="{ activeDropdown: null }">
            @auth
                @if(Auth::user()->role === 'admin')

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors
                            {{ request()->is('dashboard*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>

                    @php
                        $stokHabis = \App\Models\Obat::where('stok', 0)->count();
                        $stokMenipis = \App\Models\Obat::where('stok', '<=', 10)->where('stok', '>', 0)->count();
                    @endphp
                    <!-- Obat Dropdown -->
                    <div>
                        <button @click="activeDropdown = (activeDropdown === 'obat' ? null : 'obat')" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-blue-700 hover:text-white transition-colors
                                {{ request()->is('obat*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                            <i data-feather="box" class="w-5 h-5"></i>
                            <span class="ml-3 flex-1 text-left">Obat</span>
                            <svg :class="{ 'rotate-180': activeDropdown === 'obat' }"
                                class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <div class="flex space-x-1">
                                @if($stokMenipis > 0)
                                    <span class="bg-yellow-500 text-xs px-2 py-0.5 rounded-full">{{ $stokMenipis }}</span>
                                @endif
                                @if($stokHabis > 0)
                                    <span class="bg-red-500 text-xs px-2 py-0.5 rounded-full">{{ $stokHabis }}</span>
                                @endif
                            </div>

                        </button>

                        <div x-show="activeDropdown === 'obat'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-10 mt-1 space-y-1">


                            <a href="{{ route('obat.index') }}" class="flex justify-between px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors
                                    {{ request()->is('obat') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Daftar Obat
                            </a>

                            <a href="{{ route('obat.create') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors 
                                    {{ request()->is('obat/create') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Tambah Obat
                            </a>

                            <a href="{{ route('obat.index', ['filter' => 'menipis']) }}"
                                class="block px-4 py-2 rounded hover:bg-yellow-600 hover:text-white transition-colors 
                                    {{ request()->fullUrlIs('*obat*filter=menipis*') ? 'bg-yellow-700 text-white font-semibold' : 'text-gray-300' }}">
                                Stok Menipis
                                @if($stokMenipis > 0)
                                    <span class="bg-yellow-500 text-xs px-2 py-0.5 rounded-full">{{ $stokMenipis }}</span>
                                @endif
                            </a>

                            <a href="{{ route('obat.index', ['filter' => 'habis']) }}"
                                class="block px-4 py-2 rounded hover:bg-red-600 hover:text-white transition-colors 
                                    {{ request()->fullUrlIs('*obat*filter=habis*') ? 'bg-red-700 text-white font-semibold' : 'text-gray-300' }}">
                                Stok Habis
                                @if($stokHabis > 0)
                                    <span class="bg-red-500 text-xs px-2 py-0.5 rounded-full">{{ $stokHabis }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Supplier Dropdown -->
                    <div>
                        <button @click="activeDropdown = (activeDropdown === 'supplier' ? null : 'supplier')" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-blue-700 hover:text-white transition-colors
                                {{ request()->is('supplier*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                            <i data-feather="users" class="w-5 h-5"></i>
                            <span class="ml-3 flex-1 text-left">Supplier</span>
                            <svg :class="{ 'rotate-180': activeDropdown === 'supplier' }"
                                class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeDropdown === 'supplier'" x-transition x-cloak class="ml-10 mt-1 space-y-1">
                            <a href="{{ route('supplier.index') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('supplier') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Daftar Supplier
                            </a>
                            <a href="{{ route('supplier.create') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('supplier/create') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Tambah Supplier
                            </a>
                        </div>
                    </div>

                    <!-- Pembelian Dropdown -->
                    <div>
                        <button @click="activeDropdown = (activeDropdown === 'pembelian' ? null : 'pembelian')" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-blue-700 hover:text-white transition-colors
                                {{ request()->is('pembelian*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                            <i data-feather="shopping-cart" class="w-5 h-5"></i>
                            <span class="ml-3 flex-1 text-left">Pembelian</span>
                            <svg :class="{ 'rotate-180': activeDropdown === 'pembelian' }"
                                class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeDropdown === 'pembelian'" x-transition x-cloak class="ml-10 mt-1 space-y-1">
                            <a href="{{ route('pembelian.index') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('pembelian') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Daftar Pembelian
                            </a>
                            <a href="{{ route('pembelian.create') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('pembelian/create') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Tambah Pembelian
                            </a>
                        </div>
                    </div>

                    <!-- Retur Dropdown -->
                    <div>
                        <button @click="activeDropdown = (activeDropdown === 'retur' ? null : 'retur')" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-blue-700 hover:text-white transition-colors
                                {{ request()->is('retur*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                            <i data-feather="corner-up-left" class="w-5 h-5"></i>
                            <span class="ml-3 flex-1 text-left">Retur Barang</span>
                            <svg :class="{ 'rotate-180': activeDropdown === 'retur' }"
                                class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeDropdown === 'retur'" x-transition x-cloak class="ml-10 mt-1 space-y-1">
                            <a href="{{ route('retur.index') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('retur') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Riwayat Retur
                            </a>
                            <a href="{{ route('retur.create') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('retur/create') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Tambah Retur
                            </a>
                        </div>
                    </div>

                    <!-- Laporan -->
                    <a href="{{ route('laporan.index') }}"
                        class="flex items-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors {{ request()->is('laporan*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                        <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                        <span class="ml-3 flex-1">Laporan</span>
                    </a>

                    <!-- Management Kasir Dropdown -->
                    <div>
                        <button @click="activeDropdown = (activeDropdown === 'users' ? null : 'users')" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-blue-700 hover:text-white transition-colors
                                {{ request()->is('users*') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-200' }}">
                            <i data-feather="user-plus" class="w-5 h-5"></i>
                            <span class="ml-3 flex-1 text-left">Management Kasir</span>
                            <svg :class="{ 'rotate-180': activeDropdown === 'users' }"
                                class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeDropdown === 'users'" x-transition x-cloak class="ml-10 mt-1 space-y-1">
                            <a href="{{ route('users.index') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('users') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Daftar Kasir
                            </a>
                            <a href="{{ route('users.create') }}"
                                class="block px-4 py-2 rounded hover:bg-blue-600 hover:text-white transition-colors {{ request()->is('users/create') ? 'bg-blue-700 text-white font-semibold' : 'text-gray-300' }}">
                                Tambah Kasir
                            </a>
                        </div>
                    </div>

                @endif
            @endauth
        </nav>
    </aside>

    <script>
        feather.replace();
    </script>


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
        <main class="p-6 flex-1 overflow-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        feather.replace();

        // Dropdown toggle
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const icon = document.getElementById('obatIcon');
            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>

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