{{-- File: /views/kasir/kwitansi.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 15px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .kwitansi-container {
            border: 2px solid #000;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
        }

        .left-section {
            width: 25%;
            border-right: 2px solid #000;
            text-align: center;
            padding: 10px;
        }

        .left-section img {
            max-width: 90px;
            margin-top: 15px;
        }

        .left-section .apotek {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .right-section {
            width: 75%;
            padding: 10px;
        }

        .field {
            margin: 10px 0;
        }

        .field span {
            display: inline-block;
            min-width: 120px;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 20px;
            width: 200px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="kwitansi-container">
        <!-- Bagian kiri: Logo + info apotek -->
        <div class="left-section">
            <div class="apotek">APOTEK LIZ FARMA 02</div>
            <div>JL. RAYA BATUJAJAR NO. 321<br>RT.001 RW.005<br>
                KEL. BATUJAJAR BARAT, KEC. BATUJAJAR</div>
            <img src="{{ public_path('images/logo-apotek.png') }}" alt="Logo Apotek">
        </div>

        <!-- Bagian kanan: isi kwitansi -->
        <div class="right-section">
            <div class="field"><span>No:</span> <strong>{{ $kwitansi->nomor ?? '-' }}</strong></div>
            <div class="field"><span>Telah terima dari:</span> {{ $kwitansi->nama ?? '...................................' }}</div>
            <div class="field"><span>Uang sejumlah:</span> Rp {{ number_format($kwitansi->jumlah ?? 0, 0, ',', '.') }}</div>
            <div class="field"><span>Untuk pembayaran:</span> {{ $kwitansi->keterangan ?? '...................................' }}</div>

            <div class="amount-box">
                Rp {{ number_format($kwitansi->jumlah ?? 0, 0, ',', '.') }}
            </div>

            <div class="footer">
                Cimareme, {{ \Carbon\Carbon::now()->format('d-m-Y') }} <br><br><br>
                (________________________)
            </div>
        </div>
    </div>
</body>

</html>