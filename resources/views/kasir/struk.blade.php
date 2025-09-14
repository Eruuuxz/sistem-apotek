{{-- File: /views/kasir/struk.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .invoice-container {
            border: 2px solid #000;
            padding: 15px;
        }

        .details td.text-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }

        th {
            text-align: center;
            background: #f5f5f5;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }

        .header-left {
            width: 70%;
        }

        .header-left img {
            max-width: 70px;
            vertical-align: middle;
        }

        .header-left .info {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
        }

        .header-right {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
        }

        .details {
            margin-bottom: 10px;
        }

        .details td {
            border: none;
            padding: 3px;
        }

        .totals td {
            border: none;
            padding: 3px;
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
        }

        .sign {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="invoice-container">
        {{-- HEADER --}}
        <div class="header">
            <div class="header-left">
                <img src="{{ asset('images/logo-apotek.png') }}" alt="Logo">
                <div class="info">
                    <strong>Apotek LIZ Farma 02</strong><br>
                    JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005<br>
                    KEL. BATUJAJAR BARAT KEC. BATUJAJAR<br>
                    Telp. 08125457845, Email : support@vmedis.com<br>
                    Website : vmedis.com
                </div>
            </div>
            <div class="header-right">FAKTUR</div>
        </div>

        {{-- DETAIL TRANSAKSI & PELANGGAN --}}
        <table class="details" width="100%">
            <tr>
                <td style="width:50%;"><strong>Nama Pelanggan</strong> : {{ $penjualan->nama_pelanggan ?? '-' }}</td>
                <td class="text-right" style="width:50%;"><strong>Kasir</strong> : {{ $penjualan->kasir->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>No. Telp</strong> : {{ $penjualan->telepon_pelanggan ?? '-' }}</td>
                <td class="text-right"><strong>Tanggal</strong> :
                    {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Alamat</strong> : {{ $penjualan->alamat_pelanggan ?? '-' }}</td>
                <td class="text-right"><strong>No. Faktur</strong> : {{ $penjualan->no_nota }}</td>
            </tr>
            @if($penjualan->pelanggan)
            <tr>
                <td><strong>Status Member</strong> : {{ ucfirst($penjualan->pelanggan->status_member) }}</td>
                <td class="text-right"><strong>Poin Member</strong> : {{ number_format($penjualan->pelanggan->point, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($penjualan->details->firstWhere('no_ktp')) {{-- Cek jika ada detail dengan no_ktp --}}
            <tr>
                <td colspan="2"><strong>No. KTP</strong> : {{ $penjualan->details->firstWhere('no_ktp')->no_ktp }}</td>
            </tr>
            @endif
        </table>


        {{-- TABEL BARANG --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Expired Date</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details ?? [] as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $item->obat->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td>
                            @if($item->obat->expired_date)
                                (ED {{ \Carbon\Carbon::parse($item->obat->expired_date)->format('d-m-Y') }})
                            @else
                                (ED -)
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TOTAL --}}
        <table class="totals" width="100%">
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 15%; border: none; text-align: right;">Subtotal:</td>
                <td style="width: 15%; border: none; text-align: right;">Rp {{ number_format($penjualan->subtotal_attribute, 0, ',', '.') }}</td>
            </tr>
            @if($penjualan->diskon_amount > 0)
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 15%; border: none; text-align: right;">Diskon ({{ $penjualan->diskon_type == 'persen' ? $penjualan->diskon_value . '%' : 'Rp ' . number_format($penjualan->diskon_value, 0, ',', '.') }}):</td>
                <td style="width: 15%; border: none; text-align: right;">- Rp {{ number_format($penjualan->diskon_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 15%; border: none; text-align: right;">Total:</td>
                <td style="width: 15%; border: none; text-align: right;"><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 15%; border: none; text-align: right;">Bayar:</td>
                <td style="width: 15%; border: none; text-align: right;">Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="width: 70%; border: none;"></td>
                <td style="width: 15%; border: none; text-align: right;">Kembalian:</td>
                <td style="width: 15%; border: none; text-align: right;">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- CATATAN --}}
        <div class="footer">
            <p><strong>Catatan:</strong><br>
            Terimakasih telah berkunjung. Semoga sehat selalu.<br>
            Maaf, barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>

            <div style="text-align: right; margin-top: 50px; margin-right: 50px;">
                Kasir<br><br><br>
                __________________<br>
            </div>
        </div>

    </div>

</body>

</html>