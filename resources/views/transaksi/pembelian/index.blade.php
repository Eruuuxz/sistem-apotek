@extends('layouts.admin')

@section('title', 'Manajemen Pembelian')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white p-4 sm:p-6 shadow-lg rounded-xl">
        <!-- Tab Navigation -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="pembelianTab" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group"
                            id="sp-tab" type="button" role="tab" aria-controls="sp" aria-selected="true">
                        <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M19 4h-1.25a1 1 0 0 0-1-1h-1.5a1 1 0 0 0-1 1H4a1 1 0 0 0-1 1v1h1.25a1 1 0 0 1 1 1h12.5a1 1 0 0 1 1-1H20V5a1 1 0 0 0-1-1Zm0 3H1v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7Z"/></svg>
                        Perlu Diproses
                        @if($suratPesanans->count() > 0)
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold ml-2 px-2.5 py-0.5 rounded-full">{{ $suratPesanans->count() }}</span>
                        @endif
                    </button>
                </li>
                <li role="presentation">
                    <button class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group"
                            id="riwayat-tab" type="button" role="tab" aria-controls="riwayat" aria-selected="false">
                        <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/></svg>
                        Riwayat Pembelian
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div id="pembelianTabContent">
            <!-- Surat Pesanan Tab -->
            <div class="hidden" id="sp" role="tabpanel" aria-labelledby="sp-tab">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-gray-700">Surat Pesanan Menunggu Diproses</h3>
                    <a href="{{ route('surat_pesanan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Buat SP Baru
                    </a>
                </div>
                 <div class="space-y-3">
                    @forelse ($suratPesanans as $sp)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center space-x-4 mb-3 sm:mb-0">
                                <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $sp->no_sp }}</p>
                                    <p class="text-sm text-gray-600">Supplier: <span class="font-semibold">{{ $sp->supplier->nama ?? '-' }}</span></p>
                                    <p class="text-xs text-gray-500">Tanggal: {{ $sp->tanggal_sp->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 w-full sm:w-auto">
                                <form action="{{ route('pembelian.createFromSp', $sp->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Proses SP ini menjadi pembelian?');">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition duration-300 ease-in-out flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        Proses Pesanan
                                    </button>
                                </form>
                                <a href="{{ route('surat_pesanan.edit', $sp->id) }}" class="text-gray-500 hover:text-yellow-600 p-2 rounded-full bg-gray-200 hover:bg-yellow-100 transition" title="Edit SP">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                </a>
                                <a href="{{ route('surat_pesanan.pdf', $sp->id) }}" target="_blank" class="text-gray-500 hover:text-blue-600 p-2 rounded-full bg-gray-200 hover:bg-blue-100 transition" title="Lihat PDF">
                                     <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639l4.43-4.43a1.012 1.012 0 0 1 1.431 0l4.43 4.43a1.012 1.012 0 0 1 0 .639l-4.43 4.43a1.012 1.012 0 0 1-1.431 0l-4.43-4.43Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 border-2 border-dashed rounded-lg">
                             <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9.75v4.5a3.375 3.375 0 0 1-3.375 3.375h-9.75a3.375 3.375 0 0 1-3.375-3.375v-4.5M21.75 9.75 19.5 7.5l-4.125 4.125-4.125-4.125L4.5 9.75m17.25 0-4.125-4.125M4.5 9.75l4.125-4.125" /></svg>
                            <h4 class="mt-2 text-lg font-semibold text-gray-700">Tidak Ada Surat Pesanan</h4>
                            <p class="mt-1 text-sm text-gray-500">Semua surat pesanan sudah diproses atau belum ada yang dibuat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Pembelian Tab -->
            <div class="hidden" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                 <h3 class="text-lg font-semibold text-gray-700 mb-5">Riwayat Transaksi Pembelian</h3>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-sm border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border px-4 py-2 text-left">No Faktur</th>
                                <th class="border px-4 py-2 text-left">Tanggal</th>
                                <th class="border px-4 py-2 text-left">Supplier</th>
                                <th class="border px-4 py-2 text-right">Total</th>
                                <th class="border px-4 py-2 text-center">Status</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembelians as $row)
                            <tr class="hover:bg-gray-50 {{ $row->status == 'draft' ? 'bg-yellow-50/50' : '' }}">
                                <td class="border px-4 py-2 font-medium">
                                    <span class="block text-gray-800">{{ $row->no_faktur_pbf ?? $row->no_faktur }}</span>
                                    @if(!$row->no_faktur_pbf) <span class="text-xs text-gray-500">(Internal)</span> @endif
                                </td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i') }}</td>
                                <td class="border px-4 py-2">{{ $row->supplier->nama ?? '-' }}</td>
                                <td class="border px-4 py-2 text-right font-semibold text-blue-600">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    @if($row->status == 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                            Menunggu Faktur
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @if($row->status == 'draft')
                                        <a href="{{ route('pembelian.edit', $row->id) }}" class="text-yellow-600 hover:text-yellow-900 font-bold inline-flex items-center text-sm">
                                             <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                            Input Faktur
                                        </a>
                                    @else
                                        <a href="{{ route('pembelian.faktur', $row->id) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center text-sm">
                                             <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.431 0l4.43 4.43a1.012 1.012 0 010 .639l-4.43 4.43a1.012 1.012 0 01-1.431 0l-4.43-4.43z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            Lihat Faktur
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-4 text-center text-gray-500">Belum ada data pembelian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
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
        const tabElements = [
            { id: 'sp', triggerEl: document.querySelector('#sp-tab'), targetEl: document.querySelector('#sp') },
            { id: 'riwayat', triggerEl: document.querySelector('#riwayat-tab'), targetEl: document.querySelector('#riwayat') }
        ];

        const options = {
            defaultTabId: 'sp',
            activeClasses: 'text-blue-600 border-blue-600',
            inactiveClasses: 'text-gray-500 hover:text-gray-600 border-transparent hover:border-gray-300',
            onShow: () => {}
        };

        let activeTabId = options.defaultTabId;

        const getTab = (tabId) => tabElements.find(tab => tab.id === tabId);

        const showTab = (tabId) => {
            const tab = getTab(tabId);
            if (!tab) return;

            tabElements.forEach(t => {
                t.targetEl.classList.add('hidden');
                t.triggerEl.setAttribute('aria-selected', 'false');
                t.triggerEl.classList.remove(...options.activeClasses.split(" "));
                t.triggerEl.classList.add(...options.inactiveClasses.split(" "));
                t.triggerEl.firstElementChild.classList.add('text-gray-400', 'group-hover:text-gray-500');
                t.triggerEl.firstElementChild.classList.remove('text-blue-600');
            });

            tab.targetEl.classList.remove('hidden');
            tab.triggerEl.setAttribute('aria-selected', 'true');
            tab.triggerEl.classList.remove(...options.inactiveClasses.split(" "));
            tab.triggerEl.classList.add(...options.activeClasses.split(" "));
            tab.triggerEl.firstElementChild.classList.remove('text-gray-400', 'group-hover:text-gray-500');
            tab.triggerEl.firstElementChild.classList.add('text-blue-600');
            
            activeTabId = tabId;
        };

        showTab(activeTabId);

        tabElements.forEach(tab => {
            tab.triggerEl.addEventListener('click', () => showTab(tab.id));
        });
    });
</script>
@endpush
