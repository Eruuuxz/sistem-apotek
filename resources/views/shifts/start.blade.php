{{-- File: resources/views/shifts/start.blade.php --}}
{{-- Ini adalah file kosong untuk tampilan memulai/mengakhiri shift kasir. --}}
{{-- File: resources/views/shifts/start.blade.php --}}
{{-- Tampilan form untuk memulai shift kasir --}}
{{-- Setelah shift dimulai, kasir akan diarahkan ke halaman POS --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Mulai Shift Kasir</h2>

    @if ($activeShift)
        <div class="alert alert-info">
            Anda sudah memiliki shift aktif: <strong>{{ $activeShift->shift->nama }}</strong> dimulai pada {{ $activeShift->start_time }}.
            <br>
            <a href="{{ route('pos.index') }}" class="btn btn-success mt-2">Lanjut ke POS</a>
        </div>
    @else
        <form method="POST" action="{{ route('shifts.start') }}">
            @csrf

            <div class="mb-3">
                <label for="shift_id" class="form-label">Pilih Shift</label>
                <select name="shift_id" id="shift_id" class="form-select" required>
                    <option value="">-- Pilih Shift --</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->nama }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="initial_cash" class="form-label">Modal Awal Kasir (Rp)</label>
                <input type="number" name="initial_cash" id="initial_cash" class="form-control" required min="0">
            </div>

            <button type="submit" class="btn btn-primary">Mulai Shift & Masuk POS</button>
        </form>
    @endif
</div>
@endsection
