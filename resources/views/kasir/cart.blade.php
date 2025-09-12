@extends('layouts.kasir')
@section('title', 'POS Kasir - Keranjang')
@section('content')
<div class="container mx-auto py-6">

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Kiri: Daftar Obat / Keranjang -->
        <div class="lg:w-2/3 bg-white shadow-lg rounded-2xl p-4 overflow-x-auto">
            <h2 class="text-lg font-semibold border-b pb-2 mb-4">Keranjang</h2>

            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Obat</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-right">Harga</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-center">Stok</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $index => $item)
                    <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-3 py-2">{{ $item['nama'] }}</td>
                        <td class="px-3 py-2">{{ $item['kategori'] }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($item['harga'],0,',','.') }}</td>
                        <td class="px-3 py-2 text-center flex items-center justify-center gap-1">
                            <button type="button" class="qty-minus px-2 bg-gray-200 rounded" data-index="{{ $index }}">-</button>
                            <input type="number" name="qty[{{ $index }}]" value="{{ $item['qty'] }}" 
                                   class="w-16 border rounded text-center px-1 py-1 qty-input"
                                   data-index="{{ $index }}" min="1" max="{{ $item['stok'] }}">
                            <button type="button" class="qty-plus px-2 bg-gray-200 rounded" data-index="{{ $index }}">+</button>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full {{ $item['stok']==0?'bg-red-200 text-red-800':($item['stok']<10?'bg-yellow-200 text-yellow-800':'bg-green-200 text-green-800') }}">
                                {{ $item['stok']==0?'Habis':($item['stok']<10?'Low':$item['stok']) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right subtotal">Rp {{ number_format($item['qty']*$item['harga'],0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Kanan: Ringkasan Pembayaran -->
        <div class="lg:w-1/3 bg-white shadow-lg rounded-2xl p-6 space-y-4">
            <h2 class="text-lg font-semibold border-b pb-2">Ringkasan Pembayaran</h2>

            <form id="form-checkout" action="{{ route('pos.checkout') }}" method="POST">
                @csrf

                <!-- Nama Kasir -->
                <div class="grid grid-cols-3 gap-2 items-center">
                    <label class="text-sm font-medium text-gray-700">Nama Kasir</label>
                    <input type="text" class="col-span-2 w-full border rounded-lg px-3 py-2 bg-gray-100" value="{{ Auth::user()->name }}" readonly>
                </div>

                <!-- Pilih Member -->
                <div class="grid grid-cols-3 gap-2 items-start mt-2">
                    <label class="text-sm font-medium text-gray-700 mt-2">Member</label>
                    <select id="member" name="member_id" class="col-span-2 w-full border rounded-lg px-3 py-2">
                        <option value="">-- Bukan Member --</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}" 
                                data-nama="{{ $member->nama }}" 
                                data-alamat="{{ $member->alamat }}" 
                                data-telepon="{{ $member->telepon }}">
                            {{ $member->nama }} - {{ $member->telepon }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Data Pelanggan -->
                <div class="grid grid-cols-3 gap-2 items-start mt-2">
                    <label class="text-sm font-medium text-gray-700 mt-2">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="col-span-2 w-full border rounded-lg px-3 py-2" placeholder="Nama Pelanggan" required>
                </div>
                <div class="grid grid-cols-3 gap-2 items-start">
                    <label class="text-sm font-medium text-gray-700 mt-2">Alamat</label>
                    <textarea id="alamat_pelanggan" name="alamat_pelanggan" class="col-span-2 w-full border rounded-lg px-3 py-2" rows="2" placeholder="Alamat Pelanggan"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-2 items-start">
                    <label class="text-sm font-medium text-gray-700 mt-2">Telepon</label>
                    <input type="text" id="telepon_pelanggan" name="telepon_pelanggan" class="col-span-2 w-full border rounded-lg px-3 py-2" placeholder="No. Telepon">
                </div>

                <!-- KTP (psikotropika) -->
                <div class="grid grid-cols-3 gap-2 items-start" id="ktp-wrapper" style="display:none;">
                    <label class="text-sm font-medium text-gray-700 mt-2">KTP</label>
                    <input type="text" id="ktp_pelanggan" name="ktp_pelanggan" class="col-span-2 w-full border rounded-lg px-3 py-2" placeholder="KTP Pelanggan">
                    <p class="col-span-2 text-red-600 text-sm mt-1" id="ktp-error"></p>
                </div>

                <!-- Diskon -->
                <div class="grid grid-cols-3 gap-2 items-center mt-2">
                    <label class="text-sm font-medium text-gray-700">Diskon</label>
                    <div class="col-span-2 flex gap-2">
                        <input type="number" id="diskon" name="diskon" class="w-full border rounded-lg px-3 py-2 text-right" value="0">
                        <select id="tipe_diskon" name="tipe_diskon" class="border rounded-lg px-2 py-2">
                            <option value="nominal">Rp</option>
                            <option value="persen">%</option>
                        </select>
                    </div>
                </div>

                <!-- Total -->
                <div class="grid grid-cols-3 gap-2 items-center mt-2">
                    <label class="text-sm font-medium text-gray-700">Total</label>
                    <input type="text" id="total" name="total_display" class="col-span-2 w-full border rounded-lg px-3 py-2 text-right font-bold bg-gray-100" value="Rp 0" readonly>
                    <input type="hidden" name="total_hidden" value="0">
                </div>

                <!-- Bayar & Kembalian -->
                <div class="grid grid-cols-3 gap-2 items-center mt-2">
                    <label class="text-sm font-medium text-gray-700">Bayar</label>
                    <input type="text" id="bayar_display" class="col-span-2 w-full border rounded-lg px-3 py-2 text-right" placeholder="Rp 0">
                    <input type="hidden" name="bayar" id="bayar">
                </div>
                <div class="grid grid-cols-3 gap-2 items-center mt-2">
                    <label class="text-sm font-medium text-gray-700">Kembalian</label>
                    <input type="text" id="kembalian" class="col-span-2 w-full border rounded-lg px-3 py-2 text-right font-bold bg-green-100 text-green-800" value="Rp 0" readonly>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg mt-3 font-medium">Simpan & Cetak Struk</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    let cart = @json($cart);

    function updateQty(index, qty) {
        qty = parseInt(qty) || 1;
        const max = parseInt(cart[index].stok);
        if(qty < 1) qty = 1;
        if(qty > max) qty = max;
        cart[index].qty = qty;

        $(`.qty-input[data-index="${index}"]`).val(qty);
        const subtotal = qty * parseFloat(cart[index].harga);
        $(`.qty-input[data-index="${index}"]`).closest('tr').find('.subtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));

        hitungTotal();
        checkPsikotropika();
    }

    function hitungTotal() {
        let total = cart.reduce((sum, item) => sum + item.qty * parseFloat(item.harga), 0);

        let diskon = parseFloat($('#diskon').val()) || 0;
        let tipe = $('#tipe_diskon').val();
        if(tipe === 'persen') total -= total * diskon / 100;
        else total -= diskon;
        if(total < 0) total = 0;

        $('#total').val('Rp ' + total.toLocaleString('id-ID'));
        $('input[name="total_hidden"]').val(total);
        hitungKembalian();
    }

    function hitungKembalian() {
        let total = parseFloat($('input[name="total_hidden"]').val()) || 0;
        let bayar = parseFloat($('#bayar').val()) || 0;
        let kembalian = bayar - total;
        $('#kembalian').val('Rp ' + (kembalian>0?kembalian.toLocaleString('id-ID'):"0"));
    }

    function checkPsikotropika() {
        const hasPsiko = cart.some(item => item.kategori === 'psikotropika' && item.qty > 0);
        if(hasPsiko){
            $('#ktp-wrapper').show();
            $('#ktp_pelanggan').prop('disabled', false);
        } else {
            $('#ktp-wrapper').hide();
            $('#ktp_pelanggan').prop('disabled', true).val('');
            $('#ktp-error').text('');
        }
    }

    // Event input qty
    $(document).on('input', '.qty-input', function(){
        const index = $(this).data('index');
        updateQty(index, $(this).val());
    });

    // Tombol + / -
    $(document).on('click', '.qty-plus', function(){
        const index = $(this).data('index');
        updateQty(index, cart[index].qty + 1);
    });
    $(document).on('click', '.qty-minus', function(){
        const index = $(this).data('index');
        updateQty(index, cart[index].qty - 1);
    });

    // Diskon & bayar
    $('#diskon, #tipe_diskon').on('input change', hitungTotal);
    $('#bayar_display').on('input', function(){
        let value = $(this).val().replace(/\D/g,'');
        $('#bayar').val(value ? parseInt(value,10) : 0);
        $(this).val(value ? 'Rp ' + parseInt(value,10).toLocaleString('id-ID') : '');
        hitungKembalian();
    });

    // Validasi KTP
    $('#form-checkout').on('submit', function(e){
        const hasPsiko = cart.some(item => item.kategori === 'psikotropika' && item.qty > 0);
        if(hasPsiko && !$('#ktp_pelanggan').val().trim()){
            e.preventDefault();
            $('#ktp-error').text('KTP wajib untuk pembelian psikotropika!');
            $('#ktp_pelanggan').focus();
        }
    });

    // Select2 Member
    $('#member').select2({placeholder:"Cari Member...",allowClear:true,width:'100%'});
    $('#member').on('change', function(){
        let option=$(this).find(':selected');
        $('#nama_pelanggan').val(option.data('nama')||"");
        $('#alamat_pelanggan').val(option.data('alamat')||"");
        $('#telepon_pelanggan').val(option.data('telepon')||"");
    });

    // Initialize subtotal & total
    cart.forEach((item,index)=>updateQty(index,item.qty));
});
</script>
@endpush
