<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-history mr-2"></i>
            {{ __('Riwayat Keluar/Masuk Aset') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="assetHistory()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Panel Filter --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm dark:text-gray-300">Dari Tgl</label>
                        <input type="date" x-model="filters.start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm dark:text-gray-300">Sampai Tgl</label>
                        <input type="date" x-model="filters.end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm dark:text-gray-300">Jenis Transaksi</label>
                        <select x-model="filters.type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            <option value="in">Masuk</option>
                            <option value="out">Keluar</option>
                        </select>
                    </div>
                    <div class="relative flex-grow">
                        <label class="block text-sm dark:text-gray-300">Cari Nama Aset</label>
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pt-6 pointer-events-none"><i
                                class="fas fa-search text-gray-400"></i></div>
                        <input type="search" x-model.debounce.500ms="filters.search" placeholder="Cari aset..."
                            class="mt-1 w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
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
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs uppercase">Nama Aset</th>
                                <th class="px-6 py-3 text-left text-xs uppercase">S/N</th>
                                <th class="px-6 py-3 text-left text-xs uppercase">Oleh</th>
                                <th class="px-6 py-3 text-left text-xs uppercase">Dokumen/Penerima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-if="isLoading">
                                <tr>
                                    <td colspan="6" class="text-center py-10"><i
                                            class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                </tr>
                            </template>
                            <template x-if="!isLoading && history.length === 0">
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak ada
                                        data riwayat.</td>
                                </tr>
                            </template>
                            <template x-for="item in history" :key="`${item.type}-${item.asset_id}-${item.timestamp}`">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"
                                        x-text="new Date(item.timestamp).toLocaleString('id-ID')"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="item.type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            x-text="item.type === 'in' ? 'MASUK' : 'KELUAR'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        x-text="item.name_asset"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.serial_number || '-'">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.user_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"
                                        x-text="item.type === 'out' ? `${item.document_number} (${item.recipient_name})` : '-'">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                {{-- Paginasi --}}
                <div class="p-4 flex justify-between items-center bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
                    x-show="!isLoading && pagination.total > 0">
                    <p class="text-sm">Menampilkan <span x-text="pagination.from || 0"></span>-<span
                            x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span></p>
                    <nav class="flex items-center space-x-1">
                        <template x-for="link in pagination.links">
                            <button @click="changePage(link.url)" :disabled="!link.url"
                                :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white dark:bg-gray-800 hover:bg-gray-200': !link.active && link.url, 'bg-gray-100 cursor-not-allowed': !link.url }"
                                class="px-3 py-2 rounded-md text-sm transition border" x-html="link.label"></button>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function assetHistory() {
            return {
                history: [],
                pagination: {},
                isLoading: true,
                filters: { start_date: '', end_date: '', type: '', search: '' },
                init() {
                    this.fetchHistory();
                },
                 applyFilters() {
                    this.fetchHistory(1);
                },
                resetFilters() {
                    this.filters = { start_date: '', end_date: '', type: '', search: '' };
                    this.fetchHistory(1);
                },
                fetchHistory(page = 1) {
                    this.isLoading = true;
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v != null && v !== ''));
                    const params = new URLSearchParams({ page, perPage: 15, ...activeFilters });

                    axios.get(`/api/asset-history?${params.toString()}`)
                    .then(response => {
                        this.history = response.data.data;
                         response.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = response.data;
                    })
                    .catch(error => window.iziToast.error({ title: 'Gagal', message: 'Gagal memuat riwayat aset.', position: 'topRight' }))
                    .finally(() => this.isLoading = false);
                },
                changePage(url) {
                    if (!url) return;
                    this.fetchHistory(new URL(url).searchParams.get('page'));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>