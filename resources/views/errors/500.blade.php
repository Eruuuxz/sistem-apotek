@extends('layouts.admin')

@section('title', 'Kesalahan Server')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-300">500</h1>
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Kesalahan Server</h2>
        <p class="text-gray-600 mb-8">Terjadi kesalahan pada server. Tim kami telah diberitahu dan sedang menangani masalah ini.</p>
        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
