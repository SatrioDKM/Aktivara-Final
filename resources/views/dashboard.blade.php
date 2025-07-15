<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="dashboard()">
                <div x-show="isLoading" class="text-center text-gray-500">Memuat data dashboard...</div>

                <div x-show="!isLoading">
                    <!-- ============================================= -->
                    <!-- TAMPILAN UNTUK ADMIN & MANAGER                 -->
                    <!-- ============================================= -->
                    <template x-if="stats.role_type === 'admin'">
                        <div class="space-y-6">
                            <!-- Kartu Statistik -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Tugas Baru (Unassigned)</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900"
                                        x-text="stats.tasks.unassigned"></p>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Tugas Dikerjakan</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900"
                                        x-text="stats.tasks.in_progress"></p>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Total Aset</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900" x-text="stats.total_assets">
                                    </p>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Total Pengguna</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900" x-text="stats.total_users"></p>
                                </div>
                            </div>
                            <!-- Grafik -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Status Tugas</h3>
                                    <div class="h-64"><canvas id="taskStatusChart"></canvas></div>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Status Aset</h3>
                                    <div class="h-64"><canvas id="assetStatusChart"></canvas></div>
                                </div>
                            </div>
                            <!-- Tabel Laporan Harian Terbaru -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-700">Laporan Harian Terbaru</h3>
                                        <a href="{{ route('export.daily_reports') }}"
                                            class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500">
                                            Export Laporan
                                        </a>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Judul Laporan</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Tugas Terkait</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Dilaporkan Oleh</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Tanggal</th>
                                                    <th scope="col" class="relative px-6 py-3"><span
                                                            class="sr-only">Detail</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="report in stats.latest_reports" :key="report.id">
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                            x-text="report.title"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="report.task ? report.task.title : 'N/A'"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="report.user.name"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="new Date(report.created_at).toLocaleDateString('id-ID')">
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a :href="`/tasks/${report.task_id}`"
                                                                class="text-indigo-600 hover:text-indigo-900">Lihat
                                                                Detail</a>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template
                                                    x-if="!stats.latest_reports || stats.latest_reports.length === 0">
                                                    <tr>
                                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                            Tidak ada laporan terbaru.</td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================= -->
                    <!-- TAMPILAN UNTUK LEADER                          -->
                    <!-- ============================================= -->
                    <template x-if="stats.role_type === 'leader'">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Tugas Menunggu Review</h3>
                                <p class="mt-1 text-3xl font-semibold text-yellow-600"
                                    x-text="stats.tasks_pending_review"></p>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Tugas Dikerjakan Tim</h3>
                                <p class="mt-1 text-3xl font-semibold text-blue-600"
                                    x-text="stats.tasks_in_progress_by_team"></p>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Total Tugas Dibuat</h3>
                                <p class="mt-1 text-3xl font-semibold text-gray-900" x-text="stats.tasks_created_total">
                                </p>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Tugas Diselesaikan Tim</h3>
                                <p class="mt-1 text-3xl font-semibold text-green-600"
                                    x-text="stats.tasks_completed_by_team"></p>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================= -->
                    <!-- TAMPILAN BARU UNTUK STAFF                     -->
                    <!-- ============================================= -->
                    <template x-if="stats.role_type === 'staff'">
                        <div class="space-y-6">
                            <!-- Kartu Statistik Personal -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <a href="{{ route('tasks.my_tasks') }}"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                                    <h3 class="text-sm font-medium text-blue-600 truncate">Tugas Aktif Anda</h3>
                                    <p class="mt-1 text-3xl font-semibold text-blue-600"
                                        x-text="stats.my_active_tasks_count"></p>
                                </a>
                                <a href="{{ route('tasks.completed_history') }}"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-green-50 transition">
                                    <h3 class="text-sm font-medium text-green-600 truncate">Tugas Selesai</h3>
                                    <p class="mt-1 text-3xl font-semibold text-green-600"
                                        x-text="stats.my_completed_tasks_count"></p>
                                </a>
                            </div>

                            <!-- Papan Tugas (Job Board) -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Papan Tugas Tersedia</h3>
                                    <!-- Search Bar -->
                                    <div class="mb-4">
                                        <input type="text" x-model.debounce.500ms="search" @input="getDashboardData(1)"
                                            placeholder="Cari berdasarkan judul tugas..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>

                                    <!-- Daftar Tugas -->
                                    <div class="space-y-4">
                                        <template x-if="isLoadingTasks">
                                            <p class="text-center text-gray-500 py-4">Mencari tugas...</p>
                                        </template>
                                        <template x-for="task in availableTasks.data" :key="task.id">
                                            <div class="border p-4 rounded-lg flex justify-between items-center">
                                                <div>
                                                    <p class="font-bold text-gray-800" x-text="task.title"></p>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        Lokasi: <span
                                                            x-text="task.room ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor}` : 'Tidak spesifik'"></span>
                                                    </p>
                                                </div>
                                                <div>
                                                    <x-primary-button @click="claimTask(task.id)"
                                                        ::disabled="isSubmitting">
                                                        <span x-show="!isSubmitting">Ambil</span>
                                                        <span x-show="isSubmitting">Memproses...</span>
                                                    </x-primary-button>
                                                </div>
                                            </div>
                                        </template>
                                        <template
                                            x-if="!isLoadingTasks && (!availableTasks.data || availableTasks.data.length === 0)">
                                            <p class="text-center text-gray-500 py-4">Tidak ada tugas yang cocok dengan
                                                pencarian Anda.</p>
                                        </template>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="mt-6 flex justify-between items-center"
                                        x-show="availableTasks.total > 0">
                                        <p class="text-sm text-gray-700">
                                            Menampilkan <span x-text="availableTasks.from || 0"></span> sampai <span
                                                x-text="availableTasks.to || 0"></span> dari <span
                                                x-text="availableTasks.total || 0"></span> hasil
                                        </p>
                                        <div class="flex space-x-2">
                                            <button @click="getDashboardData(availableTasks.current_page - 1)"
                                                :disabled="!availableTasks.prev_page_url"
                                                class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Sebelumnya</button>
                                            <button @click="getDashboardData(availableTasks.current_page + 1)"
                                                :disabled="!availableTasks.next_page_url"
                                                class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Berikutnya</button>
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

    <script>
        function dashboard() {
            return {
                isLoading: true,
                stats: {},
                // State baru untuk Papan Tugas Staff
                isLoadingTasks: true,
                availableTasks: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                search: '',
                isSubmitting: false, // State untuk tombol claim
                notification: { show: false, message: '', type: 'success' },

                init() {
                    this.getDashboardData();
                },

                getDashboardData(page = 1) {
                    // Hanya set loading utama saat pertama kali
                    if (page === 1 && this.search === '') {
                        this.isLoading = true;
                    }
                    this.isLoadingTasks = true;

                    const params = new URLSearchParams({
                        page: page,
                        search: this.search
                    }).toString();

                    fetch(`{{ route('api.dashboard.stats') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            this.stats = data;
                            if (data.role_type === 'staff') {
                                this.availableTasks = data.available_tasks;
                            }
                            this.isLoading = false;
                            this.isLoadingTasks = false;

                            this.$nextTick(() => {
                                if (this.stats.role_type === 'admin') {
                                    this.createTaskStatusChart();
                                    this.createAssetStatusChart();
                                }
                            });
                        });
                },

                createTaskStatusChart() {
                    const ctx = document.getElementById('taskStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if(this.taskChart) this.taskChart.destroy();
                    this.taskChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Unassigned', 'In Progress', 'Pending Review', 'Completed'],
                            datasets: [{
                                label: 'Status Tugas',
                                data: [ this.stats.tasks.unassigned, this.stats.tasks.in_progress, this.stats.tasks.pending_review, this.stats.tasks.completed ],
                                backgroundColor: [ 'rgba(156, 163, 175, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(34, 197, 94, 0.7)' ],
                                borderColor: '#fff', borderWidth: 2
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
                    });
                },

                createAssetStatusChart() {
                    const ctx = document.getElementById('assetStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if(this.assetChart) this.assetChart.destroy();
                    this.assetChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Available', 'In Use', 'Maintenance', 'Disposed'],
                            datasets: [{
                                label: 'Status Aset',
                                data: [ this.stats.assets.available, this.stats.assets.in_use, this.stats.assets.maintenance, this.stats.assets.disposed ],
                                backgroundColor: [ 'rgba(34, 197, 94, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(107, 114, 128, 0.7)' ],
                                borderColor: '#fff', borderWidth: 2
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
                    });
                },

                // --- FUNGSI BARU UNTUK KLAIM TUGAS ---
                async claimTask(taskId) {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/tasks/${taskId}/claim`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                    })
                    .then(async res => {
                        if (res.ok) {
                            window.location.href = '{{ route('tasks.my_tasks') }}';
                        } else {
                            const err = await res.json();
                            throw new Error(err.message || 'Gagal mengambil tugas.');
                        }
                    })
                    .catch(err => {
                        this.showNotification(err.message, 'error');
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) return decodeURIComponent(csrfCookie.split('=')[1]);
                    return '';
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                },
            }
        }
    </script>
</x-app-layout>