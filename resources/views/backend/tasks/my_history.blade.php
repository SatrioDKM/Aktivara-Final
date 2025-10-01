<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Tugas Saya') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="myHistoryPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                            <a href="#" @click.prevent="changeTab('active')"
                                :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'active', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'active' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                Tugas Aktif
                            </a>
                            <a href="#" @click.prevent="changeTab('completed')"
                                :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'completed', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'completed' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                                Tugas Selesai
                            </a>
                        </nav>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-b-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="date" x-model="filters.start_date"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                placeholder="Dari Tanggal">
                            <input type="date" x-model="filters.end_date"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                placeholder="Sampai Tanggal">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="filters.search"
                                    placeholder="Cari judul tugas..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul
                                        Tugas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl.
                                        Diperbarui</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="5" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && tasks.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="task in tasks" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium" x-text="task.title"></td>
                                        <td class="px-6 py-4" x-text="task.task_type.name_task"></td>
                                        <td class="px-6 py-4 text-center"><span
                                                class="px-3 py-1 text-xs capitalize font-semibold rounded-full"
                                                x-text="task.status.replace('_', ' ')"></span></td>
                                        <td class="px-6 py-4"
                                            x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                        <td class="px-6 py-4 text-center">
                                            <a :href="`/tasks/${task.id}`"
                                                class="text-indigo-600 hover:underline text-sm">Lihat Detail</a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                            <template x-for="link in pagination.links">
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-500 hover:bg-gray-200': !link.active }"
                                    class="px-3 py-2 rounded-md text-sm" x-html="link.label"></button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function myHistoryPage() {
                return {
                    tasks: [], pagination: {}, isLoading: true,
                    currentTab: 'active',
                    filters: { status: 'active', start_date: '', end_date: '', search: '' },
                    init() {
                        this.fetchHistory();
                        this.$watch('filters.start_date', () => this.applyFilters());
                        this.$watch('filters.end_date', () => this.applyFilters());
                        this.$watch('filters.search', () => this.applyFilters());
                    },
                    changeTab(tab) {
                        this.currentTab = tab;
                        this.filters.status = tab;
                        this.applyFilters();
                    },
                    applyFilters() { this.fetchHistory(1); },
                    fetchHistory(page = 1) {
                        this.isLoading = true;
                        this.filters.status = this.currentTab;
                        const params = new URLSearchParams({ page, ...this.filters });
                        fetch(`/api/tasks/my-history?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.tasks = data.data;
                            data.links.forEach(link => {
                                if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                                if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                            });
                            this.pagination = data;
                            this.isLoading = false;
                        });
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