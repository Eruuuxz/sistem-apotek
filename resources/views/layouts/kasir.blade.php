<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Kasir Apotek')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 flex min-h-screen font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-900 text-white flex flex-col shadow-xl fixed inset-y-0 left-0 z-20">
        <!-- Brand -->
        <div class="p-6 text-2xl font-bold border-b border-green-800 flex items-center justify-center tracking-wide">
            Apotek <span class="text-green-300 ml-1">LIZ Farma 02</span>
        </div>

        <!-- Navigation -->
        <nav class="mt-6 flex-1 px-2 space-y-2">
            @auth
                @if(in_array(Auth::user()->role, ['kasir']))

                    {{-- POS Penjualan --}}
                    <a href="{{ route('pos.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition 
                                      {{ request()->routeIs('pos.*') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-800 text-gray-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 opacity-80" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <span>POS Penjualan</span>
                    </a>

                    {{-- Riwayat Penjualan --}}
                    <a href="{{ route('kasir.riwayat') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition 
                                      {{ request()->routeIs('kasir.riwayat') ? 'bg-green-700 text-white font-semibold' : 'hover:bg-green-800 text-gray-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 opacity-80" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Riwayat Penjualan</span>
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


        <!-- Page Content -->
        <main class="p-6 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>