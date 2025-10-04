<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-clipboard-list mr-2"></i>
            {{ __('Manajemen Jenis Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="taskTypesPage()" x-cloak>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Daftar Jenis
                            Tugas</h3>
                        <a href="{{ route('master.task_types.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Jenis Tugas
                        </a>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div wire:ignore>
                                <select id="departmentFilter" class="w-full">
                                    <option value="">Semua Departemen</option>
                                    <option value="HK">Housekeeping</option>
                                    <option value="TK">Teknisi</option>
                                    <option value="SC">Security</option>
                                    <option value="PK">Parking</option>
                                    <option value="WH">Warehouse</option>
                                    <option value="UMUM">Umum</option>
                                </select>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search" placeholder="Cari nama tugas..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Tugas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Departemen</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Prioritas Default</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi</th>
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
                                        <td colspan="5" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak
                                            ada data ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="(type, index) in taskTypes" :key="type.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100"
                                            x-text="type.name_task"></td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400" x-text="type.departemen">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                                :class="{
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': type.priority_level === 'low',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': type.priority_level === 'medium',
                                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300': type.priority_level === 'high',
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': type.priority_level === 'critical'
                                                }" x-text="type.priority_level">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/task-types/${type.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                                    title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/task-types/${type.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-600 transition"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(type.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600 transition"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center"
                        x-show="!isLoading && pagination.total > 0">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                            <template x-for="link in pagination.links">
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'bg-white dark:bg-gray-800 text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition border dark:border-gray-600"
                                    x-html="link.label"></button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function taskTypesPage() {
            return {
                taskTypes: [],
                pagination: {},
                isLoading: true,
                search: '',
                departmentFilter: '',

                init() {
                    this.fetchTaskTypes();
                    this.$watch('search', () => this.applyFilters());

                    const self = this;
                    $('#departmentFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Departemen', allowClear: true })
                        .on('change', function() {
                            self.departmentFilter = $(this).val();
                            self.applyFilters();
                        });

                    const toastMessage = sessionStorage.getItem('toastMessage');
                    if (toastMessage) {
                        window.iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                        sessionStorage.removeItem('toastMessage');
                    }
                },

                applyFilters() {
                    this.fetchTaskTypes(1);
                },

                fetchTaskTypes(page = 1) {
                    this.isLoading = true;
                    const params = new URLSearchParams({ page, perPage: 10, search: this.search, department: this.departmentFilter });

                    axios.get(`/api/task-types?${params.toString()}`)
                        .then(res => {
                            this.taskTypes = res.data.data;
                            res.data.links.forEach(link => {
                                if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                                if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                            });
                            this.pagination = res.data;
                        })
                        .catch(err => {
                            console.error("Gagal mengambil data:", err);
                            window.iziToast.error({ title: 'Gagal!', message: 'Tidak dapat mengambil data dari server.', position: 'topRight' });
                        })
                        .finally(() => this.isLoading = false);
                },

                changePage(url) {
                    if (!url) return;
                    this.fetchTaskTypes(new URL(url).searchParams.get('page'));
                },

                confirmDelete(id) {
                    window.iziToast.question({
                        timeout: 20000, close: false, overlay: true,
                        title: 'Konfirmasi Hapus', message: 'Anda yakin ingin menghapus data ini?', position: 'center',
                        buttons: [
                            ['<button><b>YA, HAPUS</b></button>', (instance, toast) => {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                this.deleteTaskType(id);
                            }, true],
                            ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                        ]
                    });
                },

                deleteTaskType(id) {
                    axios.delete(`/api/task-types/${id}`)
                        .then(res => {
                            window.iziToast.success({ title: 'Berhasil', message: res.data.message || 'Jenis tugas telah dihapus.', position: 'topRight' });
                            const isLastItemOnPage = this.taskTypes.length === 1 && this.pagination.current_page > 1;
                            this.fetchTaskTypes(isLastItemOnPage ? this.pagination.current_page - 1 : this.pagination.current_page);
                        })
                        .catch(err => window.iziToast.error({ title: 'Gagal', message: err.response?.data?.message || 'Gagal menghapus data.', position: 'topRight' }));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>