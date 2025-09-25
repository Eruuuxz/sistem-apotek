@extends('layouts.kasir')

@section('title', 'Ringkasan Shift Saya')

@section('content')
<div class="bg-white p-6 shadow-lg rounded-xl">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Ringkasan Shift Saya</h2>
            <p class="text-sm text-gray-500">Lacak riwayat shift dan total penjualan Anda.</p>
        </div>
    </div>

    <form action="{{ route('shifts.my.summary') }}" method="GET" class="flex items-center space-x-4 bg-gray-50 p-4 rounded-lg mb-6">
        <label for="date" class="font-medium text-sm text-gray-700">Filter Tanggal:</label>
        <input type="date" name="date" id="date" value="{{ request('date') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">Tampilkan</button>
        @if(request('date'))
            <a href="{{ route('shifts.my.summary') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-semibold">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Shift</th>
                    <th class="px-6 py-3 text-left">Waktu Mulai</th>
                    <th class="px-6 py-3 text-left">Waktu Berakhir</th>
                    <th class="px-6 py-3 text-right">Modal Awal</th>
                    <th class="px-6 py-3 text-right">Total Penjualan</th>
                    <th class="px-6 py-3 text-right">Modal Akhir Seharusnya</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($cashierShifts as $shift)
                    <tr class="border-b border-gray-200 hover:bg-blue-50/50">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $shift->shift->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($shift->start_time)->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($shift->end_time)
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('d M Y, H:i') }}
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Masih Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-green-600">Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-blue-600">
                            Rp {{ number_format($shift->initial_cash + $shift->total_sales, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data shift yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $cashierShifts->links() }}
    </div>
</div>
@endsection
