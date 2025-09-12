<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr>
        <th colspan="6" style="text-align:center;">Laporan Penjualan</th>
    </tr>
    <tr>
        <th>No</th>
        <th>No Nota</th>
        <th>Tanggal</th>
        <th>Nama Obat (Qty)</th>
        <th>Total Qty</th>
        <th>Subtotal</th>
    </tr>
    @foreach($rows as $i => $r)
        @php
            $obatList = $r->details
                ->map(fn($d) => ($d->obat->nama ?? '-') . " ({$d->qty})")
                ->join(', ');

            $totalQty = $r->details->sum('qty');
            $subtotal = $r->details->sum('subtotal');
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->no_nota }}</td>
            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y H:i:s') }}</td>
            <td>{{ $obatList }}</td>
            <td style="text-align:center;">{{ $totalQty }}</td>
            <td style="text-align:right;">{{ $subtotal }}</td>
        </tr>
    @endforeach
    <tr>
        <th colspan="4" style="text-align:right;">TOTAL</th>
        <th style="text-align:center;">{{ $rows->sum(fn($r) => $r->details->sum('qty')) }}</th>
        <th style="text-align:right;">{{ $rows->sum(fn($r) => $r->details->sum('subtotal')) }}</th>
    </tr>
</table>
