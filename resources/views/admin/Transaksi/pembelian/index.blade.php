@extends('layouts.admin')

@section('title', 'Manajemen Pembelian')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Transaksi Pembelian</h2>
            <p class="text-sm text-gray-500">Kelola Surat Pesanan (SP) dan riwayat pembelian barang.</p>
        </div>
        <div>
            {{-- HANYA ADA TOMBOL BUAT SP (Input Manual Dihapus) --}}
             <a href="{{ route('surat_pesanan.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Buat Surat Pesanan Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Tabs Header --}}
        <div class="border-b border-gray-100">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center bg-gray-50/50" id="pembelianTab" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-flex items-center justify-center p-4 border-b-2 transition-colors duration-200 group"
                            id="sp-tab" type="button" role="tab" aria-controls="sp" aria-selected="true">
                        <i data-feather="inbox" class="w-4 h-4 mr-2"></i>
                        SP Menunggu Proses
                        @if($suratPesanans->count() > 0)
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold ml-2 px-2 py-0.5 rounded-full">{{ $suratPesanans->count() }}</span>
                        @endif
                    </button>
                </li>
                <li role="presentation">
                    <button class="inline-flex items-center justify-center p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 transition-colors duration-200 group text-gray-500"
                            id="riwayat-tab" type="button" role="tab" aria-controls="riwayat" aria-selected="false">
                        <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                        Riwayat Pembelian & SP
                    </button>
                </li>
            </ul>
        </div>

        <div class="p-6" id="pembelianTabContent">
            {{-- TAB 1: Surat Pesanan (Perlu Diproses) --}}
            <div class="hidden space-y-4" id="sp" role="tabpanel" aria-labelledby="sp-tab">
                @forelse ($suratPesanans as $sp)
                    <div class="group bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-blue-200 transition-all duration-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                                    <i data-feather="shopping-bag" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $sp->no_sp }}</h4>
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            {{ ucfirst($sp->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        Supplier: <span class="font-semibold text-gray-800">{{ $sp->supplier->nama ?? '-' }}</span>
                                        <span class="mx-1 text-gray-300">|</span>
                                        <span class="text-gray-500">{{ $sp->tanggal_sp->format('d M Y, H:i') }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                {{-- Tombol Proses hanya muncul jika status pending --}}
                                @if($sp->status == 'pending')
                                <form action="{{ route('pembelian.createFromSp', $sp->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Barang sudah datang? Lanjutkan proses penerimaan barang?');">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                        <i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Terima Barang
                                    </button>
                                </form>
                                @endif

                                <div class="flex gap-1">
                                    <a href="{{ route('surat_pesanan.edit', $sp->id) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit SP">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('surat_pesanan.pdf', $sp->id) }}" target="_blank" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Cetak PDF">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <div class="bg-white p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Semua SP Sudah Diproses</h3>
                        <p class="text-gray-500 text-sm mt-1">Belum ada Surat Pesanan baru yang perlu ditindaklanjuti.</p>
                    </div>
                @endforelse
            </div>

            {{-- TAB 2: Riwayat Pembelian & SP --}}
            <div class="hidden" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Faktur & Referensi SP</th>
                                <th class="px-6 py-3 font-semibold">Tanggal Terima</th>
                                <th class="px-6 py-3 font-semibold">Supplier</th>
                                <th class="px-6 py-3 font-semibold text-right">Total Faktur</th>
                                <th class="px-6 py-3 font-semibold text-center">Status</th>
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pembelians as $row)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    {{-- Tampilkan No Faktur --}}
                                    <div class="font-bold text-gray-800">
                                        {{ $row->no_faktur_pbf ?? $row->no_faktur }}
                                        @if(!$row->no_faktur_pbf) <span class="text-[10px] text-gray-400 font-normal border border-gray-200 rounded px-1">Internal</span> @endif
                                    </div>
                                    
                                    {{-- Tampilkan Referensi SP --}}
                                    @if($row->suratPesanan)
                                        <div class="flex items-center mt-1 text-xs text-blue-600 font-medium bg-blue-50 w-fit px-2 py-0.5 rounded">
                                            <i data-feather="link" class="w-3 h-3 mr-1"></i>
                                            SP: {{ $row->suratPesanan->no_sp }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400 mt-1">Tanpa SP (Manual)</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($row->tanggal)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $row->supplier->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($row->status == 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($row->status == 'draft')
                                            <a href="{{ route('pembelian.edit', $row->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                                Lengkapi Faktur
                                            </a>
                                        @else
                                            {{-- Tombol Lihat Faktur --}}
                                            <a href="{{ route('pembelian.faktur', $row->id) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Lihat Detail Faktur">
                                                 <i data-feather="eye" class="w-4 h-4"></i>
                                            </a>
                                            
                                            {{-- Tombol Lihat PDF SP (Jika ada) --}}
                                            @if($row->suratPesanan)
                                                <a href="{{ route('surat_pesanan.pdf', $row->surat_pesanan_id) }}" target="_blank" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors tooltip" title="Lihat PDF SP Asli">
                                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-white">
                                    Belum ada riwayat pembelian yang tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $pembelians->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();

        const tabs = [
            { id: 'sp', button: document.getElementById('sp-tab') },
            { id: 'riwayat', button: document.getElementById('riwayat-tab') }
        ];

        function setActiveTab(tabId) {
            tabs.forEach(tab => {
                const content = document.getElementById(tab.id);
                const isSelected = tab.id === tabId;
                
                if (isSelected) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }

                if (isSelected) {
                    tab.button.classList.add('text-blue-600', 'border-blue-600');
                    tab.button.classList.remove('text-gray-500', 'border-transparent');
                    const icon = tab.button.querySelector('svg');
                    if(icon) icon.classList.add('text-blue-600');
                } else {
                    tab.button.classList.remove('text-blue-600', 'border-blue-600');
                    tab.button.classList.add('text-gray-500', 'border-transparent');
                    const icon = tab.button.querySelector('svg');
                    if(icon) icon.classList.remove('text-blue-600');
                }
            });
        }

        tabs.forEach(tab => {
            if(tab.button) {
                tab.button.addEventListener('click', () => setActiveTab(tab.id));
            }
        });

        setActiveTab('sp');
    });
</script>
@endpush