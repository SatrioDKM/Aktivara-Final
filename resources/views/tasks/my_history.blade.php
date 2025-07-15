<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('History Tugas Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="taskHistory()">
                <!-- Panel Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Tombol Filter Status -->
                            <div class="flex-shrink-0">
                                <span class="isolate inline-flex rounded-md shadow-sm">
                                    <button @click="setFilter('active')"
                                        :class="{ 'bg-indigo-600 text-white': filters.status === 'active', 'bg-white text-gray-700 hover:bg-gray-50': filters.status !== 'active' }"
                                        type="button"
                                        class="relative inline-flex items-center rounded-l-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-10 transition">Tugas
                                        Aktif</button>
                                    <button @click="setFilter('completed')"
                                        :class="{ 'bg-indigo-600 text-white': filters.status === 'completed', 'bg-white text-gray-700 hover:bg-gray-50': filters.status !== 'completed' }"
                                        type="button"
                                        class="relative -ml-px inline-flex items-center rounded-r-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-10 transition">Tugas
                                        Selesai</button>
                                </span>
                            </div>
                            <!-- Search Bar -->
                            <div class="w-full md:w-1/2">
                                <input type="text" x-model.debounce.500ms="filters.search" @input="fetchTasks(1)"
                                    placeholder="Cari berdasarkan judul tugas..."
                                    class="block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Tugas -->
                <div class="space-y-4">
                    <template x-if="isLoading">
                        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Memuat tugas...</div>
                    </template>
                    <template x-for="task in tasks.data" :key="task.id">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                                <div>
                                    <p class="font-bold text-lg text-gray-800" x-text="task.title"></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Lokasi: <span
                                            x-text="task.room ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor}` : 'Tidak spesifik'"></span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4 flex-shrink-0">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="statusColor(task.status)"
                                        x-text="task.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                    <a :href="`/tasks/${task.id}`"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">Lihat
                                        Detail</a>
                                    <!-- Tombol Lapor hanya untuk tugas aktif -->
                                    <template x-if="['in_progress', 'rejected'].includes(task.status)">
                                        <a :href="`/tasks/${task.id}`"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                            Submit Laporan
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!isLoading && tasks.data.length === 0">
                        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Tidak ada tugas
                            ditemukan.</div>
                    </template>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center" x-show="tasks.total > 0">
                    <p class="text-sm text-gray-700">Menampilkan <span x-text="tasks.from || 0"></span> sampai <span
                            x-text="tasks.to || 0"></span> dari <span x-text="tasks.total || 0"></span> hasil</p>
                    <div class="flex space-x-2">
                        <button @click="fetchTasks(tasks.current_page - 1)" :disabled="!tasks.prev_page_url"
                            class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Sebelumnya</button>
                        <button @click="fetchTasks(tasks.current_page + 1)" :disabled="!tasks.next_page_url"
                            class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Berikutnya</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function taskHistory() {
            return {
                tasks: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                isLoading: true,
                filters: { search: '', status: 'active' }, // Default filter adalah tugas aktif

                init() { this.fetchTasks(); },

                setFilter(status) {
                    this.filters.status = status;
                    this.fetchTasks(1); // Reset ke halaman pertama saat filter diubah
                },

                fetchTasks(page = 1) {
                    if (page < 1) return;
                    this.isLoading = true;
                    const params = new URLSearchParams({ ...this.filters, page: page }).toString();

                    fetch(`{{ route('api.tasks.my_history_data') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => { this.tasks = data; })
                    .finally(() => this.isLoading = false);
                },

                statusColor(status) {
                    const colors = {
                        'in_progress': 'bg-blue-100 text-blue-800',
                        'pending_review': 'bg-yellow-100 text-yellow-800',
                        'completed': 'bg-green-100 text-green-800',
                        'rejected': 'bg-red-100 text-red-800',
                    };
                    return colors[status] || 'bg-gray-100';
                }
            }
        }
    </script>
</x-app-layout>