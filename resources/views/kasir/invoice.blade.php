<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $penjualan->no_nota }}</title>
    <style>
        @page {
            size: 58mm; /* Ukuran kertas printer termal */
            margin: 0;
        }
        body {
            font-family: 'monospace', sans-serif;
            font-size: 10px;
            color: #000;
            margin: 5mm; /* Margin di sekitar konten */
            padding: 0;
            width: 48mm; /* Lebar konten agar tidak terpotong */
        }
        .container {
            width: 100%;
        }
        .header, .footer {
            text-align: center;
        }
        .header h3 {
            margin: 0;
            font-size: 12px;
        }
        .header p {
            margin: 2px 0;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-table, .items-table, .totals-table {
            width: 100%;
            font-size: 10px;
        }
        .info-table td {
            padding: 1px 0;
        }
        .items-table .item-name {
            display: block;
        }
        .items-table .item-details {
            display: flex;
            justify-content: space-between;
        }
        .totals-table td {
            padding: 1px 0;
            text-align: right;
        }
        .totals-table td:first-child {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h3>Apotek LIZ Farma 02</h3>
            <p>JL. RAYA BATUJAJAR NO. 321</p>
            <p>Telp. 08125457845</p>
        </div>

        <div class="separator"></div>

        <table class="info-table">
            <tr>
                <td>No Nota</td>
                <td class="text-right">{{ $penjualan->no_nota }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="text-right">{{ $penjualan->kasir->name ?? '-' }}</td>
            </tr>
             <tr>
                <td>Pelanggan</td>
                <td class="text-right">{{ $penjualan->nama_pelanggan ?? '-' }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        <div class="items-table">
            @foreach($penjualan->details as $item)
                <div class="item">
                    <span class="item-name">{{ $item->obat->nama ?? 'Obat Dihapus' }}</span>
                    <div class="item-details">
                        <span>{{ $item->qty }}x @ {{ number_format($item->harga, 0, ',', '.') }}</span>
                        <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="separator"></div>

        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td>{{ number_format($penjualan->subtotal_attribute, 0, ',', '.') }}</td>
            </tr>
             @if($penjualan->ppn_amount > 0)
            <tr>
                <td>PPN:</td>
                <td>{{ number_format($penjualan->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($penjualan->diskon_amount > 0)
            <tr>
                <td>Diskon:</td>
                <td>-{{ number_format($penjualan->diskon_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>TOTAL:</strong></td>
                <td><strong>{{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Bayar:</td>
                <td>{{ number_format($penjualan->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali:</td>
                <td>{{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        <div class="footer">
            <p>Terima Kasih</p>
            <p>Semoga Sehat Selalu</p>
        </div>
    </div>
</body>
</html>