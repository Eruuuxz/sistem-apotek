<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan Prekursor - {{ $suratPesanan->no_sp }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11pt; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 14pt; text-decoration: underline; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { vertical-align: top; padding: 2px 0; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .content-table th { background-color: #f2f2f2; font-size: 10pt; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; width: 100%; }
        .signature { float: right; width: 300px; text-align: center; }
        .signature p { margin-bottom: 70px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SURAT PESANAN OBAT MENGANDUNG PREKURSOR</h2>
        </div>

        <table class="info-table">
            <tr>
                <td colspan="3">Yang bertandatangan dibawah ini:</td>
            </tr>
            <tr>
                <td style="width: 150px;">Nama</td>
                <td style="width: 10px;">:</td>
                <td>{{ $apotekerData['nama'] }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $apotekerData['jabatan'] }}</td>
            </tr>
            <tr>
                <td>Nomor SIPA</td>
                <td>:</td>
                <td>{{ $apotekerData['sipa'] }}</td>
            </tr>
            <tr><td colspan="3">&nbsp;</td></tr>
             <tr>
                <td colspan="3">Mengajukan pesanan obat yang mengandung prekursor farmasi kepada:</td>
            </tr>
            <tr>
                <td>Nama PBF</td>
                <td>:</td>
                <td><strong>{{ $suratPesanan->supplier->nama ?? '' }}</strong></td>
            </tr>
             <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $suratPesanan->supplier->alamat ?? '' }}</td>
            </tr>
        </table>
        
        <p>Jenis obat yang mengandung prekursor farmasi yang dipesan adalah:</p>

        <table class="content-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 25%;">Zat Aktif Prekursor</th>
                    <th style="width: 15%;">Sediaan</th>
                    <th class="text-center" style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php $rowCount = 0; @endphp
                @foreach($suratPesanan->details as $index => $detail)
                    @if($detail->obat && ($detail->obat->is_prekursor || $suratPesanan->details->contains(fn($d) => $d->obat->is_prekursor)))
                    <tr>
                        <td class="text-center">{{ $rowCount + 1 }}</td>
                        <td>{{ $detail->obat->nama ?? '' }}</td>
                        <td>{{ $detail->obat->zat_aktif_prekursor ?? '' }}</td>
                        <td>{{ $detail->obat->sediaan ?? '' }}</td>
                        <td class="text-center">{{ $detail->qty_pesan }} {{ $detail->obat->satuan_terkecil ?? '' }}</td>
                    </tr>
                    @php $rowCount++; @endphp
                    @endif
                @endforeach
                
                @for ($i = $rowCount; $i < 5; $i++)
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
            <div class="signature">
                <p>Cimahi, {{ $suratPesanan->tanggal_sp->format('d F Y') }}</p>
                <strong><u>{{ $apotekerData['nama'] }}</u></strong><br>
                <span>{{ $apotekerData['sipa'] }}</span>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>