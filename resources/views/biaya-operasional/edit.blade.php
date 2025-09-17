@extends('layouts.admin')

@section('title', 'Edit Biaya Operasional')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Edit Biaya Operasional</h1>
    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <form action="{{ route('biaya-operasional.update', $biayaOperasional->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('biaya-operasional.form', ['biayaOperasional' => $biayaOperasional])
            <div class="mt-6">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection