<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Retur - {{ $retur->no_retur }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f0f0f0; text-align: center; }
        
        .no-border td { border: none; padding: 2px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        
        .meta-info { margin-bottom: 15px; }
        .signature { margin-top: 50px; width: 100%; }
        .signature td { border: none; text-align: center; width: 33%; }
    </style>
</head>
<body>

    <div class="header">
        <h2>APOTEK LIZ FARMA 02</h2>
        <p>JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005</p>
        <p>Telp: (022) 123456</p>
        <hr>
        <h3>BUKTI RETUR BARANG</h3>
    </div>

    <div class="meta-info">
        <table class="no-border">
            <tr>
                <td width="15%">No. Retur</td>
                <td width="2%">:</td>
                <td width="33%">{{ $retur->no_retur }}</td>
                
                <td width="15%">Tanggal</td>
                <td width="2%">:</td>
                <td>{{ \Carbon\Carbon::parse($retur->tanggal)->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jenis Retur</td>
                <td>:</td>
                <td>
                    {{ $retur->jenis == 'pembelian' ? 'Retur Pembelian (Keluar)' : 'Retur Penjualan (Masuk)' }}
                </td>

                <td>Kepada/Dari</td>
                <td>:</td>
                <td>
                    @if($retur->jenis == 'pembelian')
                        {{ $retur->pembelian->supplier->nama ?? 'Supplier Umum' }}
                    @else
                        {{ $retur->penjualan->pelanggan->nama ?? $retur->penjualan->nama_pelanggan ?? 'Pelanggan Umum' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td colspan="4">{{ $retur->keterangan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Obat</th>
                <th width="10%">Qty</th>
                <th width="20%">Harga Satuan</th>
                <th width="20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retur->details as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detail->obat->nama ?? '-' }} ({{ $detail->obat->kode ?? '' }})</td>
                <td class="text-center">{{ $detail->qty }}</td>
                <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right bold">TOTAL RETUR</td>
                <td class="text-right bold">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature">
        <tr>
            <td>
                Diserahkan Oleh,<br><br><br><br>
                ( ........................... )
            </td>
            <td></td>
            <td>
                Diterima Oleh,<br><br><br><br>
                ( ........................... )
            </td>
        </tr>
    </table>

</body>
</html>