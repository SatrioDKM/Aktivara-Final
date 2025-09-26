<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="dashboard()" x-cloak>
                {{-- Indikator Loading Utama --}}
                <div x-show="isLoading" class="text-center text-gray-500 py-10">
                    <i class="fas fa-spinner fa-spin fa-3x text-gray-400"></i>
                    <p class="mt-4">Memuat data dashboard...</p>
                </div>

                {{-- Konten Dashboard Setelah Loading --}}
                <div x-show="!isLoading">
                    {{-- ======================== TAMPILAN ADMIN & MANAGER ======================== --}}
                    <template x-if="stats.role_type === 'admin'">
                        <div class="space-y-6">
                            {{-- Card Statistik Tugas --}}
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
                                {{-- Filter Tanggal --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                            Mulai</label>
                                        <input type="date" x-model="filters.start_date"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                            Akhir</label>
                                        <input type="date" x-model="filters.end_date"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    </div>
                                    <x-primary-button @click="getDashboardData()"
                                        class="w-full sm:w-auto justify-center">
                                        <i class="fas fa-filter me-2"></i> Filter
                                    </x-primary-button>
                                </div>

                                {{-- Canvas untuk Chart --}}
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div class="lg:col-span-1 p-4 border rounded-lg dark:border-gray-700">
                                        <h4 class="font-semibold text-center mb-2 dark:text-gray-300">Pergerakan Barang
                                        </h4>
                                        <div class="h-64"><canvas id="assetMovementChart"></canvas></div>
                                    </div>
                                    <div class="lg:col-span-1 p-4 border rounded-lg dark:border-gray-700">
                                        <h4 class="font-semibold text-center mb-2 dark:text-gray-300">Status Tugas</h4>
                                        <div class="h-64"><canvas id="taskStatusChart"></canvas></div>
                                    </div>
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
                        <div x-data="leaderDashboard" x-init="initLeader({ staffList: stats.staff_list })">
                            {{-- Tombol Aksi --}}
                            <div class="flex justify-end mb-4">
                                <x-primary-button @click="openCreateModal()">
                                    <i class="fas fa-plus me-2"></i> Buat Tugas Baru
                                </x-primary-button>
                            </div>

                            {{-- Card Filter --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg mb-6 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                                    <div><label class="block text-sm dark:text-gray-300">Status</label><select
                                            id="filterStatus" class="mt-1 block w-full rounded-md">
                                            <option value="">Semua Status</option>
                                            <option value="unassigned">Belum Dikerjakan</option>
                                            <option value="dikerjakan">Sudah Dikerjakan</option>
                                            <option value="pending_review">Perlu Review</option>
                                            <option value="completed">Selesai</option>
                                        </select></div>
                                    <div><label class="block text-sm dark:text-gray-300">Staff</label><select
                                            id="filterStaff" class="mt-1 block w-full rounded-md">
                                            <option value="">Semua Staff</option><template x-for="staff in staffList"
                                                :key="staff.id">
                                                <option :value="staff.id" x-text="staff.name"></option>
                                            </template>
                                        </select></div>
                                </div>
                            </div>

                            {{-- Tabel DataTables --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                                <table id="tasksTable" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Tugas</th>
                                            <th>Status</th>
                                            <th>Tgl. Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            {{-- Modal Buat Tugas Baru --}}
                            <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                                <div class="flex items-center justify-center min-h-screen">
                                    <div @click="showCreateModal = false"
                                        class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                                        <form @submit.prevent="saveTask()">
                                            <h3 class="text-lg font-medium mb-4 dark:text-gray-100">Buat Tugas Baru</h3>
                                            <div class="space-y-4">
                                                <div><label class="block text-sm dark:text-gray-300">Judul
                                                        Tugas</label><input type="text" x-model="formData.title"
                                                        class="mt-1 w-full rounded-md dark:bg-gray-900 dark:border-gray-700"
                                                        required></div>
                                                <div><label class="block text-sm dark:text-gray-300">Jenis
                                                        Tugas</label><select id="createTaskType"
                                                        class="mt-1 w-full rounded-md" required></select></div>
                                                <div><label
                                                        class="block text-sm dark:text-gray-300">Prioritas</label><select
                                                        id="createPriority" class="mt-1 w-full rounded-md"
                                                        required></select></div>
                                                <div><label
                                                        class="block text-sm dark:text-gray-300">Deskripsi</label><textarea
                                                        x-model="formData.description" rows="3"
                                                        class="mt-1 w-full rounded-md dark:bg-gray-900 dark:border-gray-700"></textarea>
                                                </div>
                                            </div>
                                            <div class="mt-6 flex justify-end space-x-3">
                                                <x-secondary-button type="button" @click="showCreateModal = false">Batal
                                                </x-secondary-button>
                                                <x-primary-button type="submit" ::disabled="isSubmitting"><span
                                                        x-show="!isSubmitting">Simpan</span><span
                                                        x-show="isSubmitting">Menyimpan...</span></x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ======================== TAMPILAN STAFF ======================== --}}
                    <template x-if="stats.role_type === 'staff'">
                        <div class="space-y-6">
                            {{-- Card Statistik --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <a href="{{ route('tasks.my_history') }}"
                                    class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition flex items-center">
                                    <div class="bg-blue-100 rounded-full p-3 me-4"><i
                                            class="fas fa-tasks fa-lg text-blue-500"></i></div>
                                    <div>
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300 truncate">Tugas
                                            Aktif Anda</h3>
                                        <p class="mt-1 text-3xl font-semibold text-blue-600 dark:text-blue-400"
                                            x-text="stats.my_active_tasks_count"></p>
                                    </div>
                                </a>
                                <a href="{{ route('tasks.my_history') }}?status=completed"
                                    class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-green-50 dark:hover:bg-gray-700/50 transition flex items-center">
                                    <div class="bg-green-100 rounded-full p-3 me-4"><i
                                            class="fas fa-check-circle fa-lg text-green-500"></i></div>
                                    <div>
                                        <h3 class="text-sm font-medium text-green-800 dark:text-green-300 truncate">
                                            Tugas Selesai</h3>
                                        <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400"
                                            x-text="stats.my_completed_tasks_count"></p>
                                    </div>
                                </a>
                            </div>

                            {{-- Papan Tugas --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Papan Tugas
                                        Tersedia</h3>
                                    <div class="mb-4 relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i></div>
                                        <input type="text" x-model.debounce.500ms="search" @input="getDashboardData(1)"
                                            placeholder="Cari berdasarkan judul tugas..."
                                            class="block w-full rounded-md border-gray-300 ps-10 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
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
                                    {{-- Paginasi --}}
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
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    {{-- iziToast, DataTables, Select2 CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcP5nltDl3W+PPTaCiadIYMA2iNT1ebLVR6NoucF5bnraIovFdFnDGeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    {{-- iziToast, DataTables, Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0A7atXbqZQlXMVFD+iTNaxwIgajBJI8bXgG2bgweoWocZaOKimEi2o27aZhdGEOQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Logika Utama Dashboard
        function dashboard() {
            return {
                isLoading: true,
                stats: {},
                filters: {
                    start_date: '',
                    end_date: ''
                },
                availableTasks: {
                    data: [],
                    from: 0,
                    to: 0,
                    total: 0,
                    current_page: 1,
                    prev_page_url: null,
                    next_page_url: null
                },
                search: '',
                isSubmitting: false,
                init() {
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setDate(endDate.getDate() - 30);
                    this.filters.start_date = startDate.toISOString().split('T')[0];
                    this.filters.end_date = endDate.toISOString().split('T')[0];
                    this.getDashboardData();
                },
                getDashboardData(page = 1) {
                    this.isLoading = (page === 1 && this.search === '');
                    const params = new URLSearchParams({
                        page: page,
                        search: this.search,
                        ...this.filters
                    }).toString();
                    fetch(`{{ route('api.dashboard.stats') }}?${params}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.stats = data;
                            if (data.role_type === 'staff') {
                                this.availableTasks = data.available_tasks;
                            }
                            this.isLoading = false;
                            this.$nextTick(() => {
                                if (this.stats.role_type === 'admin') {
                                    this.createCharts();
                                }
                            });
                        });
                },
                createCharts() {
                    this.createAssetMovementChart();
                    this.createTaskStatusChart();
                    this.createAssetStatusChart();
                },
                createTaskStatusChart() {
                    const ctx = document.getElementById('taskStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.taskChart instanceof Chart) window.taskChart.destroy();
                    window.taskChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Baru', 'Dikerjakan', 'Review', 'Selesai'],
                            datasets: [{
                                data: [this.stats.tasks.unassigned, this.stats.tasks.in_progress, this.stats.tasks.pending_review, this.stats.tasks.completed],
                                backgroundColor: ['#6B7280', '#3B82F6', '#F59E0B', '#10B981']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                },
                createAssetStatusChart() {
                    const ctx = document.getElementById('assetStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.assetChart instanceof Chart) window.assetChart.destroy();
                    window.assetChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Aset Tetap', 'Habis Pakai'],
                            datasets: [{
                                data: [this.stats.assets.total_fixed, this.stats.assets.total_consumable],
                                backgroundColor: ['#3B82F6', '#10B981']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                },
                createAssetMovementChart() {
                    const ctx = document.getElementById('assetMovementChart')?.getContext('2d');
                    if (!ctx) return;
                    if (window.assetMovementChart instanceof Chart) window.assetMovementChart.destroy();
                    window.assetMovementChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Masuk', 'Keluar'],
                            datasets: [{
                                label: 'Jumlah',
                                data: [this.stats.asset_movement.in, this.stats.asset_movement.out],
                                backgroundColor: ['#10B981', '#EF4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                },
                async claimTask(taskId) {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/tasks/${taskId}/claim`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-XSRF-TOKEN': this.getCsrfToken()
                            }
                        })
                        .then(async res => res.ok ? res.json() : Promise.reject(await res.json()))
                        .then(() => {
                            window.location.href = '{{ route('tasks.my_history') }}';
                        })
                        .catch(err => {
                            iziToast.error({ title: 'Gagal', message: err.message || 'Gagal mengambil tugas.', position: 'topRight' });
                        })
                        .finally(() => {
                            this.isSubmitting = false;
                        });
                },
                getCsrfToken() {
                    const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN='));
                    return c ? decodeURIComponent(c.split('=')[1]) : '';
                },
            }
        }

        // Logika untuk Dashboard Leader
        const leaderDashboard = {
            staffList: [],
            tasksTable: null,
            showCreateModal: false,
            isSubmitting: false,
            formData: {},
            initLeader(data) {
                this.staffList = data.staffList;
                this.$nextTick(() => {
                    this.initializePlugins();
                    this.initializeDataTable();
                });
            },
            initializePlugins() {
                const self = this;
                $('#filterStatus, #filterStaff').select2({
                    theme: "classic",
                    width: '100%'
                });
                $('#filterStatus').on('change', () => this.tasksTable.draw());
                $('#filterStaff').on('change', () => this.tasksTable.draw());

                // Inisialisasi Select2 untuk modal
                $('#createTaskType').select2({
                    theme: "classic",
                    width: '100%',
                    dropdownParent: $('#createTaskType').parent(),
                    placeholder: 'Pilih jenis tugas',
                    ajax: {
                        url: '{{ route('api.task-types.by-department', substr(Auth::user()->role_id, 0, 2)) }}',
                        dataType: 'json',
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name_task
                                    }
                                })
                            };
                        }
                    }
                });
                $('#createPriority').select2({
                    theme: "classic",
                    width: '100%',
                    dropdownParent: $('#createPriority').parent()
                });
            },
            initializeDataTable() {
                this.tasksTable = $('#tasksTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('api.dashboard.stats') }}',
                        type: 'GET',
                        data: function(d) {
                            d.status = $('#filterStatus').val();
                            d.staff_id = $('#filterStaff').val();
                        }
                    },
                    columns: [{
                        data: 'title',
                        name: 'title',
                        render: (data, type, row) => `<div>${data}</div><div class="text-xs text-gray-500">Jenis: ${row.task_type.name_task}</div>`
                    }, {
                        data: 'status',
                        name: 'status',
                        render: (data, type, row) => this.formatStatus(data, row.staff ? row.staff.name : null)
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        render: (data) => new Date(data).toLocaleDateString('id-ID')
                    }, {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: (data) => `<a href="/tasks/${data}" class="text-indigo-600 hover:underline">Detail</a>`
                    }]
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
            openCreateModal() {
                this.formData = { title: '', task_type_id: '', priority: 'medium', description: '' };
                $('#createTaskType').val(null).trigger('change');
                $('#createPriority').val('medium').trigger('change');
                this.showCreateModal = true;
            },
            async saveTask() {
                this.formData.task_type_id = $('#createTaskType').val();
                this.formData.priority = $('#createPriority').val();
                this.isSubmitting = true;
                await fetch('/sanctum/csrf-cookie');
                fetch('{{ route('api.tasks.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                    .then(data => {
                        this.showCreateModal = false;
                        this.tasksTable.draw();
                        iziToast.success({
                            title: 'Berhasil',
                            message: data.message,
                            position: 'topRight'
                        });
                    })
                    .catch(() => iziToast.error({
                        title: 'Gagal',
                        message: 'Gagal membuat tugas.',
                        position: 'topRight'
                    }))
                    .finally(() => this.isSubmitting = false);
            },
            getCsrfToken() {
                const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN='));
                return c ? decodeURIComponent(c.split('=')[1]) : '';
            },
        };
    </script>
    @endpush
</x-app-layout>