<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Pesanan - {{ $suratPesanan->no_sp }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background-color: #f0f0f0; }
        .no-border td { border: none; padding: 4px; }
        .text-right { text-align: right; }
        .signature { margin-top: 50px; }
        th.no-wrap, td.no-wrap { white-space: nowrap; }
        /* Lebar kolom */
        th:nth-child(1), td:nth-child(1) { width: 5%; }   /* No */
        th:nth-child(2), td:nth-child(2) { width: 35%; }  /* Nama Obat */
        th:nth-child(3), td:nth-child(3) { width: 10%; }  /* Qty */
        th:nth-child(4), td:nth-child(4) { width: 15%; }  /* Batch */
        th:nth-child(5), td:nth-child(5) { width: 15%; }  /* Rekursor */
        th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Harga Satuan */
        th:nth-child(7), td:nth-child(7) { width: 10%; }  /* Total */
    </style>
</head>
<body>
    <h2>Surat Pesanan (SP) - {{ $suratPesanan->no_sp }}</h2>

    <table class="no-border">
        <tr>
            <td><strong>Tanggal:</strong> {{ $suratPesanan->tanggal_sp->format('d/m/Y H:i') }}</td>
            <td><strong>Supplier:</strong> {{ $suratPesanan->supplier->nama ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Keterangan:</strong> {{ $suratPesanan->keterangan ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Mode SP:</strong> {{ ucfirst($suratPesanan->sp_mode) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="no-wrap">No</th>
                <th class="no-wrap">Nama Obat</th>
                <th class="text-right no-wrap">Qty</th>
                <th class="no-wrap">Batch</th>
                <th class="no-wrap">Rekursor</th>
                <th class="text-right no-wrap">Harga Satuan</th>
                <th class="text-right no-wrap">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalHarga = 0;
            @endphp

            @if($suratPesanan->sp_mode === 'blank' || $suratPesanan->details->isEmpty())
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @else
                @foreach($suratPesanan->details as $index => $detail)
                    @php
                        $qty = $detail->qty_pesan ?? 0;
                        $harga = $detail->harga_satuan ?? 0;
                        $subtotal = $qty * $harga;
                        $totalQty += $qty;
                        $totalHarga += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $suratPesanan->sp_mode === 'dropdown' ? ($detail->obat->nama ?? '') : ($detail->nama_manual ?? '') }}</td>
                        <td class="text-right">{{ $qty }}</td>
                        <td>{{ $detail->batch ?? '' }}</td>
                        <td>{{ $detail->rekursor ?? '' }}</td>
                        <td class="text-right">{{ number_format($harga, 2) }}</td>
                        <td class="text-right">{{ number_format($subtotal, 2) }}</td>
                    </tr>
                @endforeach

                {{-- Total --}}
                <tr>
                    <th colspan="2" class="text-right">Total</th>
                    <th class="text-right">{{ $totalQty }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right">{{ number_format($totalHarga, 2) }}</th>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature">
        <p>Tanda tangan:</p>
        <p>__________________________</p>
    </div>
</body>
</html>
