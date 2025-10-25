<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-history mr-2"></i>
            {{ __('Riwayat Tugas Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="myHistory()" x-cloak>

                {{-- Card untuk Panel Filter --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-filter mr-3 text-gray-400"></i>Filter Pencarian
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                            {{-- Filter Dari Tanggal --}}
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                                    Tanggal</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-calendar-day text-gray-400"></i></div>
                                    <input type="date" x-model="filters.start_date" id="start_date"
                                        class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            {{-- Filter Sampai Tanggal --}}
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                                    Tanggal</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-calendar-week text-gray-400"></i></div>
                                    <input type="date" x-model="filters.end_date" id="end_date"
                                        class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            {{-- Filter Status --}}
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status
                                    Tugas</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-tag text-gray-400"></i></div>
                                    <select x-model="filters.status" id="status"
                                        class="block w-full pl-10 border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Semua Status</option>
                                        <option value="in_progress">Dikerjakan</option>
                                        <option value="rejected">Ditolak</option>
                                        <option value="pending_review">Menunggu Review</option>
                                        <option value="completed">Selesai</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Tombol Aksi Filter --}}
                            <div class="flex space-x-2">
                                <button @click="applyFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
                                    <i class="fas fa-search mr-2"></i>Filter
                                </button>
                                <button @click="resetFilters" title="Reset Filter"
                                    class="p-2 border rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 focus:ring-indigo-500">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Filter Pencarian Teks --}}
                        <div>
                            <label for="search" class="sr-only">Cari</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="text" x-model.debounce.500ms="filters.search" @input="applyFilters"
                                    id="search" placeholder="Cari berdasarkan judul tugas..."
                                    class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card untuk Tabel Hasil --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Judul Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dibuat Oleh</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tgl. Update</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 relative">
                                {{-- Overlay Loading --}}
                                <template x-if="isLoading">
                                    <tr
                                        class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-50 dark:bg-opacity-50 flex items-center justify-center z-10">
                                        <td class="text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                                            <p class="mt-2">Memuat riwayat...</p>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="task in history.data" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="task.title"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.creator ? task.creator.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="statusColor(task.status)"
                                                x-text="statusText(task.status)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a :href="`/tasks/${task.id}`"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Lihat
                                                Detail</a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && (!history.data || history.data.length === 0)">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                            <p class="mt-4 text-gray-500 dark:text-gray-400">Tidak ada riwayat tugas
                                                yang cocok dengan filter Anda.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    {{-- Kontrol Paginasi --}}
                    <div class="p-4 flex justify-between items-center bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
                        x-show="history.total > 0">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Menampilkan <span class="font-medium" x-text="history.from || 0"></span> - <span
                                class="font-medium" x-text="history.to || 0"></span> dari <span class="font-medium"
                                x-text="history.total || 0"></span> hasil
                        </p>
                        <div class="flex space-x-2">
                            <button @click="fetchHistory(history.current_page - 1)" :disabled="!history.prev_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Sebelumnya</button>
                            <button @click="fetchHistory(history.current_page + 1)" :disabled="!history.next_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('myHistory', () => ({
                isLoading: true,
                history: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null, last_page: 1 },
                filters: { start_date: '', end_date: '', status: '', search: '' },

                init() {
                    this.fetchHistory(1);
                },

                applyFilters() {
                    this.fetchHistory(1);
                },

                resetFilters() {
                    this.filters = { start_date: '', end_date: '', status: '', search: '' };
                    this.fetchHistory(1);
                },

                fetchHistory(page) {
                    if (page < 1 || (page > this.history.last_page && this.history.last_page !== null)) return;

                    this.isLoading = true;
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v !== null && v !== ''));
                    const params = new URLSearchParams({ page: page, ...activeFilters }).toString();

                    axios.get(`{{ route('api.tasks.my_history_data') }}?${params}`)
                        .then(response => {
                            this.history = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal memuat riwayat tugas:', error);
                            // alert('Gagal memuat riwayat. Silakan coba lagi.');
                            window.iziToast.error({
                                title: 'Gagal!',
                                message: 'Gagal memuat riwayat. Silakan coba lagi.',
                                position: 'topRight'
                            });
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                statusColor(status) {
                    const colors = {
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                },

                statusText(status) {
                    const texts = {
                        'in_progress': 'Dikerjakan',
                        'pending_review': 'Review',
                        'completed': 'Selesai',
                        'rejected': 'Ditolak'
                    };
                    return texts[status] || status.replace(/_/g, ' ');
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>