<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat & Laporan Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="historyPage(@js($data))">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Filter Riwayat Tugas</h3>

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                            <div>
                                <label for="departmentFilter"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen</label>
                                <select id="departmentFilter" class="mt-1 block w-full">
                                    <option value="">Semua Departemen</option>
                                    @foreach($data['departments'] as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label for="staffFilter"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Staff</label>
                                <select id="staffFilter" class="mt-1 block w-full">
                                    <option value="">Semua Staff</option>
                                    @foreach($data['staffUsers'] as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="statusFilter"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="statusFilter" class="mt-1 block w-full">
                                    <option value="">Semua Status</option>
                                    <option value="completed">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="pending_review">Perlu Review</option>
                                </select>
                            </div>

                            <div>
                                <label for="startDate"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                                    Tanggal</label>
                                <input type="date" x-model="filters.start_date" id="startDate"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                            </div>
                            <div>
                                <label for="endDate"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                                    Tanggal</label>
                                <input type="date" x-model="filters.end_date" id="endDate"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                            </div>

                            <div class="lg:col-span-5 relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="filters.search"
                                    placeholder="Cari berdasarkan judul tugas atau nama staff..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembuat
                                        Tugas</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl.
                                        Selesai/Update</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && tasks.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada data ditemukan
                                            sesuai filter.</td>
                                    </tr>
                                </template>
                                <template x-for="task in tasks" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium" x-text="task.title"></td>
                                        <td class="px-6 py-4" x-text="task.staff ? task.staff.name : 'N/A'"></td>
                                        <td class="px-6 py-4" x-text="task.creator ? task.creator.name : 'N/A'"></td>
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

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function historyPage(data) {
                return {
                    tasks: [],
                    pagination: {},
                    isLoading: true,
                    staffUsers: data.staffUsers || [],
                    departments: data.departments || [],
                    filters: {
                        start_date: '',
                        end_date: '',
                        status: '',
                        staff_id: '',
                        department: '',
                        search: ''
                    },
                    init() {
                        this.fetchHistory();

                        // Initialize Select2
                        $('#departmentFilter, #staffFilter, #statusFilter').select2({ theme: 'classic', width: '100%', allowClear: true, placeholder: 'Pilih Opsi' });

                        // Watch for changes
                        $('#departmentFilter').on('change', (e) => this.filters.department = e.target.value);
                        $('#staffFilter').on('change', (e) => this.filters.staff_id = e.target.value);
                        $('#statusFilter').on('change', (e) => this.filters.status = e.target.value);

                        this.$watch('filters', () => this.applyFilters(), { deep: true });
                    },
                    applyFilters() {
                        this.fetchHistory(1);
                    },
                    fetchHistory(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({ page, ...this.filters });
                        fetch(`/api/tasks/history?${params.toString()}`, { headers: {'Accept': 'application/json'} })
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