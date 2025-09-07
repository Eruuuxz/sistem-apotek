{{-- File: /views/kasir/kwitansi.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .kwitansi-container {
            border: 2px solid #000;
            display: flex;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }

       .left-section {
    width: 20%;
    border-right: 2px solid #000;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    box-sizing: border-box;
}

.left-box {
    border: 2px solid #000;
    border-radius: 15px;
    padding: 5px;
    flex: 1;                /* biar panjang ke atas-bawah */
    display: flex;
    align-items: center;
    justify-content: center;
}

.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    line-height: 1.5;
}

.logo img {
    max-width: 70px;
    margin-top: 10px;
}


        /* Bagian kanan */
        .right-section {
            width: 80%;
            padding: 15px 20px;
            box-sizing: border-box;
            border: 2px solid #000;
            border-radius: 20px;
            margin: 10px;
        }

        .field {
            margin: 8px 0;
            font-size: 13px;
        }

        .field span {
            display: inline-block;
            min-width: 150px;
            font-weight: bold;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 12px 20px;
            display: inline-block;
            min-width: 200px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-top: 25px;
            border-radius: 12px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="kwitansi-container">
        <!-- Bagian kiri -->
<!-- Bagian kiri -->
<div class="left-section">
    <div class="left-box">
        <div class="vertical-text">
            APOTEK LIZ FARMA 02<br>
            JL. RAYA BATUJAJAR NO. 321<br>
            RT.001 RW.005<br>
            KEL. BATUJAJAR BARAT, KEC. BATUJAJAR
        </div>
    </div>
    <div class="logo">
        <img src="{{ asset('images/ilus.jpg') }}" alt="Logo Apotek">
    </div>
</div>


        <!-- Bagian kanan -->
        <div class="right-section">
            <div class="field">
                <span>No:</span> {{ $penjualan->no_nota }}
            </div>
            <div class="field">
                <span>Telah terima dari:</span> ....................................
            </div>
            <div class="field">
                <span>Uang sejumlah:</span> Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}
            </div>
            <div class="field">
                <span>Untuk pembayaran:</span> ....................................
            </div>

            <div class="amount-box">
                Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}
            </div>
        </div>
    </div>
</body>

</html>
