<h2 style="text-align:center;">Laporan Data Customer - Periode {{ $periode }}</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background:#f2f2f2;">
            <th>No</th>
            <th>Nama Customer</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Total Transaksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->email }}</td>
            <td>{{ $row->no_hp }}</td>
            <td>{{ $row->penjualans_count ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
