<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem Apotek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <main class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            
            <div class="text-center mb-8">
                <a href="#">
                    <img src="{{ asset('images/logo-apotek.png') }}" alt="Logo Apotek" 
                         class="h-20 w-20 mx-auto rounded-full object-cover shadow-md">
                </a>
            </div>

            <div class="space-y-4">
                
                {{-- Tombol Login Admin --}}
                <a href="{{ route('login.admin') }}" 
                   class="group flex w-full items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                    </div>
                    <div class="flex-grow text-left">
                        <h3 class="font-semibold text-slate-800">Admin</h3>
                        <p class="text-sm text-slate-500">Manajemen & Laporan</p>
                    </div>
                    <svg class="h-5 w-5 flex-shrink-0 text-slate-400 transition-transform duration-200 group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>

                {{-- Tombol Login Kasir --}}
                <a href="{{ route('login.kasir') }}" 
                   class="group flex w-full items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <div class="flex-grow text-left">
                        <h3 class="font-semibold text-slate-800">Kasir</h3>
                        <p class="text-sm text-slate-500">Point of Sale (POS)</p>
                    </div>
                    <svg class="h-5 w-5 flex-shrink-0 text-slate-400 transition-transform duration-200 group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
        </div>

        <footer class="text-center mt-6">
            <p class="text-sm text-slate-500">&copy; 2025 Sistem Apotek. Semua hak cipta dilindungi.</p>
        </footer>
    </main>

</body>
</html>