<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="dashboard()" x-cloak x-init="initDashboard()">

                <div x-show="isLoading" class="text-center text-gray-500 py-10">
                    <i class="fas fa-spinner fa-spin fa-3x text-gray-400"></i>
                    <p class="mt-4">Memuat data dashboard...</p>
                </div>

                <div x-show="!isLoading" style="display: none;">
                    {{-- ======================== TAMPILAN ADMIN & MANAGER ======================== --}}
                    <template x-if="stats.role_type === 'admin'">
                        <div class="space-y-6">
                            {{-- Card Statistik Tugas --}}
                            @if (!str_starts_with(auth()->user()->role_id, 'WH'))
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div
                                        class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 flex items-center">
                                        <div class="bg-gray-100 rounded-full p-3 me-4"><i
                                                class="fas fa-inbox fa-lg text-gray-500"></i></div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Tugas
                                                Baru</h3>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="stats.tasks.unassigned"></p>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 flex items-center">
                                        <div class="bg-blue-100 rounded-full p-3 me-4"><i
                                                class="fas fa-cogs fa-lg text-blue-500"></i></div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Tugas
                                                Dikerjakan</h3>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="stats.tasks.in_progress"></p>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 flex items-center">
                                        <div class="bg-yellow-100 rounded-full p-3 me-4"><i
                                                class="fas fa-user-check fa-lg text-yellow-500"></i></div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                                Menunggu Review</h3>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="stats.tasks.pending_review"></p>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 flex items-center">
                                        <div class="bg-purple-100 rounded-full p-3 me-4"><i
                                                class="fas fa-users fa-lg text-purple-500"></i></div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total
                                                Pengguna</h3>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100"
                                                x-text="stats.total_users"></p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Card Statistik Aset --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Aset Tetap
                                    </h3>
                                    <p class="mt-1 text-3xl font-semibold text-blue-600"
                                        x-text="stats.assets.total_fixed"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang Habis Pakai
                                    </h3>
                                    <p class="mt-1 text-3xl font-semibold text-green-600"
                                        x-text="stats.assets.total_consumable"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Aset Dalam
                                        Perbaikan</h3>
                                    <p class="mt-1 text-3xl font-semibold text-yellow-600"
                                        x-text="stats.assets.fixed_in_maintenance"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang Stok Menipis
                                    </h3>
                                    <p class="mt-1 text-3xl font-semibold text-red-600"
                                        x-text="stats.assets.consumable_low_stock"></p>
                                </div>
                            </div>
                            {{-- Bagian Chart --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Analitik Visual
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                            Mulai</label>
                                        <input type="date" x-model="filters.start_date"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                            Akhir</label>
                                        <input type="date" x-model="filters.end_date"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <x-primary-button @click="getDashboardData()"
                                        class="w-full sm:w-auto justify-center">
                                        <i class="fas fa-filter me-2"></i> Filter
                                    </x-primary-button>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div class="lg:col-span-1 p-4 border rounded-lg dark:border-gray-700">
                                        <h4 class="font-semibold text-center mb-2 dark:text-gray-300">Pergerakan Barang
                                        </h4>
                                        <div class="h-64"><canvas id="assetMovementChart"></canvas></div>
                                    </div>
                                    @if (!str_starts_with(auth()->user()->role_id, 'WH'))
                                        <div class="lg:col-span-1 p-4 border rounded-lg dark:border-gray-700">
                                            <h4 class="font-semibold text-center mb-2 dark:text-gray-300">Status Tugas</h4>
                                            <div class="h-64"><canvas id="taskStatusChart"></canvas></div>
                                        </div>
                                    @endif
                                    <div class="lg:col-span-1 p-4 border rounded-lg dark:border-gray-700">
                                        <h4 class="font-semibold text-center mb-2 dark:text-gray-300">Komposisi Aset
                                        </h4>
                                        <div class="h-64"><canvas id="assetStatusChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ======================== TAMPILAN LEADER ======================== --}}
                    <template x-if="stats.role_type === 'leader'">
                        <div class="space-y-6">
                            @if (auth()->user()->role_id !== 'WH01')
                                <div class="flex justify-end">
                                    <a href="{{ route('tasks.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-plus me-2"></i> Buat Tugas Baru
                                    </a>
                                </div>
                            @endif
                            {{-- ======================== WIDGET STATISTIK LEADER (BARU) ======================== --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {{-- Total Tugas --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-3 me-4">
                                        <i class="fas fa-list-check fa-lg text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate">Total Tugas (Dept)</h4>
                                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.stats.totalTasks ?? 0"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Tugas Selesai --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border dark:border-gray-700">
                                <div class="flex items-center">
                                     <div class="bg-green-100 dark:bg-green-900/50 rounded-full p-3 me-4">
                                        <i class="fas fa-check-circle fa-lg text-green-500 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-green-700 dark:text-green-300 truncate">Tugas Selesai</h4>
                                        <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400" x-text="stats.stats.completedTasks ?? 0"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Tugas Belum Selesai --}}
                             <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border dark:border-gray-700">
                                <div class="flex items-center">
                                     <div class="bg-yellow-100 dark:bg-yellow-900/50 rounded-full p-3 me-4">
                                        <i class="fas fa-hourglass-half fa-lg text-yellow-500 dark:text-yellow-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 truncate">Belum Selesai</h4>
                                        <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400" x-text="stats.stats.pendingTasks ?? 0"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Tugas Ditolak --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border dark:border-gray-700">
                                <div class="flex items-center">
                                     <div class="bg-red-100 dark:bg-red-900/50 rounded-full p-3 me-4">
                                        <i class="fas fa-times-circle fa-lg text-red-500 dark:text-red-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-red-700 dark:text-red-300 truncate">Tugas Ditolak</h4>
                                        <p class="mt-1 text-3xl font-bold text-red-600 dark:text-red-400" x-text="stats.stats.rejectedTasks ?? 0"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ======================== AKHIR WIDGET BARU ======================== --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                                    <div><label class="block text-sm dark:text-gray-300">Status</label><select
                                            id="filterStatus"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Semua Status</option>
                                            <option value="unassigned">Belum Dikerjakan</option>
                                            <option value="dikerjakan">Dikerjakan</option>
                                            <option value="pending_review">Perlu Review</option>
                                            <option value="completed">Selesai</option>
                                        </select></div>
                                    <div><label class="block text-sm dark:text-gray-300">Staff</label><select
                                            id="filterStaff"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Semua Staff</option><template
                                                x-for="staff in stats.staff_list" :key="staff.id">
                                                <option :value="staff.id" x-text="staff.name"></option>
                                            </template>
                                        </select></div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                <table id="tasksTable" class="display table-auto w-full text-sm">
                                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th>Tugas</th>
                                            <th>Status</th>
                                            <th>Tgl. Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </template>

                    {{-- ======================== TAMPILAN STAFF ======================== --}}
                    @if (!str_starts_with(auth()->user()->role_id, 'WH'))
                        <template x-if="stats.role_type === 'staff'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <a href="{{ route('tasks.my_tasks') }}"
                                    class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition flex items-center">
                                    <div class="bg-blue-100 rounded-full p-3 me-4"><i
                                            class="fas fa-person-running fa-lg text-blue-500"></i></div>
                                    <div>
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300 truncate">Tugas
                                            Aktif Anda</h3>
                                        <p class="mt-1 text-3xl font-semibold text-blue-600 dark:text-blue-400"
                                            x-text="stats.my_active_tasks_count"></p>
                                    </div>
                                </a>
                                <a href="{{ route('tasks.my_history') }}"
                                    class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-green-50 dark:hover:bg-gray-700/50 transition flex items-center">
                                    <div class="bg-green-100 rounded-full p-3 me-4"><i
                                            class="fas fa-history fa-lg text-green-500"></i></div>
                                    <div>
                                        <h3 class="text-sm font-medium text-green-800 dark:text-green-300 truncate">
                                            Total Riwayat Tugas</h3>
                                        <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400"
                                            x-text="stats.my_completed_tasks_count + stats.my_active_tasks_count"></p>
                                    </div>
                                </a>
                            </div>
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Papan Tugas
                                        Tersedia</h3>
                                    <div class="mb-4 relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" x-model.debounce.500ms="search" @input="getDashboardData(1)"
                                            placeholder="Cari berdasarkan judul tugas..."
                                            class="block w-full rounded-md border-gray-300 ps-10 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="space-y-4">
                                        <div x-show="isLoadingTasks" class="text-center text-gray-500 py-4">Mencari
                                            tugas...</div>
                                        <template x-for="task in availableTasks.data" :key="task.id">
                                            <div
                                                class="border dark:border-gray-700 p-4 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                                <div>
                                                    <p class="font-bold text-gray-800 dark:text-gray-100"
                                                        x-text="task.title"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dibuat
                                                        oleh: <span class="font-medium"
                                                            x-text="task.creator.name"></span> | Prioritas: <span
                                                            class="font-medium capitalize"
                                                            x-text="task.priority"></span></p>
                                                </div>
                                                <div class="mt-3 sm:mt-0">
                                                    <x-primary-button @click="claimTask(task.id)"
                                                        ::disabled="isSubmitting"><span
                                                            x-show="!isSubmitting">Ambil</span><span
                                                            x-show="isSubmitting">Memproses...</span></x-primary-button>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="!isLoadingTasks && (!availableTasks.data || availableTasks.data.length === 0)"
                                            class="text-center text-gray-500 py-4">Tidak ada tugas tersedia yang cocok.
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-between items-center"
                                        x-show="availableTasks.total > 0">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">Menampilkan <span
                                                x-text="availableTasks.from || 0"></span>-<span
                                                x-text="availableTasks.to || 0"></span> dari <span
                                                x-text="availableTasks.total || 0"></span></p>
                                        <div class="flex space-x-2">
                                            <x-secondary-button
                                                @click="getDashboardData(availableTasks.current_page - 1)"
                                                ::disabled="!availableTasks.prev_page_url">Sebelumnya
                                            </x-secondary-button>
                                            <x-secondary-button
                                                @click="getDashboardData(availableTasks.current_page + 1)"
                                                ::disabled="!availableTasks.next_page_url">Berikutnya
                                            </x-secondary-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </template>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    {{-- Styling untuk DataTables + Tailwind --}}
    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css"> --}}
    @endpush

    @push('scripts')
    {{-- PERBAIKAN: Load jQuery dari CDN SEBELUM DataTables --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script> --}}

    <script>
        // PERBAIKAN: Semua logika disatukan ke dalam satu komponen Alpine
        function dashboard() {
            return {
                isLoading: true,
                stats: { staff_list: [] },
                filters: { start_date: '', end_date: '' },
                availableTasks: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                search: '',
                isLoadingTasks: false,
                isSubmitting: false,
                tasksTable: null,

                initDashboard() {
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setDate(endDate.getDate() - 30);
                    this.filters.start_date = startDate.toISOString().split('T')[0];
                    this.filters.end_date = endDate.toISOString().split('T')[0];
                    this.getDashboardData();
                },

                getDashboardData(page = 1) {
                    if (page === 1 && this.search === '') this.isLoading = true; else this.isLoadingTasks = true;
                    const params = new URLSearchParams({ page: page, search: this.search, ...this.filters }).toString();
                    axios.get(`{{ route('api.dashboard.stats') }}?${params}`)
                        .then(response => {
                            this.stats = response.data;
                            if (response.data.role_type === 'staff') {
                                this.availableTasks = response.data.available_tasks;
                            }
                            this.$nextTick(() => {
                                if (this.stats.role_type === 'admin') this.createCharts();
                                if (this.stats.role_type === 'leader') this.initializeLeaderPlugins();
                            });
                        })
                        .catch(error => console.error(error))
                        .finally(() => {
                            this.isLoading = false;
                            this.isLoadingTasks = false;
                        });
                },

                createCharts() {
                    if (!this.stats || !this.stats.tasks) return;
                    this.createAssetMovementChart();
                    this.createTaskStatusChart();
                    this.createAssetStatusChart();
                },

                createTaskStatusChart() {
                    const ctx = document.getElementById('taskStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.taskChart instanceof Chart) window.taskChart.destroy();
                    window.taskChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['Baru', 'Dikerjakan', 'Review', 'Selesai'], datasets: [{ data: [this.stats.tasks.unassigned, this.stats.tasks.in_progress, this.stats.tasks.pending_review, this.stats.tasks.completed], backgroundColor: ['#6B7280', '#3B82F6', '#F59E0B', '#10B981'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } } });
                },

                createAssetStatusChart() {
                    const ctx = document.getElementById('assetStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.assetChart instanceof Chart) window.assetChart.destroy();
                    window.assetChart = new Chart(ctx, { type: 'pie', data: { labels: ['Aset Tetap', 'Habis Pakai'], datasets: [{ data: [this.stats.assets.total_fixed, this.stats.assets.total_consumable], backgroundColor: ['#3B82F6', '#10B981'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } } });
                },

                createAssetMovementChart() {
                    const ctx = document.getElementById('assetMovementChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.assetMovementChart instanceof Chart) window.assetMovementChart.destroy();
                    window.assetMovementChart = new Chart(ctx, { type: 'bar', data: { labels: ['Masuk', 'Keluar'], datasets: [{ label: 'Jumlah', data: [this.stats.asset_movement.in, this.stats.asset_movement.out], backgroundColor: ['#10B981', '#EF4444'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
                },

                claimTask(taskId) {
                    this.isSubmitting = true;
                    axios.post(`/api/tasks/${taskId}/claim`)
                        .then(() => { window.location.href = '{{ route("tasks.my_tasks") }}'; })
                        .catch(err => window.iziToast.error({ title: 'Gagal', message: err.response?.data?.message || 'Gagal mengambil tugas.', position: 'topRight' }))
                        .finally(() => this.isSubmitting = false);
                },

                initializeLeaderPlugins() {
                    if (this.tasksTable) {
                        this.tasksTable.ajax.reload();
                        return;
                    }

                    $('#filterStatus, #filterStaff').select2({ theme: "classic", width: '100%' }).on('change', () => this.tasksTable.ajax.reload());

                    const self = this;
                    this.tasksTable = new DataTable('#tasksTable', {
                        processing: true,
                        serverSide: true,
                        ajax: { url: '{{ route('api.dashboard.stats') }}', type: 'GET', data: function(d) { d.status = $('#filterStatus').val(); d.staff_id = $('#filterStaff').val(); } },
                        columns: [
                            { data: 'title', name: 'title', render: (data, type, row) => `<div>${data}</div><div class="text-xs text-gray-500">Jenis: ${row.task_type.name_task}</div>` },
                            { data: 'status', name: 'status', render: (data, type, row) => self.formatStatus(data, row.assignee ? row.assignee.name : null) },
                            { data: 'updated_at', name: 'updated_at', render: (data) => new Date(data).toLocaleDateString('id-ID') },
                            { data: 'id', name: 'id', orderable: false, searchable: false, className: 'text-center', render: (data) => `<a href="/tasks/${data}" class="text-indigo-600 hover:underline">Detail</a>` }
                        ]
                    });
                },

                formatStatus(status, staffName) {
                    const statusMap = {
                        unassigned: '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Dikerjakan</span>',
                        rejected: '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>',
                        in_progress: `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Dikerjakan: ${staffName || ''}</span>`,
                        pending_review: '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Perlu Review</span>',
                        completed: '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>',
                    };
                    return statusMap[status] || status;
                },
            }
        }
    </script>
    @endpush
</x-app-layout>