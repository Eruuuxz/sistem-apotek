<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan Prekursor - {{ $suratPesanan->no_sp }}</title>
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

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin: 25px 0;
        }

        .info-section p {
            margin: 5px 0;
        }

        .info-table {
            margin-left: 20px;
        }

        .info-table td {
            padding: 1px 5px;
        }

        /* Tabel Item */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        <div class="header-table">
            <div class="clinic-info" style="float: right;">
                <h2>APOTEK LIZ FARMA 02</h2>
                <p>JL. RAYA BATUJAJAR NO. 321 RT.001 RW.005</p>
                <p>Telp: (022) 123456</p>
            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="title">SURAT PESANAN OBAT MENGANDUNG PREKURSOR</div>

        <div class="info-section">
            <p>Yang bertandatangan dibawah ini:</p>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $apotekerData['nama'] }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Apoteker Penanggung Jawab</td>
                </tr>
                <tr>
                    <td>Nomor SIPA</td>
                    <td>:</td>
                    <td>{{ $apotekerData['sipa'] }}</td>
                </tr>
            </table>

            <p>Mengajukan pesanan obat yang mengandung prekursor farmasi kepada:</p>
            <table class="info-table">
                <tr>
                    <td>Nama PBF</td>
                    <td>:</td>
                    <td>{{ $suratPesanan->supplier->nama ?? '' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td style="vertical-align: top;">:</td>
                    <td>{{ $suratPesanan->supplier->alamat ?? '' }}</td>
                </tr>
            </table>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 20%;">Zat Aktif Prekursor</th>
                    <th style="width: 12%;">Sediaan</th>
                    <th style="width: 12%;">Satuan</th>
                    <th style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php $rowCount = 0; @endphp
                @foreach($suratPesanan->details as $index => $detail)
                    @if($detail->obat && $detail->obat->is_prekursor)
                        <tr>
                            <td class="text-center">{{ $rowCount + 1 }}</td>
                            <td>{{ $detail->obat->nama ?? '' }}</td>
                            <td>{{ $detail->obat->zat_aktif_prekursor ?? '' }}</td>
                            <td class="text-center">{{ $detail->obat->sediaan ?? '' }}</td>
                            <td class="text-center">{{ $detail->obat->satuan_terkecil ?? '' }}</td>
                            <td class="text-center">{{ $detail->qty_pesan }}</td>
                        </tr>
                        @php $rowCount++; @endphp
                    @endif
                @endforeach

                @for ($i = $rowCount; $i < 10; $i++)
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

        <div class="signature-section">
            <div class="signature-block">
                <p>Cimahi, {{ $suratPesanan->tanggal_sp->format('d F Y') }}</p>
                <strong><u>{{ $apotekerData['nama'] }}</u></strong><br>
                <span>SIPA: {{ $apotekerData['sipa'] }}</span>
            </div>
        </div>
    </div>
</body>

</html>