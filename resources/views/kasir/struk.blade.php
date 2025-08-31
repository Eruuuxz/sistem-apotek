<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Faktur Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .container { border: 2px solid #000; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; font-size: 12px; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; }
        .right { text-align: right; }
        .total-box { border: 1px solid #000; padding: 8px; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER TOKO -->
        <div class="header">
            <h2>APOTEK SIMPANG CIMAREME</h2>
            <p>Jl. Raya Cimareme RT 01 RW 02, Ds. Cimareme, Kec. Ngamprah</p>
            <p>No Nota: {{ $penjualan->no_nota }} | Tanggal: {{ $penjualan->tanggal }}</p>
            <p>Kasir: {{ $penjualan->kasir_nama }}</p>
        </div>

        <!-- DETAIL PEMBELIAN -->
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th class="right">Harga</th>
                    <th class="right">Qty</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->detail as $d)
                <tr>
                    <td>{{ $d->obat->kode }}</td>
                    <td>{{ $d->obat->nama }}</td>
                    <td class="right">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                    <td class="right">{{ $d->qty }}</td>
                    <td class="right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="total-box">
            Total: Rp {{ number_format($penjualan->total, 0, ',', '.') }}
        </div>

        <p style="margin-top:20px; font-size:11px; text-align:center;">
            Terima kasih telah berbelanja di Apotek Simpang Cimareme
        </p>
    </div>
</body>
</html>
