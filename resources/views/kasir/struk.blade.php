<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi A6</title>
    <style>
        @page {
            size: A6 landscape; /* Pastikan A6 horizontal */
            margin: 10px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .kwitansi {
            border: 2px solid #000;
            display: flex;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }
        .left {
            width: 35%;
            border-right: 2px solid #000;
            text-align: center;
            padding: 8px;
        }
        .left img {
            width: 60px;
            margin-bottom: 5px;
        }
        .left h3 {
            margin: 5px 0;
            font-size: 12px;
            text-transform: uppercase;
        }
        .left p {
            font-size: 10px;
            margin: 2px 0;
            line-height: 1.2;
        }
        .right {
            flex: 1;
            padding: 12px;
            position: relative;
        }
        .line {
            margin: 8px 0;
            font-size: 11px;
        }
        .rp-box {
            border: 1px solid #000;
            width: 160px;
            height: 35px;
            position: absolute;
            bottom: 15px;
            left: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="kwitansi">
        <!-- Bagian Kiri -->
        <div class="left">
            {{-- Logo Apotek --}}
            <img src="{{ public_path('logo.png') }}" alt="Logo">
            <h3>APOTEK LIZ FARMA 02</h3>
            <p>Jl. Raya Batujajar No. 321 RT.001 RW.005</p>
            <p>Kel. Batujajar Barat, Kec. Batujajar</p>
        </div>

        <!-- Bagian Kanan -->
        <div class="right">
            <div class="line">No: {{ $penjualan->no_nota }}</div>
            <div class="line">Telah Terima dari: <strong>{{ $penjualan->kasir_nama }}</strong></div>
            <div class="line">Uang Sejumlah: <strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></div>
            <div class="line">Untuk Pembayaran: Pembelian Obat</div>

            <div class="rp-box">
                Rp {{ number_format($penjualan->total, 0, ',', '.') }}
            </div>
        </div>
    </div>
</body>
</html>