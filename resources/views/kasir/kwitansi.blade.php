<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $penjualan->no_nota }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 2px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #ccc; padding: 8px; }
        .items-table thead { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { float: right; width: 40%; margin-top: 20px; }
        .totals table { width: 100%; }
        .totals td { padding: 5px; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #555; }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h1>FAKTUR PENJUALAN</h1>
            <p><strong>Apotek LIZ Farma 02</strong></p>
            <p>JL. RAYA BATUJAJAR NO. 321, BATUJAJAR BARAT</p>
            <p>Telp: 08125457845</p>
        </div>
        <table class="info-table">
            <tr>
                <td width="50%"><strong>Kepada:</strong> {{ $penjualan->nama_pelanggan ?? '-' }}</td>
                <td width="50%" class="text-right"><strong>No. Faktur:</strong> {{ $penjualan->no_nota }}</td>
            </tr>
            <tr>
                <td><strong>Alamat:</strong> {{ $penjualan->alamat_pelanggan ?? '-' }}</td>
                <td class="text-right"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d F Y') }}</td>
            </tr>
             <tr>
                <td><strong>Telepon:</strong> {{ $penjualan->telepon_pelanggan ?? '-' }}</td>
                <td class="text-right"><strong>Kasir:</strong> {{ $penjualan->kasir->name ?? '-' }}</td>
            </tr>
        </table>
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $item->obat->nama ?? 'Obat Dihapus' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totals">
            <table>
                 <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp {{ number_format($penjualan->subtotal_attribute, 0, ',', '.') }}</td>
                </tr>
                 @if($penjualan->diskon_amount > 0)
                 <tr>
                    <td>Diskon</td>
                    <td class="text-right">- Rp {{ number_format($penjualan->diskon_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
                </tr>
                 <tr>
                    <td>Bayar</td>
                    <td class="text-right">Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</td>
                </tr>
                 <tr>
                    <td>Kembalian</td>
                    <td class="text-right">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
        <div class="footer">
            <p>Terima kasih telah berbelanja di Apotek LIZ Farma 02.</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
        </div>
    </div>
</body>
</html>

