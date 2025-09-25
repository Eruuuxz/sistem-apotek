<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan - {{ $suratPesanan->no_sp }}</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
        }

        .container {
            width: 100%;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
            padding: 2px;
        }

        .clinic-info {
            text-align: right;
        }

        .clinic-info h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .clinic-info p {
            margin: 1px 0;
            font-size: 10pt;
        }

        .sp-details {
            border: 1px solid #000;
            padding: 5px;
            width: 300px;
        }

        .sp-details td {
            padding: 3px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin: 25px 0;
        }

        /* Tabel Item */
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 7px;
            text-align: left;
        }

        .content-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
        }

        .notes p {
            margin: 0;
        }

        .signature-section {
            margin-top: 30px;
            text-align: right;
        }

        .signature-block {
            display: inline-block;
            text-align: center;
        }

        .signature-block p {
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    <table class="sp-details">
                        <tr>
                            <td>No</td>
                            <td>: {{ $suratPesanan->no_sp }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ $suratPesanan->tanggal_sp->format('d / m / Y') }}</td>
                        </tr>
                        <tr>
                            <td>Distributor</td>
                            <td>: {{ $suratPesanan->supplier->nama ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="clinic-info">
                    <h2>APOTEK LIZ FARMA 02</h2>
                    <p>JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005</p>
                    <p>Telp: (022) 123456</p>
                </td>
            </tr>
        </table>

        <div class="title">SURAT PESANAN</div>

        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 12%;">Sediaan</th>
                    <th style="width: 12%;">Kemasan</th>
                    <th style="width: 12%;">Satuan</th>
                    <th style="width: 15%;">Jumlah Pesanan</th>
                </tr>
            </thead>
            <tbody>
                @php $rowCount = 0; @endphp
                @foreach($suratPesanan->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->obat->nama ?? $detail->nama_manual ?? '' }}</td>
                        <td class="text-center">{{ $detail->obat->sediaan ?? '' }}</td>
                        <td class="text-center">{{ $detail->obat->kemasan ?? '' }}</td>
                        <td class="text-center">{{ $detail->obat->satuan_terkecil ?? '' }}</td>
                        <td class="text-center">{{ $detail->qty_pesan }}</td>
                    </tr>
                    @php $rowCount++; @endphp
                @endforeach

                @for ($i = $rowCount; $i < 15; $i++)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="footer">
            <div class="notes">
                <p><strong>Catatan:</strong></p>
                <p>1. Jika barang yang dipesan kosong, harap menghubungi Narahubung kami.</p>
            </div>
            <div class="signature-section">
                <div class="signature-block">
                    <p>Pemesan,</p>
                    <strong><u>{{ $apotekerData['nama'] }}</u></strong><br>
                    <span>SIPA: {{ $apotekerData['sipa'] }}</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>