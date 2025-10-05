@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- 
        Logika ini akan memeriksa apakah sesi kasir aktif. 
        Jika $activeShift bernilai true, antarmuka POS akan ditampilkan.
        Jika null, form modal awal akan ditampilkan.
    --}}
    @if ($activeShift)
        {{-- Tampilan POS utama jika sesi kasir aktif --}}
        @include('kasir.partials.pos_interface')

        {{-- Sertakan modal yang relevan --}}
        @include('kasir.partials.modal_list_obat')
        @include('kasir.partials.modal_add_pelanggan')
    @else
        {{-- Tampilan form "Input Modal Awal" jika sesi belum dimulai --}}
        @include('kasir.partials.set_initial_cash_form')
    @endif

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
                const ppn = parseFloat($('#total_ppn').val()) || 0;
                const diskonValue = parseFloat($('#diskon_value').val()) || 0;
                const diskonType = $('#diskon_type').val();

                let diskonAmount = 0;
                const totalSebelumDiskon = subtotal + ppn;
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