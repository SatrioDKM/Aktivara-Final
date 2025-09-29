<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Jenis Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="taskTypesPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('master.task_types.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Jenis Tugas
                        </a>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select id="departmentFilter" class="w-full">
                                <option value="">Semua Departemen</option>
                                <option value="HK">Housekeeping</option>
                                <option value="TK">Teknisi</option>
                                <option value="SC">Security</option>
                                <option value="UMUM">Umum</option>
                            </select>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search" placeholder="Cari nama tugas..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Tugas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Departemen</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                        Prioritas Default</th>
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
                                <template x-if="!isLoading && taskTypes.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada data.</td>
                                    </tr>
                                </template>
                                <template x-for="(type, index) in taskTypes" :key="type.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 font-medium" x-text="type.name_task"></td>
                                        <td class="px-6 py-4" x-text="type.departemen"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize"
                                                :class="{
                                                    'bg-blue-100 text-blue-800': type.priority_level === 'low',
                                                    'bg-yellow-100 text-yellow-800': type.priority_level === 'medium',
                                                    'bg-orange-100 text-orange-800': type.priority_level === 'high',
                                                    'bg-red-100 text-red-800': type.priority_level === 'critical'
                                                }" x-text="type.priority_level">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/task-types/${type.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200"
                                                    title="Lihat"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/task-types/${type.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(type.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <p class="text-sm text-gray-600">Menampilkan <span x-text="pagination.from || 0"></span>-<span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                        </p>
                        <nav x-show="pagination.last_page > 1">
                            {{-- Paginasi Kustom --}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function taskTypesPage() {
                return {
                    taskTypes: [], pagination: {}, isLoading: true,
                    search: '', departmentFilter: '',
                    init() { /* ... Logika init ... */ },
                    applyFilters() { this.fetchTaskTypes(1); },
                    fetchTaskTypes(page = 1) { /* ... Logika fetch ... */ },
                    changePage(url) { /* ... Logika change page ... */ },
                    getCsrfToken() { /* ... Logika get CSRF ... */ },
                    confirmDelete(id) { /* ... Logika iziToast confirm ... */ },
                    async deleteTaskType(id) { /* ... Logika delete fetch ... */ }
                }
            }
    </script>
    @endpush
</x-app-layout>