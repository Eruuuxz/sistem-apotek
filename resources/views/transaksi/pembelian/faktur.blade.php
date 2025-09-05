<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian - {{ $p->no_faktur }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="p-8 bg-gray-100">
    <div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold">FAKTUR PEMBELIAN</h1>
                <p class="text-sm text-gray-600">Nomor: <span class="font-medium">{{ $p->no_faktur }}</span></p>
                <p class="text-sm text-gray-600">Tanggal: <span
                        class="font-medium">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y H:i:s') }}</span> {{-- Tambahkan format jam --}}
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold">APOTEK SEHAT SELALU</h2>
                <p class="text-sm text-gray-600">Jl. Kesehatan No. 10, Bandung</p>
                <p class="text-sm text-gray-600">Telp: (022) 123456</p>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="font-bold">Supplier:</h3>
            <p>{{ $p->supplier->nama ?? '-' }}</p>
            <p>{{ $p->supplier->alamat ?? '-' }}</p>
            <p>Telp: {{ $p->supplier->telepon ?? '-' }}</p>
        </div>

        <table class="w-full text-sm border-collapse border border-gray-300 mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-3 py-2 text-left">No</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Nama Obat</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Jumlah</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Harga Satuan</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($p->detail as $i => $d)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $i + 1 }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $d->obat->nama ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ $d->jumlah }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp
                            {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp
                            {{ number_format($d->jumlah * $d->harga_beli, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="border border-gray-300 px-3 py-2 text-right font-bold">Total</td>
                    <td class="border border-gray-300 px-3 py-2 text-right font-bold">Rp
                        {{ number_format($p->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="flex justify-between mt-8">
            <div class="text-center">
                <p class="mb-16">Diterima Oleh,</p>
                <p class="font-medium">Admin Apotek</p>
            </div>
            <div class="text-center">
                <p class="mb-16">Hormat Kami,</p>
                <p class="font-medium">{{ $p->supplier->nama ?? 'Supplier' }}</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 no-print flex justify-center gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Cetak Faktur</button>
        <a href="{{ route('pembelian.pdf', $p->id) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow">Unduh
            PDF</a>
    </div>
</body>

</html>