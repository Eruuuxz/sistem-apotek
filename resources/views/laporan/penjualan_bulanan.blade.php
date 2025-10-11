@extends('layouts.admin')

@section('title', 'Laporan Penjualan (Bulanan)')

@section('content')

    {{-- Navigasi Bulan --}}
<div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-2">
    {{-- Bulan Sebelumnya --}}
    <a href="{{ route('laporan.penjualan.bulanan', ['periode' => $prevMonth['tahun'].'-'.str_pad($prevMonth['bulan'],2,'0',STR_PAD_LEFT)]) }}"
       class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">← Bulan Sebelumnya</a>

    {{-- Form pilih bulan --}}
    <form method="GET" class="flex items-center gap-2">
        <span class="font-bold">{{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</span>
        <input type="month" name="periode" value="{{ $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
               max="{{ now()->format('Y-m') }}" class="border px-3 py-2 rounded">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat</button>
    </form>

    {{-- Bulan Berikutnya --}}
    @if(!($bulan == now()->month && $tahun == now()->year))
        <a href="{{ route('laporan.penjualan.bulanan', ['periode' => $nextMonth['tahun'].'-'.str_pad($nextMonth['bulan'],2,'0',STR_PAD_LEFT)]) }}"
           class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">Bulan Berikutnya →</a>
    @else
        <span class="px-4 py-2 text-gray-400">Bulan Berikutnya →</span>
    @endif
</div>

    {{-- Ringkasan --}}
    <div class="bg-white p-4 shadow-md rounded mb-6 flex flex-col md:flex-row justify-between items-center gap-2">
        <div class="text-sm md:text-base">
            <span class="font-semibold">Jumlah Transaksi:</span>
            <span class="font-bold text-blue-600">{{ $jumlahTransaksi }}</span>

            <span class="ml-6 font-semibold">Total Penjualan:</span>
            <span class="font-bold text-green-600">Rp {{ number_format($totalAll, 0, ',', '.') }}</span>

            <span class="ml-6 font-semibold">Total Obat Terjual:</span>
            <span class="font-bold text-purple-600">{{ $totalObatTerjual }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('laporan.penjualan.bulanan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">PDF</a>
            <a href="{{ route('laporan.penjualan.bulanan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Excel</a>
        </div>
    </div>


    {{-- Tabel Rekap Per Hari --}}
    <div class="overflow-x-auto bg-white shadow-md rounded p-4">
        <h3 class="text-lg font-semibold mb-3">Rekap Per Hari</h3>
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-left">Hari</th>
                    <th class="px-4 py-2 border text-left">Tanggal</th>
                    <th class="px-4 py-2 border text-right">Total Penjualan</th>
                    <th class="px-4 py-2 border text-center">Total Obat Terjual</th>
                    <th class="px-4 py-2 border text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dataPerHari = $data->groupBy(fn($d) => \Carbon\Carbon::parse($d->tanggal)->format('Y-m-d'));
                @endphp
                @php
                    \Carbon\Carbon::setLocale('id');
                @endphp


                @foreach($dataPerHari as $tanggal => $rows)
                    @php
                        $carbon = \Carbon\Carbon::parse($tanggal);
                        $tanggalFormat = $carbon->format('d-m-Y');
                        $hari = $carbon->translatedFormat('l');
                        $totalHarian = $rows->sum('total');
                        $totalQtyHarian = $rows->flatMap(fn($r) => $r->details)->sum('qty');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $hari }}</td>
                        <td class="border px-4 py-2">{{ $tanggalFormat }}</td>
                        <td class="border px-4 py-2 text-right">Rp {{ number_format($totalHarian, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2 text-center">{{ $totalQtyHarian }}</td>
                        <td class="border px-4 py-2 text-center">
                            <button onclick="openModal('{{ $tanggal }}')"
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs">Detail</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 p-6 rounded shadow-lg relative">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-black">✕</button>
            <h2 class="text-lg font-bold mb-4">Detail Transaksi</h2>
            <div id="modalContent">
                {{-- Isi tabel transaksi harian dimasukkan lewat JS --}}
            </div>
        </div>
    </div>

    @php
        // Data bersih untuk JS
        $dataForJs = $dataPerHari->map(function ($rows) {
            return $rows->map(function ($r) {
                return [
                    'no_nota' => $r->no_nota,
                    'kasir' => $r->kasir->name ?? '-',
                    'total' => $r->total,
                    'total_qty' => $r->total_qty ?? 0,
                    'tanggal' => \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d H:i:s'), // Tambahkan format jam
                    'details' => $r->details->map(function ($d) {
                        return [
                            'nama' => $d->obat->nama,
                            'qty' => $d->qty,
                        ];
                    })->toArray(),
                ];
            })->toArray();
        })->toArray();
    @endphp

    <script>
        const dataByDate = @json($dataForJs);

        function openModal(tanggal) {
            let rows = dataByDate[tanggal];
            let html = `
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border text-left">No Nota</th>
                                <th class="px-4 py-2 border text-left">Kasir</th>
                                <th class="px-4 py-2 border text-left">Tanggal & Waktu</th> {{-- Tambahkan header --}}
                                <th class="px-4 py-2 border text-right">Total</th>
                                <th class="px-4 py-2 border text-center">Item</th>
                                <th class="px-4 py-2 border text-center">Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

            rows.forEach(r => {
                let items = r.details.map(d => `${d.nama} (${d.qty})`).join(', ');
                html += `
                        <tr>
                            <td class="border px-4 py-2">${r.no_nota}</td>
                            <td class="border px-4 py-2">${r.kasir}</td>
                            <td class="border px-4 py-2">${r.tanggal}</td> {{-- Tampilkan tanggal dengan jam --}}
                            <td class="border px-4 py-2 text-right">Rp ${Number(r.total).toLocaleString('id-ID')}</td>
                            <td class="border px-4 py-2">${items}</td>
                            <td class="border px-4 py-2 text-center">${r.total_qty}</td>
                        </tr>
                    `;
            });

            html += `</tbody></table>`;
            document.getElementById('modalContent').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }
    </script>

@endsection