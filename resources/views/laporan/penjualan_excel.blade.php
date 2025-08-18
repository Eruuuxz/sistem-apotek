<table>
    <tr><th colspan="4">Laporan Penjualan</th></tr>
    <tr><th>No</th><th>No Nota</th><th>Tanggal</th><th>Total</th></tr>
    @foreach($rows as $i=>$r)
    <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $r->no_nota }}</td>
        <td>{{ $r->tanggal }}</td>
        <td>{{ $r->total }}</td>
    </tr>
    @endforeach
    <tr><th colspan="3">TOTAL</th><th>{{ $totalAll }}</th></tr>
</table>