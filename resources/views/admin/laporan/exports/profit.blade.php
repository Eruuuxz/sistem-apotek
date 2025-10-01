<h2 style="text-align:center;">Laporan Profit - Periode {{ $periode }}</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background:#f2f2f2;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Total Penjualan (Rp)</th>
            <th>Total Modal (Rp)</th>
            <th>Profit (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->tanggal }}</td>
            <td>{{ number_format($row->total_penjualan, 0, ',', '.') }}</td>
            <td>{{ number_format($row->total_modal, 0, ',', '.') }}</td>
            <td>{{ number_format($row->profit, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
