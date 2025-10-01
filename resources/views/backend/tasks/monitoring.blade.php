<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-tv mr-2"></i>
            {{ __('Monitoring Tugas Aktif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="monitoringTasks()" x-cloak>

                {{-- Card untuk Panel Filter --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            <i class="fas fa-filter mr-2 text-gray-400"></i>Filter Pencarian
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            {{-- Filter Pencarian Teks --}}
                            <div class="md:col-span-2">
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari
                                    Tugas</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model.debounce.500ms="filters.search" @input="applyFilters"
                                        id="search" placeholder="Cari berdasarkan judul atau nama staff..."
                                        class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            {{-- Filter Status --}}
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <select x-model="filters.status" @change="applyFilters" id="status"
                                        class="block w-full pl-10 border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Semua Status Aktif</option>
                                        <option value="unassigned">Belum Diambil</option>
                                        <option value="in_progress">Sedang Dikerjakan</option>
                                        <option value="pending_review">Menunggu Review</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card untuk Tabel Hasil --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Daftar Tugas</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Judul Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dikerjakan Oleh</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Lokasi</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                                            <p class="mt-2">Memuat data...</p>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(task, index) in tasks" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="task.title"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400"
                                                x-text="task.task_type.name_task"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.assignee ? task.assignee.name : 'Belum Diambil'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.room ? `${task.room.name_room}` : 'Tidak spesifik'"></td>
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
                                <template x-if="!isLoading && tasks.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                            <p class="mt-4 text-gray-500 dark:text-gray-400">Tidak ada tugas aktif yang
                                                cocok dengan filter Anda.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('monitoringTasks', () => ({
                tasks: [],
                isLoading: true,
                filters: {
                    search: '',
                    status: ''
                },

                init() {
                    this.applyFilters();
                },

                applyFilters() {
                    this.isLoading = true;
                    // Buat parameter URL dari objek filter, abaikan properti yang kosong
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v != null && v !== ''));
                    const params = new URLSearchParams(activeFilters).toString();

                    axios.get(`{{ route('api.tasks.active_data') }}?${params}`)
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal memuat data monitoring:', error);
                            alert('Gagal memuat data tugas. Silakan coba lagi.');
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                statusColor(status) {
                    const colors = {
                        'unassigned': 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    };
                    return colors[status] || 'bg-gray-100';
                },

                statusText(status) {
                    const texts = {
                        'unassigned': 'Belum Diambil',
                        'in_progress': 'Dikerjakan',
                        'pending_review': 'Review',
                    };
                    return texts[status] || status;
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>