<h2 style="text-align:center;">Laporan Stok Obat - Periode {{ $periode }}</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background:#f2f2f2;">
            <th>No</th>
            <th>Nama Obat</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Harga Jual (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->kategori->nama ?? '-' }}</td>
            <td>{{ $row->stok }}</td>
            <td>{{ number_format($row->harga_jual, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
