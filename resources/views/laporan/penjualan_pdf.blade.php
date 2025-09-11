<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 20px; color: #333; }
        h3 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin: 0 0 15px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px 8px; font-size: 12px; }
        th { background: #f2f2f2; text-align: center; }
        td { vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        tfoot th { background: #f9f9f9; }
    </style>
</head>
<body>
    <h3>Laporan Penjualan</h3>
    <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:15%">No Nota</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:25%">Nama Obat</th>
                <th style="width:10%">Qty</th>
                <th style="width:15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rows as $penjualan)
                @foreach($penjualan->details as $detail)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ $penjualan->no_nota }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($penjualan->tanggal)->translatedFormat('l, d-m-Y H:i:s') }}
                        </td>
                        <td>{{ $detail->obat->nama_obat ?? '-' }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalAll, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>