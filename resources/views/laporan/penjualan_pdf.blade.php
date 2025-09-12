<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 18px;
        }

        p {
            text-align: center;
            margin: 0 0 15px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            font-size: 12px;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }

        td {
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        tfoot th {
            background-color: #e0e0e0;
            font-weight: bold;
            border-top: 2px solid #444;
        }
    </style>
</head>

@php
\Carbon\Carbon::setLocale('id');
@endphp

<body>
    <h3>Laporan Penjualan</h3>
    <p>
    @if(isset($bulan) && isset($tahun))
        Bulan: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
    @else
        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d-m-Y') }}
    @endif
</p>


    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:20%">No Nota</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:35%">Nama Obat (Qty)</th>
                <th style="width:10%">Total Qty</th>
                <th style="width:10%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rows as $penjualan)
                @php
                    $obatList = $penjualan->details
                        ->map(fn($d) => ($d->obat->nama ?? '-') . " ({$d->qty})")
                        ->join(', ');
                    $totalQty = $penjualan->details->sum('qty');
                    $subtotal = $penjualan->details->sum('subtotal');
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $penjualan->no_nota }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($penjualan->tanggal)->translatedFormat('l, d-m-Y H:i:s') }}</td>
                    <td>{{ $obatList }}</td>
                    <td class="text-center">{{ $totalQty }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($rows->sum(fn($p) => $p->details->sum('subtotal')), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>