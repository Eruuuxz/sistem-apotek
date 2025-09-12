@extends(in_array(auth()->user()->role, ['admin', 'kasir']) ? 'layouts.admin' : 'layouts.kasir')

@section('title', 'Tambah Pelanggan Baru')

@section('content')
<div class="bg-white p-8 shadow-xl rounded-xl max-w-2xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6">Tambah Pelanggan Baru</h2>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pelanggan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @php
            $fields = [
                ['label' => 'Nama Pelanggan', 'name' => 'nama', 'type' => 'text', 'required' => true],
                ['label' => 'Telepon', 'name' => 'telepon', 'type' => 'text'],
                ['label' => 'Alamat', 'name' => 'alamat', 'type' => 'textarea'],
                ['label' => 'Nomor KTP', 'name' => 'no_ktp', 'type' => 'text'],
                ['label' => 'Point', 'name' => 'point', 'type' => 'number', 'min' => 0, 'default' => 0],
            ];

            $selects = [
                'status_member' => [
                    'label' => 'Status Member',
                    'required' => true,
                    'options' => ['non_member' => 'Non-Member', 'member' => 'Member']
                ]
            ];
        @endphp

        {{-- Loop input / textarea --}}
        @foreach($fields as $field)
            <div class="mb-4">
                <label for="{{ $field['name'] }}" class="block font-semibold mb-1">
                    {{ $field['label'] }}
                    @if(!empty($field['required']))
                        <span class="text-red-600">*</span>
                    @endif
                </label>

                @if($field['type'] === 'textarea')
                    <textarea name="{{ $field['name'] }}" id="{{ $field['name'] }}" rows="3"
                        class="w-full border rounded px-3 py-2 @error($field['name']) border-red-500 @enderror">{{ old($field['name']) }}</textarea>
                @else
                    <input type="{{ $field['type'] }}"
                        name="{{ $field['name'] }}"
                        id="{{ $field['name'] }}"
                        value="{{ old($field['name'], $field['default'] ?? '') }}"
                        @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                        class="w-full border rounded px-3 py-2 @error($field['name']) border-red-500 @enderror">
                @endif

                @error($field['name'])
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        {{-- File upload --}}
        <div class="mb-4">
            <label for="file_ktp" class="block font-semibold mb-1">Upload File KTP (JPG, PNG, GIF, max 2MB)</label>
            <input type="file" name="file_ktp" id="file_ktp" accept="image/*"
                class="w-full border rounded px-3 py-2 @error('file_ktp') border-red-500 @enderror">
            @error('file_ktp')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Loop select --}}
        @foreach($selects as $name => $select)
            <div class="mb-4">
                <label for="{{ $name }}" class="block font-semibold mb-1">
                    {{ $select['label'] }}
                    @if(!empty($select['required']))
                        <span class="text-red-600">*</span>
                    @endif
                </label>
                <select name="{{ $name }}" id="{{ $name }}"
                    class="w-full border rounded px-3 py-2 @error($name) border-red-500 @enderror">
                    <option value="">Pilih {{ $select['label'] }}</option>
                    @foreach($select['options'] as $value => $label)
                        <option value="{{ $value }}" {{ old($name) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error($name)
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Simpan Pelanggan
            </button>
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
