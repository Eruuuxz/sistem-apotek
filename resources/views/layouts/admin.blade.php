<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Apotek Liz Farma 02')</title>
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

@php
    $stokHabis = \App\Models\Obat::where('stok', 0)->count();
    $stokMenipis = \App\Models\Obat::whereBetween('stok', [1, 10])->count();
    $stokExpired = \App\Models\Obat::whereNotNull('expired_date')->where('expired_date', '<', now())->count();
    $stokHampirExpired = \App\Models\Obat::whereNotNull('expired_date')->whereBetween('expired_date', [now(), now()->addMonth()])->count();
    $totalObatNotif = $stokHabis + $stokMenipis + $stokExpired + $stokHampirExpired;
    $pendingSP = \App\Models\SuratPesanan::where('status', 'pending')->count();
@endphp

<aside class="w-64 bg-blue-900 text-white flex flex-col shadow-lg fixed inset-y-0 left-0 z-30">
    <div class="p-6 h-20 flex items-center justify-center border-b border-blue-800/50">
        <h1 class="text-xl font-bold text-white tracking-wide">
            Apotek <span class="text-blue-300">Liz Farma 02</span>
        </h1>
    </div>

    <nav class="flex-1 px-4 pt-6 space-y-1 overflow-y-auto" x-data="{ activeDropdown: '{{ explode('.', request()->route()->getName())[0] }}' }">
        @auth
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('dashboard*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">
                <i data-feather="home" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            <p class="px-4 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-blue-400">Master</p>
            <div>
                <button @click="activeDropdown = (activeDropdown === 'obat' ? null : 'obat')" class="flex items-center w-full gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('obat*') || request()->is('stock-movement*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">
                    <i data-feather="box" class="w-5 h-5"></i>
                    <span class="flex-1 text-left">Obat</span>
                    @if($totalObatNotif > 0)
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold">{{ $totalObatNotif }}</span>
                    @endif
                    <svg :class="{ 'rotate-180': activeDropdown === 'obat' }" class="w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="activeDropdown === 'obat'" x-transition x-cloak class="mt-1 space-y-1 pl-10">
                    <a href="{{ route('obat.index') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('obat') && !request('filter') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}">Daftar Obat</a>
                    <a href="{{ route('obat.create') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('obat/create') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}">Tambah Obat</a>
                    <a href="{{ route('stock_movement.detail') }}" class="block px-4 py-2 text-sm rounded-lg transition-colors {{ request()->is('stock-movement/detail') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}">Analisis Stok</a>
                    <a href="{{ route('obat.index', ['filter' => 'menipis']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=menipis*') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}"><span>Stok Menipis</span> @if($stokMenipis > 0)<span class="bg-yellow-500 text-xs text-black font-bold px-1.5 py-0.5 rounded-full">{{ $stokMenipis }}</span>@endif</a>
                    <a href="{{ route('obat.index', ['filter' => 'habis']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=habis*') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}"><span>Stok Habis</span> @if($stokHabis > 0)<span class="bg-red-500 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $stokHabis }}</span>@endif</a>
                    <a href="{{ route('obat.index', ['filter' => 'kadaluarsa']) }}" class="flex justify-between items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->fullUrlIs('*obat*filter=kadaluarsa*') ? 'text-white font-semibold' : 'text-blue-300 hover:text-white' }}"><span>Kadaluarsa</span> @if($stokExpired + $stokHampirExpired > 0)<span class="bg-orange-500 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $stokExpired + $stokHampirExpired }}</span>@endif</a>
                </div>
            </div>
            <a href="{{ route('supplier.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('supplier*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="truck" class="w-5 h-5"></i><span>Supplier</span></a>
            <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('pelanggan*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="users" class="w-5 h-5"></i><span>Pelanggan</span></a>
            <p class="px-4 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-blue-400">Transaksi</p>
            <a href="{{ route('pembelian.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('pembelian*') || request()->is('surat_pesanan*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="shopping-cart" class="w-5 h-5"></i><span>Pembelian</span> @if($pendingSP > 0)<span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-xs font-bold">{{ $pendingSP }}</span>@endif</a>
            <a href="{{ route('retur.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('retur*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="corner-up-left" class="w-5 h-5"></i><span>Retur Barang</span></a>
            <a href="{{ route('biaya-operasional.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('biaya-operasional*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="dollar-sign" class="w-5 h-5"></i><span>Biaya Operasional</span></a>
            <p class="px-4 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-blue-400">Lainnya</p>
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('laporan*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="bar-chart-2" class="w-5 h-5"></i><span>Laporan</span></a>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors font-medium {{ request()->is('users*') ? 'bg-white/10 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}"><i data-feather="user-plus" class="w-5 h-5"></i><span>Manajemen Kasir</span></a>
        @endif
        @endauth
    </nav>
</aside>

<div class="flex-1 flex flex-col ml-64">
    <header class="bg-white/80 backdrop-blur-sm p-4 h-20 flex justify-between items-center border-b border-slate-200 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            @if (!request()->routeIs('dashboard'))
                <a href="{{ url()->previous() }}" title="Kembali" class="p-2 rounded-full hover:bg-slate-200 transition-colors">
                    <svg class="w-5 h-5 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('title', 'Dashboard')</h1>
        </div>

        <div class="flex items-center space-x-4">
             <div class="text-sm text-slate-600 font-medium text-right">
                <div id="date" class="font-semibold text-slate-800"></div>
                <div id="clock" class="text-xs"></div>
             </div>
            @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-200 transition-colors">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff&size=40" alt="Avatar" class="w-9 h-9 rounded-full">
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