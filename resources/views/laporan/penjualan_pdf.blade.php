<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px
        }

        th {
            background: #eee
        }
    </style>
</head>

<body>
    <h3>Laporan Penjualan</h3>
    <p>Periode: {{ $from ?? ' - ' }} s/d {{ $to ?? ' - ' }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Nota</th>
                <th>Tanggal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->no_nota }}</td>
                    <td>{{ $r->tanggal }}</td>
                    <td style="text-align:right">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right">TOTAL</th>
                <th style="text-align:right">Rp {{ number_format($totalAll, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>