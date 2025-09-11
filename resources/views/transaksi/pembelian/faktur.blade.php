<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian - {{ $p->no_faktur }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ccc;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header, .footer {
            width: 100%;
            margin-bottom: 20px;
        }

        .header .left, .header .right {
            display: inline-block;
            vertical-align: top;
        }

        .header .left {
            width: 60%;
        }

        .header .right {
            width: 38%;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 6px;
        }

        th {
            background-color: #eee;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .no-print {
            display: none;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="left">
                <h1>FAKTUR PEMBELIAN</h1>
                <p>Nomor: <strong>{{ $p->no_faktur }}</strong></p>
                <p>Tanggal: <strong>{{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y H:i:s') }}</strong></p>
            </div>
            <div class="right">
                <h2>APOTEK LIZ Farma 02</h2>
                <p>JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005</p>
                <p>Telp: (022) 123456</p>
            </div>
        </div>

        <div>
            <h3>Supplier:</h3>
            <p>{{ $p->supplier->nama ?? '-' }}</p>
            <p>{{ $p->supplier->alamat ?? '-' }}</p>
            <p>Telp: {{ $p->supplier->telepon ?? '-' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($p->detail as $i => $d)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $d->obat->nama ?? '-' }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($d->harga_beli,0,',','.') }}</td>
                    <td class="text-right">Rp {{ number_format($d->jumlah * $d->harga_beli,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
<tfoot>
    <tr>
        <td colspan="4" class="text-right"><strong>Subtotal</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($p->total,0,',','.') }}</strong></td>
    </tr>
    <tr>
        <td colspan="4" class="text-right"><strong>Diskon</strong></td>
        <td class="text-right">
            @if($p->diskon_type == 'persen')
                {{ $p->diskon }}% 
                (Rp {{ number_format(($p->total * $p->diskon / 100),0,',','.') }})
            @else
                Rp {{ number_format($p->diskon,0,',','.') }}
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="4" class="text-right"><strong>Total Akhir</strong></td>
        <td class="text-right">
            <strong>
                Rp 
                @if($p->diskon_type == 'persen')
                    {{ number_format($p->total - ($p->total * $p->diskon / 100),0,',','.') }}
                @else
                    {{ number_format($p->total - $p->diskon,0,',','.') }}
                @endif
            </strong>
        </td>
    </tr>
</tfoot>

        </table>

        <div class="footer" style="margin-top:40px;">
            <div style="width:45%; display:inline-block; text-align:center;">
                <p>Diterima Oleh,</p>
                <br><br>
                <p><strong>Admin Apotek</strong></p>
            </div>
            <div style="width:45%; display:inline-block; text-align:center;">
                <p>Hormat Kami,</p>
                <br><br>
                <p><strong>{{ $p->supplier->nama ?? 'Supplier' }}</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
