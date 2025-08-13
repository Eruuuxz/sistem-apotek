<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            @page { size: A4; margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-8 bg-gray-100">
    <div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold">FAKTUR PEMBELIAN</h1>
                <p class="text-sm text-gray-600">Nomor: <span class="font-medium">FPB-2025-001</span></p>
                <p class="text-sm text-gray-600">Tanggal: <span class="font-medium">11 Agustus 2025</span></p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold">APOTEK SEHAT SELALU</h2>
                <p class="text-sm text-gray-600">Jl. Kesehatan No. 10, Bandung</p>
                <p class="text-sm text-gray-600">Telp: (022) 123456</p>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="font-bold">Supplier:</h3>
            <p>PT Farmasi Jaya</p>
            <p>Jl. Obat No. 1, Jakarta</p>
            <p>Telp: (021) 987654</p>
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
                <tr>
                    <td class="border border-gray-300 px-3 py-2">1</td>
                    <td class="border border-gray-300 px-3 py-2">Paracetamol 500mg</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">100</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">Rp 2.000</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">Rp 200.000</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-3 py-2">2</td>
                    <td class="border border-gray-300 px-3 py-2">Amoxicillin 500mg</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">50</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">Rp 3.500</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">Rp 175.000</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="border border-gray-300 px-3 py-2 text-right font-bold">Total</td>
                    <td class="border border-gray-300 px-3 py-2 text-right font-bold">Rp 375.000</td>
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
                <p class="font-medium">PT Farmasi Jaya</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Cetak Faktur</button>
    </div>
</body>
</html>
