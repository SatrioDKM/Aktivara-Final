<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Tugas Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="myHistory()">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                <input type="date" x-model="filters.start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                <input type="date" x-model="filters.end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Tugas</label>
                                <select x-model="filters.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif (Dikerjakan/Ditolak)</option>
                                    <option value="pending_review">Menunggu Review</option>
                                    <option value="completed">Selesai</option>
                                </select>
                            </div>
                            <div>
                                <button @click="applyFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Filter
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <input type="text" x-model.debounce.500ms="filters.search" @input="applyFilters"
                                placeholder="Cari berdasarkan judul tugas..."
                                class="block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Judul Tugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Dibuat Oleh</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl.
                                            Update</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Memuat riwayat...
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="task in history.data" :key="task.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="task.title">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.creator ? task.creator.name : 'N/A'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                            <td class="px-6 py-4"><span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(task.status)"
                                                    x-text="statusText(task.status)"></span></td>
                                            <td class="px-6 py-4 text-center text-sm font-medium"><a
                                                    :href="`/tasks/${task.id}`"
                                                    class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a></td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && (!history.data || history.data.length === 0)">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data yang
                                                cocok dengan filter Anda.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 flex justify-between items-center" x-show="history.total > 0">
                            <p class="text-sm text-gray-700">Menampilkan <span x-text="history.from || 0"></span> -
                                <span x-text="history.to || 0"></span> dari <span x-text="history.total || 0"></span>
                                hasil</p>
                            <div class="flex space-x-2">
                                <button @click="fetchHistory(history.current_page - 1)"
                                    :disabled="!history.prev_page_url"
                                    class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Sebelumnya</button>
                                <button @click="fetchHistory(history.current_page + 1)"
                                    :disabled="!history.next_page_url"
                                    class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Berikutnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function myHistory() {
            return {
                isLoading: true,
                history: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                filters: { start_date: '', end_date: '', status: '', search: '' },

                init() {
                    // Panggil API dengan filter default (kosong) saat halaman dimuat
                    this.fetchHistory(1);
                },
                applyFilters() {
                    // Reset ke halaman pertama setiap kali filter baru diterapkan
                    this.fetchHistory(1);
                },
                fetchHistory(page) {
                    if (page < 1) return;
                    this.isLoading = true;
                    // Hapus properti filter yang kosong sebelum mengirim request
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v !== null && v !== ''));
                    const params = new URLSearchParams({ page: page, ...activeFilters }).toString();

                    fetch(`{{ route('api.tasks.my_history_data') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => { this.history = data; })
                        .finally(() => this.isLoading = false);
                },
                statusColor(status) {
                    return {
                        'in_progress': 'bg-blue-100 text-blue-800',
                        'pending_review': 'bg-yellow-100 text-yellow-800',
                        'completed': 'bg-green-100 text-green-800',
                        'rejected': 'bg-red-100 text-red-800'
                    }[status] || 'bg-gray-100 text-gray-800';
                },
                statusText(status) {
                    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
            }
        }
    </script>
</x-app-layout>