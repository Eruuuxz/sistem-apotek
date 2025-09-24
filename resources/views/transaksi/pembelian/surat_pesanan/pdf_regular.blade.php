<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan - {{ $suratPesanan->no_sp }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11pt; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { vertical-align: top; padding: 5px; }
        .clinic-info { text-align: right; }
        .clinic-info h2 { margin: 0; font-size: 14pt; }
        .clinic-info p { margin: 2px 0; font-size: 10pt; }
        .order-meta p { margin: 2px 0; }
        h3 { text-align: center; text-decoration: underline; margin-bottom: 25px; font-size: 14pt; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .content-table th { background-color: #f2f2f2; font-size: 10pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; width: 100%; }
        .signature { float: right; width: 300px; text-align: center; }
        .signature p { margin-bottom: 70px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="order-meta">
                        <p>No. : {{ $suratPesanan->no_sp }}</p>
                        <p>Tanggal : {{ $suratPesanan->tanggal_sp->format('d F Y') }}</p>
                        <br>
                        <p>Kepada Yth.</p>
                        <p><strong>Distributor {{ $suratPesanan->supplier->nama ?? '' }}</strong></p>
                        <p>{{ $suratPesanan->supplier->alamat ?? '' }}</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="clinic-info">
                        <h2>{{ $clinicData['nama'] }}</h2>
                        <p>{{ $clinicData['alamat'] }}</p>
                        <p>Telp: {{ $clinicData['telepon'] }} | Email: {{ $clinicData['email'] }}</p>
                        <p>{{ $clinicData['sio'] }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <h3>SURAT PESANAN</h3>

        <table class="content-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 15%;">Sediaan</th>
                    <th style="width: 15%;">Satuan</th>
                    <th class="text-center" style="width: 15%;">Jumlah Pesanan</th>
                </tr>
            </thead>
            <tbody>
                @php $rowCount = 0; @endphp
                @foreach($suratPesanan->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->obat->nama ?? $detail->nama_manual ?? '' }}</td>
                        <td>{{ $detail->obat->sediaan ?? '' }}</td>
                        <td>{{ $detail->obat->satuan_terkecil ?? '' }}</td>
                        <td class="text-center">{{ $detail->qty_pesan }}</td>
                    </tr>
                    @php $rowCount++; @endphp
                @endforeach

                @for ($i = $rowCount; $i < 10; $i++)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="footer">
            <p><strong>Catatan:</strong> Jika barang yang dipesan kosong, harap menghubungi narahubung kami.</p>
            <div class="signature">
                <p>Pemesan,</p>
                <strong><u>{{ $apotekerData['nama'] }}</u></strong><br>
                <span>{{ $apotekerData['sipa'] }}</span>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>