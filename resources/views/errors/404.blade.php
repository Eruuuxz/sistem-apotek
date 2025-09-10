@extends('layouts.admin')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-300">404</h1>
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-600 mb-8">Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>
        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
