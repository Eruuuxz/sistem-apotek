<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Apotek</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">

    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8 items-center">

            {{-- Bagian kiri: Card Welcome --}}
            <div class="bg-white shadow-2xl rounded-2xl p-10 text-center border border-gray-200 max-w-md mx-auto">

                {{-- Logo Apotek --}}
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/logo-apotek.png') }}" alt="" class="h-20 w-auto">
                </div>

                {{-- Judul --}}
                <h1 class="text-3xl font-extrabold text-gray-800 mb-3">
                    Selamat Datang di <span class="text-blue-600">Apotek Liz Farma 02</span>
                </h1>

                {{-- Deskripsi --}}
                <p class="text-gray-600 mb-8">
                    Sistem Informasi Apotek untuk manajemen obat, transaksi, dan laporan.
                </p>

                {{-- Tombol Login --}}
                <a href="{{ url('/pilih-login') }}"
                    class="inline-block bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg transition transform hover:-translate-y-0.5">
                    Login
                </a>
            </div>

            {{-- Bagian kanan: Ilustrasi --}}
            <div class="hidden md:flex justify-center">
                <img src="{{ asset('images/ilus.jpg') }}" alt="Ilustrasi Apotek" class="max-h-[400px]">
            </div>

        </div>
    </div>

</body>

</html>