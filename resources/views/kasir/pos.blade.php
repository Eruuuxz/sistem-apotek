@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')
<div class="h-[calc(100vh-100px)] flex flex-col md:flex-row gap-4 overflow-hidden">
    
    <div class="md:w-8/12 flex flex-col gap-4 h-full">
        <div class="bg-white p-4 rounded-xl shadow-sm shrink-0">
            <form action="{{ route('pos.add') }}" method="POST" class="relative">
                @csrf
                <i data-feather="search" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                <input type="text" id="search" name="kode" 
                    placeholder="Scan Barcode atau Cari Nama Obat..." 
                    class="w-full pl-12 pr-4 py-3 rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-lg"
                    autofocus autocomplete="off">
                <ul id="suggestions" class="absolute z-50 bg-white border border-gray-100 w-full mt-2 rounded-xl shadow-xl hidden max-h-60 overflow-y-auto"></ul>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm flex-1 overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-700">Daftar Obat Tersedia</h3>
                <button onclick="openObatModal()" class="text-sm text-green-600 font-semibold hover:underline flex items-center gap-1">
                    <i data-feather="grid" class="w-4 h-4"></i> Lihat Katalog (F2)
                </button>
            </div>
            
            <div class="overflow-y-auto p-0 flex-1">
                @include('kasir.partials.cart_table') 
            </div>
        </div>
    </div>

    <div class="md:w-4/12 flex flex-col h-full bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        {{-- Header Total (HIJAU) --}}
        <div class="p-4 bg-green-600 text-white shadow-md z-10">
            <div class="flex justify-between items-center">
                <span class="text-green-100 text-sm">Total Tagihan</span>
                <span class="text-xs bg-green-500 px-2 py-1 rounded text-white font-semibold">{{ count($cart) }} Item</span>
            </div>
            <div class="text-4xl font-bold mt-1 text-right tracking-tight">
                <span id="total_display_big">Rp {{ number_format($totalAkhir ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
            @include('kasir.partials.payment_summary')
        </div>
    </div>
</div>

@include('kasir.partials.modal_list_obat')
@include('kasir.partials.modal_add_pelanggan')

@endsection

@push('scripts')
    {{-- Dependensi Eksternal untuk Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Jalankan script HANYA jika antarmuka POS aktif
        @if ($activeShift)
            $(document).ready(function () {
                // Inisialisasi Select2 untuk pencarian member
                $('#member_search_select2').select2({
                    placeholder: "Cari Pelanggan Terdaftar...",
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('pos.searchPelanggan') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.nama + (item.telepon ? ' (' + item.telepon + ')' : ''),
                                        id: item.id,
                                        data: item // Menyimpan seluruh objek data
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });

                // Event handler saat memilih pelanggan dari Select2
                $('#member_search_select2').on('select2:select', function (e) {
                    const data = e.params.data.data;
                    $('#nama_pelanggan').val(data.nama || "");
                    $('#alamat_pelanggan').val(data.alamat || "");
                    $('#telepon_pelanggan').val(data.telepon || "");
                });

                // Event handler saat membersihkan pilihan pelanggan
                $('#member_search_select2').on('select2:clear', function (e) {
                    $('#nama_pelanggan').val("");
                    $('#alamat_pelanggan').val("");
                    $('#telepon_pelanggan').val("");
                });

                // Panggil fungsi-fungsi inisialisasi
                checkPsikotropikaInCart();
                hitungTotal();

                // Event listener untuk input diskon dan bayar
                $('#diskon_value, #diskon_type').on('input change', hitungTotal);
                $('#bayar_display').on('input', formatBayar);
            });

            // --- FUNGSI KALKULASI & FORMAT ---
            function hitungTotal() {
                const subtotal = parseFloat($('#total_subtotal').val()) || 0;
                // const ppn = parseFloat($('#total_ppn').val()) || 0; // PPN Dihapus
                const diskonValue = parseFloat($('#diskon_value').val()) || 0;
                const diskonType = $('#diskon_type').val();

                let diskonAmount = 0;
                const totalSebelumDiskon = subtotal; // PPN Dihapus (sebelumnya: subtotal + ppn)
                if (diskonType === 'persen') {
                    diskonAmount = totalSebelumDiskon * (diskonValue / 100);
                } else {
                    diskonAmount = diskonValue;
                }

                let finalTotal = Math.max(totalSebelumDiskon - diskonAmount, 0);
                $('#total_display').val("Rp " + finalTotal.toLocaleString('id-ID'));
                $('#total_hidden').val(finalTotal);
                hitungKembalian();
            }

            function formatBayar() {
                let input = $('#bayar_display');
                let hidden = $('#bayar');
                let value = input.val().replace(/\D/g, '');

                if (!value) {
                    hidden.val(0);
                    input.val("");
                } else {
                    value = parseInt(value, 10);
                    hidden.val(value);
                    input.val('Rp ' + value.toLocaleString('id-ID'));
                }
                hitungKembalian();
            }

            function hitungKembalian() {
                let total = parseFloat($('#total_hidden').val()) || 0;
                let bayar = parseFloat($('#bayar').val()) || 0;
                let kembalian = bayar - total;
                $('#kembalian').val('Rp ' + (kembalian > 0 ? kembalian.toLocaleString('id-ID') : "0"));
            }
            
            function checkPsikotropikaInCart() {
                const ktpInputGroup = $('#ktp-input-group');
                const cartRows = $('#cart-table tbody tr[data-is-psikotropika="true"]');

                if (cartRows.length > 0) {
                    ktpInputGroup.slideDown();
                    $('#no_ktp').prop('required', true);
                } else {
                    ktpInputGroup.slideUp();
                    $('#no_ktp').prop('required', false);
                }
            }
            
            // --- AUTOCOMPLETE SEARCH OBAT ---
            (function setupAutocomplete() {
                const searchBox = document.getElementById('search');
                const suggestionBox = document.getElementById('suggestions');
                if (!searchBox) return;
                let timer;

                function fetchSuggestions(q) {
                    fetch(`/pos/search?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            suggestionBox.innerHTML = "";
                            if (data.length === 0) {
                                suggestionBox.classList.add('hidden');
                                return;
                            }
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm";
                                li.innerHTML = `${item.nama} <span class="text-xs text-gray-500">(${item.kode})</span>`;
                                li.onclick = () => {
                                    searchBox.value = item.kode;
                                    suggestionBox.classList.add('hidden');
                                    searchBox.form.submit();
                                };
                                suggestionBox.appendChild(li);
                            });
                            suggestionBox.classList.remove('hidden');
                        });
                }

                searchBox.addEventListener('keyup', function (e) {
                    clearTimeout(timer);
                    const q = this.value.trim();
                    if (q.length < 2) {
                        suggestionBox.classList.add('hidden');
                        return;
                    }
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchBox.form.submit();
                        return;
                    }
                    timer = setTimeout(() => fetchSuggestions(q), 300);
                });

                document.addEventListener('click', (e) => {
                    if (!searchBox.contains(e.target)) suggestionBox.classList.add('hidden');
                });
            })();

            // --- FUNGSI MODAL ---
            function openObatModal() { $('#obatModal').removeClass('hidden').addClass('flex'); }
            function closeObatModal() { $('#obatModal').removeClass('flex').addClass('hidden'); }
            function openAddPelangganModal() { $('#addPelangganModal').removeClass('hidden').addClass('flex'); }
            function closeAddPelangganModal() {
                $('#addPelangganModal').removeClass('flex').addClass('hidden');
                $('#add-pelanggan-form')[0].reset();
            }

            // --- AJAX & EVENT LISTENER MODAL ---
            $(document).on('click', '.add-to-cart-btn', function() {
                const button = $(this);
                const kodeObat = button.data('kode');
                
                button.prop('disabled', true).html('...');

                $('<form action="{{ route('pos.add') }}" method="POST">' +
                  '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                  '<input type="hidden" name="kode" value="' + kodeObat + '">' +
                  '</form>').appendTo('body').submit();
            });

            $('#add-pelanggan-form').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                button.prop('disabled', true).text('Menyimpan...');
                
                $.ajax({
                    url: "{{ route('pos.addPelangganCepat') }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function(data) {
                        if (data.id) {
                            const newOption = new Option(data.nama + (data.telepon ? ` (${data.telepon})` : ''), data.id, true, true);
                            $('#member_search_select2').append(newOption).trigger('change');
                            $('#nama_pelanggan').val(data.nama || "");
                            $('#alamat_pelanggan').val(data.alamat || "");
                            $('#telepon_pelanggan').val(data.telepon || "");
                            closeAddPelangganModal();
                        }
                    },
                    error: function(xhr) { 
                        alert('Gagal menambahkan pelanggan. ' + (xhr.responseJSON.message || ''));
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Simpan Pelanggan');
                    }
                });
            });

            $('#modal-search').on('keyup', function () {
                const query = this.value.toLowerCase();
                $('#obat-table tbody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(query));
                });
            });
        @endif
    </script>
@endpush