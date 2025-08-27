{{-- File: /kasir/struk.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan - {{ $p->no_nota }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Ukuran kertas kecil, sesuaikan jika perlu */
        body {
            width: 58mm; /* Lebar standar struk kasir */
            margin: 0 auto;
            padding: 5mm;
            font-family: 'Consolas', 'Courier New', monospace; /* Font monospace untuk tampilan struk */
            font-size: 10px;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 2px; margin-bottom: 2px; }
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }
        .font-bold { font-weight: bold; }

        @media print {
            @page {
                size: 58mm auto; /* Lebar 58mm, tinggi otomatis */
                margin: 0;
            }
            body {
                margin: 0;
                padding: 5mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h1 class="font-bold text-sm">APOTEK SEHAT SELALU</h1>
        <p>Jl. Kesehatan No. 10, Bandung</p>
        <p>Telp: (022) 123456</p>
        <div class="border-b mt-2"></div>
    </div>

    <div class="mt-2">
        <p>Nota: {{ $p->no_nota }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d H:i') }}</p>
        <p>Kasir: {{ $p->kasir_nama }}</p>
        <div class="border-b mt-2"></div>
    </div>

    <div class="mt-2">
        @foreach($p->detail as $d)
            <p>{{ $d->qty }}x {{ $d->obat->nama }}</p> 
            <p class="text-right">Rp {{ number_format($d->harga, 0, ',', '.') }} <span style="margin-left: 10px;">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span></p>
        @endforeach
        <div class="border-b mt-2"></div>
    </div>

    <div class="mt-2 text-right">
        <p>Total: <span class="font-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</span></p>
        <p>Bayar: <span class="font-bold">Rp {{ number_format($p->bayar, 0, ',', '.') }}</span></p>
        <p>Kembalian: <span class="font-bold">Rp {{ number_format($p->bayar - $p->total, 0, ',', '.') }}</span></p>
        <div class="border-b mt-2"></div>
    </div>

    <div class="text-center mt-2">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Cetak Struk</button>
        <a href="{{ route('penjualan.show', $p->id) }}" class="bg-gray-400 text-white px-4 py-2 rounded shadow">Kembali</a>
    </div>
</body>
</html>
