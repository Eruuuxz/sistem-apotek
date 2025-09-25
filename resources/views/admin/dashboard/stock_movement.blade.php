@extends('layouts.admin')

@section('title', 'Stock Movement Dashboard')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Stock Movement Dashboard</h1>

    <form method="GET" action="{{ route('stock.movement') }}" class="mb-6">
        <label for="period" class="mr-2 font-medium">Filter Periode:</label>
        <select name="period" id="period" onchange="this.form.submit()" class="border rounded px-3 py-1">
            <option value="3" {{ $period == '3' ? 'selected' : '' }}>3 Bulan</option>
            <option value="6" {{ $period == '6' ? 'selected' : '' }}>6 Bulan</option>
            <option value="12" {{ $period == '12' ? 'selected' : '' }}>1 Tahun</option>
        </select>
    </form>

    <div class="grid grid-cols-3 gap-6">
        <div class="bg-green-500 text-white rounded-lg p-6 flex flex-col items-center justify-center">
            <div class="text-4xl font-bold">{{ $summary['Fast Moving'] ?? 0 }} Obat</div>
            <div class="mt-2 text-lg">Fast Moving</div>
        </div>
        <div class="bg-yellow-400 text-gray-900 rounded-lg p-6 flex flex-col items-center justify-center">
            <div class="text-4xl font-bold">{{ $summary['Slow Moving'] ?? 0 }} Obat</div>
            <div class="mt-2 text-lg">Slow Moving</div>
        </div>
        <div class="bg-red-600 text-white rounded-lg p-6 flex flex-col items-center justify-center">
            <div class="text-4xl font-bold">{{ $summary['Dead Stock'] ?? 0 }} Obat</div>
            <div class="mt-2 text-lg">Dead Stock</div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('stock.movement.detail', ['period' => $period]) }}"
            class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
            Detail
        </a>
    </div>
</div>
@endsection