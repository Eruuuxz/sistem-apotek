@extends('layouts.kasir')

@section('title', 'Ringkasan Shift Saya')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ringkasan Shift Saya</h1>
    </div>

    {{-- Filter berdasarkan tanggal --}}
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('shifts.my.summary') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow w-full">
                <label for="date" class="block text-sm font-medium text-gray-700">Filter Tanggal</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Tampilkan
            </button>
            @if(request('date'))
                <a href="{{ route('shifts.my.summary') }}" class="w-full md:w-auto px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg text-center transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel Ringkasan Shift --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Waktu Berakhir</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Modal Awal</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Total Penjualan</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Modal Akhir</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cashierShifts as $shift)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $shift->shift->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($shift->start_time)->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($shift->end_time)
                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('Y-m-d H:i') }}
                                @else
                                    <span class="text-yellow-500">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($shift->final_cash, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Tidak ada data shift yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $cashierShifts->links() }}
        </div>
    </div>
</div>
@endsection