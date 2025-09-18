<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Penjualan - Periode {{ $periode }}</h2>

    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Tanggal & Jam</th>
                <th>No. Transaksi</th>
                <th>Nama Obat (Qty)</th>
                <th>Jumlah Obat</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandJumlah = 0;
                $grouped = $penjualan->groupBy(fn($p) => \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l'));
            @endphp

            @foreach($grouped as $hari => $listPenjualan)
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
                        <td>{{ $pj->kode_transaksi ?? $pj->no_transaksi }}</td>
                        <td>
                            @foreach($pj->details as $detail)
                                {{ $detail->obat->nama }} ({{ $detail->qty }})<br>
                            @endforeach
                        </td>
                        <td class="text-right">{{ $jumlah }}</td>
                        <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">{{ $grandJumlah }}</td>
                <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
