<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: 'sans-serif';
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .total-row td {
            font-weight: bold;
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Penjualan</h2>
        <p>Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Tanggal & Jam</th>
                <th>No. Nota</th>
                <th>Nama Obat (Qty)</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandJumlah = 0;
                // Mengurutkan penjualan berdasarkan tanggal sebelum dikelompokkan
                $sortedPenjualan = $penjualan->sortBy('tanggal');
                $grouped = $sortedPenjualan->groupBy(fn($p) => \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l'));
            @endphp

            @forelse($grouped as $hari => $listPenjualan)
                @php $rowspan = $listPenjualan->count(); @endphp
                @foreach($listPenjualan as $i => $pj)
                    @php
                        $tglJam = \Carbon\Carbon::parse($pj->tanggal)->format('d-m-Y H:i');
                        $jumlah = $pj->details->sum('qty');
                        $total = $pj->total;
                        $grandTotal += $total;
                        $grandJumlah += $jumlah;
                    @endphp
                    <tr>
                        @if($i === 0)
                            <td rowspan="{{ $rowspan }}">{{ $hari }}</td>
                        @endif
                        <td>{{ $tglJam }}</td>
                        {{-- MENGGUNAKAN no_nota --}}
                        <td>{{ $pj->no_nota }}</td>
                        <td>
                            @foreach($pj->details as $detail)
                                - {{ $detail->obat->nama }} ({{ $detail->qty }})<br>
                            @endforeach
                        </td>
                        <td class="text-center">{{ $jumlah }}</td>
                        <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">GRAND TOTAL</td>
                <td class="text-center">{{ $grandJumlah }}</td>
                <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>