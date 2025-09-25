@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm-inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Cek apakah ada shift yang aktif --}}
    @if ($activeShift)
        {{-- Tampilan POS utama jika shift aktif --}}
        @include('kasir.partials.pos_interface')

        {{-- Sertakan semua modal --}}
        @include('kasir.partials.modal_end_shift')
        @include('kasir.partials.modal_list_obat')
        @include('kasir.partials.modal_add_pelanggan')

    @else
        {{-- Tampilan form "Mulai Shift" jika belum ada shift aktif --}}
        @include('kasir.partials.start_shift_form')
    @endif

@endsection

@push('scripts')
    {{-- All JavaScript remains here as it controls elements across all partials --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('#member_search_select2').select2({
                placeholder: "Cari Member...",
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
                                    data: item
                                }
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            $('#member_search_select2').on('select2:select', function (e) {
                const data = e.params.data.data;
                $('#nama_pelanggan').val(data.nama || "");
                $('#alamat_pelanggan').val(data.alamat || "");
                $('#telepon_pelanggan').val(data.telepon || "");
            });

            $('#member_search_select2').on('select2:clear', function (e) {
                $('#nama_pelanggan').val("");
                $('#alamat_pelanggan').val("");
                $('#telepon_pelanggan').val("");
            });

            // Panggil fungsi checkPsikotropikaInCart saat halaman dimuat
            checkPsikotropikaInCart();
            hitungTotal(); // Panggil pertama kali untuk menghitung total berdasarkan nilai yang ada

            // Perbarui total setiap kali diskon berubah
            $('#diskon_value, #diskon_type').on('input change', function () {
                hitungTotal();
            });

            // Perbarui kembalian setiap kali bayar berubah
            $('#bayar_display').on('input', function () {
                formatBayar();
            });
        });

        // --- FUNGSI KALKULASI & FORMAT ---
        function hitungTotal() {
            const subtotal = parseFloat(document.getElementById('total_subtotal').value) || 0;
            const ppn = parseFloat(document.getElementById('total_ppn').value) || 0;
            const diskonValue = parseFloat(document.getElementById('diskon_value').value) || 0;
            const diskonType = document.getElementById('diskon_type').value;

            let diskonAmount = 0;
            if (diskonType === 'persen') {
                diskonAmount = subtotal * (diskonValue / 100);
            } else {
                diskonAmount = diskonValue;
            }

            let finalTotal = Math.max(subtotal + ppn - diskonAmount, 0);
            document.getElementById('total_display').value = "Rp " + finalTotal.toLocaleString('id-ID');
            document.getElementById('total_hidden').value = finalTotal;
            hitungKembalian();
        }

        function formatBayar() {
            let input = document.getElementById('bayar_display');
            let hidden = document.getElementById('bayar');
            let value = input.value.replace(/\D/g, '');

            if (!value) {
                hidden.value = 0;
                input.value = "";
                hitungKembalian();
                return;
            }

            value = parseInt(value, 10);
            hidden.value = value;
            input.value = 'Rp ' + value.toLocaleString('id-ID');
            hitungKembalian();
        }

        function hitungKembalian() {
            let total = parseFloat(document.getElementById('total_hidden').value) || 0;
            let bayar = parseFloat(document.getElementById('bayar').value) || 0;
            let kembalian = bayar - total;
            document.getElementById('kembalian').value = 'Rp ' + (kembalian > 0 ? kembalian.toLocaleString('id-ID') : "0");
        }
        
        function checkPsikotropikaInCart() {
            const ktpInputGroup = document.getElementById('ktp-input-group');
            const cartRows = $('#cart-table tbody tr').filter(function () {
                return $(this).find('td').length > 1;
            });

            let hasPsikotropika = false;
            cartRows.each(function (index, row) {
                if ($(row).attr('data-is-psikotropika') === 'true') {
                    hasPsikotropika = true;
                }
            });

            if (hasPsikotropika) {
                ktpInputGroup.style.display = 'grid';
                document.getElementById('no_ktp').setAttribute('required', 'required');
            } else {
                ktpInputGroup.style.display = 'none';
                document.getElementById('no_ktp').removeAttribute('required');
            }
        }
        
        // --- AUTOCOMPLETE SEARCH OBAT ---
        document.addEventListener('DOMContentLoaded', function () {
            const searchBox = document.getElementById('search');
            const suggestionBox = document.getElementById('suggestions');
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
                            li.className = "px-3 py-2 hover:bg-gray-100 cursor-pointer";
                            li.innerHTML = `${item.nama} <span class="text-sm text-gray-500">(${item.kode})</span>`;
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
                if (q.length < 1) {
                    suggestionBox.classList.add('hidden');
                    return;
                }
                if (e.key === 'Enter') {
                    searchBox.form.submit();
                    return;
                }
                timer = setTimeout(() => fetchSuggestions(q), 300);
            });

            document.addEventListener('click', function (e) {
                if (!searchBox.contains(e.target)) {
                    suggestionBox.classList.add('hidden');
                }
            });
        });

        // --- FUNGSI MODAL ---
        function openObatModal() {
            $('#obatModal').removeClass('hidden').addClass('flex');
        }

        function closeObatModal() {
            $('#obatModal').removeClass('flex').addClass('hidden');
        }

        function openAddPelangganModal() {
            $('#addPelangganModal').removeClass('hidden').addClass('flex');
        }

        function closeAddPelangganModal() {
            $('#addPelangganModal').removeClass('flex').addClass('hidden');
            $('#add-pelanggan-form')[0].reset();
        }

        function openEndShiftModal() {
            const totalSales = {{ $totalSales ?? 0 }};
            const initialCash = {{ $activeShift->initial_cash ?? 0 }};
            const finalCash = initialCash + totalSales;
            $('#final_cash_display').val('Rp ' + finalCash.toLocaleString('id-ID'));
            $('#final_cash').val(finalCash);
            $('#endShiftModal').removeClass('hidden').addClass('flex');
        }

        function closeEndShiftModal() {
            $('#endShiftModal').removeClass('flex').addClass('hidden');
        }

        // --- AJAX & EVENT LISTENER MODAL ---
        $(document).on('click', '.add-to-cart-btn', function() {
            const button = $(this);
            const kodeObat = button.data('kode');
            
            button.prop('disabled', true).text('...');

            $.ajax({
                url: "{{ route('pos.add') }}",
                method: 'POST',
                data: { _token: "{{ csrf_token() }}", kode: kodeObat },
                success: function() { location.reload(); },
                error: function() {
                    alert('Gagal menambahkan obat.');
                    button.prop('disabled', false).text('+ Tambah');
                }
            });
        });

        $('#add-pelanggan-form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('pos.addPelangganCepat') }}",
                method: 'POST',
                data: new FormData(this),
                processData: false, contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
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
                error: function() { alert('Gagal menambahkan pelanggan.'); }
            });
        });

        $('#modal-search').on('keyup', function () {
            const query = this.value.toLowerCase();
            $('#obat-table tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(query));
            });
        });
    </script>
@endpush