<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Apotek Liz Farma 02')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        body { opacity: 0; transition: opacity 0.3s ease-in-out; }
        body.loaded { opacity: 1; }
        
        /* Custom Scrollbar untuk Sidebar agar lebih rapi */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
    </style>
</head>

<body class="bg-gray-50 flex min-h-screen font-sans text-gray-800">

@php
    // Logika notifikasi stok
    $stokHabis = \App\Models\Obat::where('stok', 0)->count();
    $stokMenipis = \App\Models\Obat::whereBetween('stok', [1, 10])->count();
    $stokExpired = \App\Models\Obat::whereNotNull('expired_date')->where('expired_date', '<', now())->count();
    $stokHampirExpired = \App\Models\Obat::whereNotNull('expired_date')->whereBetween('expired_date', [now(), now()->addMonth()])->count();
    $totalObatNotif = $stokHabis + $stokMenipis + $stokExpired + $stokHampirExpired;
    
    // Logika notifikasi SP
    $pendingSP = \App\Models\SuratPesanan::where('status', 'pending')->count();
@endphp

{{-- SIDEBAR (TEMA PUTIH) --}}
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed inset-y-0 left-0 z-30 h-screen transition-all duration-300">
    
    {{-- Logo Area --}}
    <div class="h-20 flex items-center justify-center border-b border-gray-100 px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <div class="bg-blue-600 text-white p-2 rounded-lg group-hover:bg-blue-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <div class="leading-tight">
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">Liz Farma 02</h1>
                <p class="text-xs text-gray-500 font-medium">Sistem Apotek</p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 pt-6 space-y-1 sidebar-scroll overflow-y-auto" x-data="{ activeDropdown: '{{ explode('.', request()->route()->getName())[0] }}' }">
        @auth
        @if(Auth::user()->role === 'admin')
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 font-medium {{ request()->is('dashboard*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-feather="home" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            
            <p class="px-4 pt-6 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Master Data</p>
            
            {{-- Dropdown Obat --}}
            <div>
                <button @click="activeDropdown = (activeDropdown === 'obat' ? null : 'obat')" 
                    class="flex items-center w-full gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 font-medium justify-between {{ request()->is('obat*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-feather="package" class="w-5 h-5"></i>
                        <span>Obat</span>
                    </div>
                    <div class="flex items-center">
                        @if($totalObatNotif > 0)
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white mr-2">{{ $totalObatNotif }}</span>
                        @endif
                        <svg :class="{ 'rotate-180': activeDropdown === 'obat' }" class="w-4 h-4 transition-transform duration-200 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </button>
                
                <div x-show="activeDropdown === 'obat'" x-transition.origin.top x-cloak class="mt-1 space-y-1 pl-4 pr-2">
                    <a href="{{ route('obat.index') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('obat') && !request('filter') ? 'text-blue-600 bg-blue-50/50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Daftar Obat</a>
                    <a href="{{ route('obat.create') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('obat/create') ? 'text-blue-600 bg-blue-50/50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Tambah Obat</a>
                    
                    {{-- Submenu Filter Stok --}}
                    <div class="pt-2 pb-1 border-t border-gray-100 mt-1">
                        <p class="px-4 text-[10px] uppercase font-bold text-gray-400 mb-1">Monitoring</p>
                        <a href="{{ route('obat.index', ['filter' => 'menipis']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=menipis*') ? 'text-yellow-600 bg-yellow-50 font-medium' : 'text-gray-500 hover:text-yellow-600' }}">
                            <span>Stok Menipis</span> 
                            @if($stokMenipis > 0)<span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stokMenipis }}</span>@endif
                        </a>
                        <a href="{{ route('obat.index', ['filter' => 'habis']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=habis*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-500 hover:text-red-600' }}">
                            <span>Stok Habis</span> 
                            @if($stokHabis > 0)<span class="bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stokHabis }}</span>@endif
                        </a>
                         <a href="{{ route('obat.index', ['filter' => 'kadaluarsa']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=kadaluarsa*') ? 'text-orange-600 bg-orange-50 font-medium' : 'text-gray-500 hover:text-orange-600' }}">
                            <span>Kadaluarsa</span> 
                            @if($stokExpired + $stokHampirExpired > 0)<span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stokExpired + $stokHampirExpired }}</span>@endif
                        </a>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('supplier.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 font-medium {{ request()->is('supplier*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-feather="truck" class="w-5 h-5"></i>
                <span>Supplier</span>
            </a>

            <p class="px-4 pt-6 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Transaksi</p>
            
            <a href="{{ route('pembelian.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-lg transition-all duration-200 font-medium {{ request()->is('pembelian*') || request()->is('surat_pesanan*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i data-feather="shopping-cart" class="w-5 h-5"></i>
                    <span>Pembelian</span>
                </div>
                @if($pendingSP > 0)
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-100 text-[10px] font-bold text-purple-600">{{ $pendingSP }}</span>
                @endif
            </a>
            
            <a href="{{ route('retur.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 font-medium {{ request()->is('retur*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-feather="corner-up-left" class="w-5 h-5"></i>
                <span>Retur Barang</span>
            </a>

        @endif
        @endauth
    </nav>
    
    {{-- User Profile Bottom --}}
    <div class="p-4 border-t border-gray-100">
        @auth
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=eff6ff&color=3b82f6&size=40" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                <div class="text-left flex-1 min-w-0">
                     <p class="font-semibold text-sm text-gray-700 truncate">{{ Auth::user()->name }}</p>
                     <p class="text-xs text-gray-500 truncate">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
                <i data-feather="chevron-up" class="w-4 h-4 text-gray-400"></i>
            </button>

            {{-- Dropdown Profile --}}
            <div x-show="open" @click.away="open = false" x-transition.origin.bottom x-cloak class="absolute bottom-full left-0 w-full mb-2 bg-white rounded-lg shadow-xl border border-gray-100 py-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 flex items-center gap-2">
                    <i data-feather="user" class="w-4 h-4"></i> Profil Saya
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center gap-2">
                        <i data-feather="log-out" class="w-4 h-4"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</aside>


{{-- MAIN CONTENT WRAPPER --}}
<div class="flex-1 flex flex-col ml-64 bg-gray-50 min-h-screen">
    
    {{-- HEADER (PUTIH) --}}
    <header class="bg-white/80 backdrop-blur-md px-8 h-20 flex justify-between items-center border-b border-gray-200 sticky top-0 z-20">
        <div class="flex items-center gap-4">
            @if (!request()->routeIs('dashboard'))
                <a href="{{ url()->previous() }}" title="Kembali" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors">
                    <i data-feather="arrow-left" class="w-5 h-5"></i>
                </a>
            @endif
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">@yield('title', 'Dashboard')</h1>
        </div>

        <div class="flex items-center gap-6">
             <div class="text-right hidden sm:block">
                <div id="date" class="font-semibold text-sm text-gray-700"></div>
                <div id="clock" class="text-xs text-gray-500 font-medium"></div>
             </div>
             
             {{-- Notification Icon (Optional Placeholder) --}}
             <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                 <i data-feather="bell" class="w-5 h-5"></i>
                 @if($totalObatNotif + $pendingSP > 0)
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                 @endif
             </button>
        </div>
    </header>

    {{-- CONTENT AREA --}}
    <main class="p-8 flex-1 overflow-y-auto">
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
        
        // Smooth transition for internal links
        document.querySelectorAll("a[href]:not([target='_blank']):not([href^='#'])").forEach(link => {
            link.addEventListener("click", function (e) {
                if(!this.closest('[x-data]')) { // Don't trigger on alpine dropdowns
                    // Optional: Add loading state logic here
                }
            });
        });
    });
</script>
</body>
</html>