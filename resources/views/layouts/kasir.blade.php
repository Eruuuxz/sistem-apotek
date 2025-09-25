<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS Kasir') - Apotek Liz Farma 02</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        body { opacity: 0; transition: opacity 0.3s ease-in-out; }
        body.loaded { opacity: 1; }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen font-sans">

<aside class="w-64 bg-green-900 text-white flex flex-col shadow-lg fixed inset-y-0 left-0 z-30">
    <div class="p-6 h-20 flex items-center justify-center border-b border-green-800/50">
        <h1 class="text-xl font-bold text-white tracking-wide">
            Apotek <span class="text-green-300">Liz Farma 02</span>
        </h1>
    </div>

    <nav class="flex-1 px-4 pt-6 space-y-1.5" x-data="{ activeDropdown: '{{ request()->is('pelanggan*') ? 'pelanggan' : '' }}' }">
        @auth
            @if(Auth::user()->role === 'kasir')
                
                <a href="{{ route('pos.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('pos.*') ? 'bg-green-600 text-white' : 'text-green-200 hover:bg-white/10 hover:text-white' }}">
                    <i data-feather="shopping-cart" class="w-5 h-5"></i>
                    <span>POS Penjualan</span>
                </a>

                <a href="{{ route('kasir.riwayat') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('kasir.riwayat') ? 'bg-green-600 text-white' : 'text-green-200 hover:bg-white/10 hover:text-white' }}">
                    <i data-feather="clock" class="w-5 h-5"></i>
                    <span>Riwayat Penjualan</span>
                </a>

                <a href="{{ route('kasir.summary') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('kasir.summary') ? 'bg-green-600 text-white' : 'text-green-200 hover:bg-white/10 hover:text-white' }}">
                   <i data-feather="briefcase" class="w-5 h-5"></i>
                    <span>Ringkasan Shift</span>
                </a>
                
                <div>
                    <button @click="activeDropdown = activeDropdown === 'pelanggan' ? null : 'pelanggan'"
                            class="flex items-center w-full gap-3 px-4 py-3 rounded-lg transition-colors font-medium {{ request()->is('pelanggan*') ? 'bg-green-600 text-white' : 'text-green-200 hover:bg-white/10 hover:text-white' }}">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span class="flex-1 text-left">Pelanggan</span>
                        <svg :class="{'rotate-180': activeDropdown === 'pelanggan'}" class="w-4 h-4 transition-transform duration-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeDropdown === 'pelanggan'" x-transition x-cloak class="mt-1 space-y-1 pl-10">
                        <a href="{{ route('pelanggan.index') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('pelanggan') && !request()->is('pelanggan/create') ? 'text-white font-semibold' : 'text-green-300 hover:text-white' }}">Daftar Pelanggan</a>
                        <a href="{{ route('pelanggan.create') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('pelanggan/create') ? 'text-white font-semibold' : 'text-green-300 hover:text-white' }}">Tambah Pelanggan</a>
                    </div>
                </div>
            @endif
        @endauth
    </nav>
</aside>

<div class="flex-1 flex flex-col ml-64">
    <header class="bg-white/80 backdrop-blur-sm p-4 h-20 flex justify-between items-center border-b border-slate-200 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            @if (!request()->routeIs('pos.index'))
                <a href="{{ url()->previous() }}" title="Kembali" class="p-2 rounded-full hover:bg-slate-200 transition-colors">
                    <svg class="w-5 h-5 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('title', 'POS Kasir')</h1>
        </div>
        
        <div class="flex items-center space-x-4">
             <div class="text-sm text-slate-600 font-medium text-right">
                <div id="date" class="font-semibold text-slate-800"></div>
                <div id="clock" class="text-xs"></div>
             </div>
            @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-200 transition-colors">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=16a34a&color=fff&size=40" alt="Avatar" class="w-9 h-9 rounded-full">
                    <div class="hidden md:block text-left">
                         <p class="font-semibold text-sm text-slate-800">{{ Auth::user()->name }}</p>
                         <p class="text-xs text-slate-500">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 border border-slate-200" x-cloak>
                    <div class="px-4 py-2 border-b border-slate-200">
                        <p class="font-semibold text-sm text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="p-6 flex-1 overflow-y-auto">
        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
    feather.replace();
    
    function updateClock() {
        const now = new Date();
        const dateEl = document.getElementById('date');
        const clockEl = document.getElementById('clock');
        if (dateEl && clockEl) {
            dateEl.innerText = now.toLocaleDateString('id-ID', {weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'});
            clockEl.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    document.addEventListener("DOMContentLoaded", function () {
        document.body.classList.add("loaded");
        document.querySelectorAll("a[href]:not([target='_blank']):not([href^='#'])").forEach(link => {
            link.addEventListener("click", function (e) {
                if(!this.closest('[x-data]')) {
                    e.preventDefault();
                    document.body.classList.remove("loaded");
                    setTimeout(() => { window.location.href = this.href; }, 300);
                }
            });
        });
    });
</script>
</body>
</html>