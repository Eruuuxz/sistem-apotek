{{-- File: /views/kasir/struk.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Penjualan</title>
    <style>
        @page {
            size: A6 landscape;
            margin: 10px;
        }

        body {
            font-family: Arial;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            border: 2px solid #000;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .details-section .right-details {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 10px;
        }

        table,
        th,
        td {
            border: 2px solid #000;
        }

        th,
        td {
            padding: 5px;
        }

        th {
            text-align: center;
        }

        .totals {
            text-align: right;
            margin-top: 10px;
            line-height: 1.5;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .footer-section .sign {
            text-align: center;
            width: 45%;
        }

        .footer-section .sign .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="invoice-container">
        <div class="header">
            <h3>STRUK PENJUALAN</h3>
        </div>

        <div class="details-section">
            <div class="left-details">
                <div>No Faktur: <strong>{{ $penjualan->no_nota }}</strong></div>
                <div>Kasir: {{ $penjualan->kasir->name ?? '-' }}</div>
                <div>Tgl: {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y H:i:s') }}</div> {{-- Format tanggal dengan jam --}}
            </div>
            <div class="right-details">
                <div>Apotek LIZ Farma 02</div>
                <div>JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005</div>
                <div>KEL. BATUJAJAR BARAT KEC. BATUJAJAR</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:5%">NO</th>
                    <th>KETERANGAN</th>
                    <th style="width:15%">BANYAKNYA</th>
                    <th style="width:20%">HARGA SATUAN</th>
                    <th style="width:20%">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualan->details ?? [] as $index => $item)
                    <tr>
                        <td style="text-align:center">{{ $index + 1 }}</td>
                        <td>{{ $item->obat->nama ?? '-' }}</td>
                        <td style="text-align:center">{{ $item->qty }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td style="text-align:right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center">Tidak ada item penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <div>Sub Total: Rp {{ number_format($penjualan->total, 0, ',', '.') }}</div>
            <div>Bayar: Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</div>
            <div>
                @if ($penjualan->kembalian >= 0)
                    Kembalian: Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}
                @else
                    Kekurangan: Rp {{ number_format(abs($penjualan->kembalian), 0, ',', '.') }}
                @endif
            </div>
        </div>

        <div class="footer-section">
            <div class="sign">
                Penerima,<br><br><br>
                <div class="name">(________________)</div>
            </div>
            <div class="sign">
                Hormat Kami,<br><br><br>
                <div class="name">(Apotek LIZ Farma 02)</div>
            </div>
        </div>
    </div>
</body>

</html>