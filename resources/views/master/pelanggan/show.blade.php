@extends(in_array(auth()->user()->role, ['admin', 'kasir']) ? 'layouts.admin' : 'layouts.kasir')

@section('content')
<div class="container">
    <h1 class="mb-4">Detail Pelanggan</h1>

    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <td>{{ $pelanggan->nama }}</td>
        </tr>
        <tr>
            <th>Telepon</th>
            <td>{{ $pelanggan->telepon }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $pelanggan->alamat }}</td>
        </tr>
        <tr>
            <th>No KTP</th>
            <td>{{ $pelanggan->no_ktp }}</td>
        </tr>
        <tr>
            <th>Status Member</th>
            <td>{{ ucfirst($pelanggan->status_member) }}</td>
        </tr>
        <tr>
            <th>Point</th>
            <td>{{ $pelanggan->point }}</td>
        </tr>
        <tr>
            <th>File KTP</th>
            <td>
                @if($pelanggan->file_ktp)
                    <a href="{{ asset('storage/'.$pelanggan->file_ktp) }}" target="_blank">Lihat</a>
                @else
                    Tidak ada
                @endif
            </td>
        </tr>
    </table>

    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection