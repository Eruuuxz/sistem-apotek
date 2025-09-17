@extends('layouts.admin')

@section('title', 'Tambah Biaya Operasional')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Tambah Biaya Operasional</h1>
    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <form action="{{ route('biaya-operasional.store') }}" method="POST">
            @csrf
            @include('biaya-operasional.form', ['biayaOperasional' => null])
            <div class="mt-6">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection