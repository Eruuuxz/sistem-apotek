{{-- Active shift banner --}}
<div class="alert bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 flex justify-between items-center">
    <div>
        Anda sudah memiliki shift aktif: <strong>{{ $activeShift->shift->name }}</strong> dimulai pada
        {{ $activeShift->start_time }}.
    </div>
    <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap" onclick="openEndShiftModal()">
        Akhiri Shift
    </button>
</div>

{{-- Main POS grid --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {{-- Left side: Search and Cart Table --}}
    @include('kasir.partials.cart_table')

    {{-- Right side: Payment Summary --}}
    @include('kasir.partials.payment_summary')
</div>