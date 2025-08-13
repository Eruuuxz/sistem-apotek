{{-- ===================================== --}}
{{-- COMPONENT: MODAL --}}
{{-- ===================================== --}}
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-4 rounded shadow w-96">
        <h2 id="modalTitle" class="font-bold mb-3">Judul Modal</h2>
        <div id="modalBody">
            <p>Isi konten modal...</p>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeModal()" class="px-3 py-1 bg-gray-400 text-white rounded">Batal</button>
            <button id="modalSave" class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{-- COMPONENT: TOAST NOTIFICATION --}}
{{-- ===================================== --}}
<div id="toastContainer" class="fixed bottom-4 right-4 space-y-2 z-50"></div>

{{-- ===================================== --}}
{{-- COMPONENT: BADGE --}}
{{-- Contoh penggunaan:
    @include('components.ui', ['badgeText' => 'Obat Keras', 'badgeColor' => 'red'])
--}}
@isset($badgeText)
<span class="px-2 py-1 rounded text-xs bg-{{ $badgeColor }}-200 text-{{ $badgeColor }}-800">
    {{ $badgeText }}
</span>
@endisset

{{-- ===================================== --}}
{{-- COMPONENT: CHART --}}
{{-- ===================================== --}}
<canvas id="chartComponent" class="hidden"></canvas>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Modal Functions
    function openModal(title = 'Modal', body = '', onSave = null) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalBody').innerHTML = body;
        const modal = document.getElementById('modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (onSave) {
            document.getElementById('modalSave').onclick = onSave;
        }
    }
    function closeModal() {
        const modal = document.getElementById('modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Toast Function
    function showToast(msg, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-600' :
                        type === 'error' ? 'bg-red-600' :
                        'bg-gray-600';

        const toast = document.createElement('div');
        toast.className = `${bgColor} text-white px-4 py-2 rounded shadow`;
        toast.innerText = msg;
        document.getElementById('toastContainer').appendChild(toast);

        setTimeout(() => toast.remove(), 3000);
    }

    // Chart Function
    function renderChart(labels = [], data = [], labelText = 'Data', type = 'bar') {
        const ctx = document.getElementById('chartComponent');
        ctx.classList.remove('hidden');
        new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: labelText,
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });
    }
</script>
@endpush
