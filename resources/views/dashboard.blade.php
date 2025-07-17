<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="dashboard()">
                <div x-show="isLoading" class="text-center text-gray-500 py-10">
                    <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="mt-4">Memuat data dashboard...</p>
                </div>

                <div x-show="!isLoading" x-cloak>
                    <template x-if="stats.role_type === 'admin'">
                        <div class="space-y-6">
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
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Menunggu Review</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900"
                                        x-text="stats.tasks.pending_review"></p>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-gray-500 truncate">Total Pengguna</h3>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900" x-text="stats.total_users"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div
                                    class="bg-blue-100 border-l-4 border-blue-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-blue-800 truncate">Total Aset Tetap</h3>
                                    <p class="mt-1 text-3xl font-semibold text-blue-900"
                                        x-text="stats.assets.total_fixed"></p>
                                </div>
                                <div
                                    class="bg-green-100 border-l-4 border-green-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-green-800 truncate">Total Barang Habis Pakai
                                    </h3>
                                    <p class="mt-1 text-3xl font-semibold text-green-900"
                                        x-text="stats.assets.total_consumable"></p>
                                </div>
                                <div
                                    class="bg-yellow-100 border-l-4 border-yellow-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-yellow-800 truncate">Aset Dalam Perbaikan</h3>
                                    <p class="mt-1 text-3xl font-semibold text-yellow-900"
                                        x-text="stats.assets.fixed_in_maintenance"></p>
                                </div>
                                <div
                                    class="bg-red-100 border-l-4 border-red-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-red-800 truncate">Barang Stok Menipis</h3>
                                    <p class="mt-1 text-3xl font-semibold text-red-900"
                                        x-text="stats.assets.consumable_low_stock"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Ringkasan Status Tugas</h3>
                                    <div class="h-64"><canvas id="taskStatusChart"></canvas></div>
                                </div>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Komposisi Aset</h3>
                                    <div class="h-64"><canvas id="assetStatusChart"></canvas></div>
                                </div>
                            </div>

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
                                                            <a :href="report.task_id ? `/tasks/${report.task_id}` : '#'"
                                                                :class="report.task_id ? 'text-indigo-600 hover:text-indigo-900' : 'text-gray-400 cursor-not-allowed'">Lihat
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

                    <template x-if="stats.role_type === 'leader'">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                            <div
                                class="bg-yellow-100 border-l-4 border-yellow-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-yellow-800 truncate">Tugas Menunggu Review</h3>
                                <p class="mt-1 text-3xl font-semibold text-yellow-900"
                                    x-text="stats.tasks_pending_review"></p>
                            </div>
                            <div
                                class="bg-blue-100 border-l-4 border-blue-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-blue-800 truncate">Tugas Dikerjakan Tim</h3>
                                <p class="mt-1 text-3xl font-semibold text-blue-900"
                                    x-text="stats.tasks_in_progress_by_team"></p>
                            </div>
                            <div
                                class="bg-green-100 border-l-4 border-green-500 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-green-800 truncate">Tugas Diselesaikan Tim</h3>
                                <p class="mt-1 text-3xl font-semibold text-green-900"
                                    x-text="stats.tasks_completed_by_team"></p>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Total Tugas Dibuat</h3>
                                <p class="mt-1 text-3xl font-semibold text-gray-900" x-text="stats.tasks_created_total">
                                </p>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-500 truncate">Aset Departemen</h3>
                                <p class="mt-1 text-3xl font-semibold text-gray-900"
                                    x-text="stats.department_assets_count"></p>
                            </div>
                        </div>
                    </template>

                    <template x-if="stats.role_type === 'staff'">
                        <div class="space-y-6">
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

                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Papan Tugas Tersedia</h3>
                                    <div class="mb-4">
                                        <input type="text" x-model.debounce.500ms="search" @input="getDashboardData(1)"
                                            placeholder="Cari berdasarkan judul tugas..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div class="space-y-4">
                                        <template x-if="isLoadingTasks">
                                            <p class="text-center text-gray-500 py-4">Mencari tugas...</p>
                                        </template>
                                        <template x-for="task in availableTasks.data" :key="task.id">
                                            <div class="border p-4 rounded-lg flex justify-between items-center">
                                                <div>
                                                    <p class="font-bold text-gray-800" x-text="task.title"></p>
                                                    <p class="text-sm text-gray-500 mt-1">Dibuat oleh: <span
                                                            class="font-medium" x-text="task.creator.name"></span> |
                                                        Prioritas: <span class="font-medium"
                                                            x-text="task.priority.charAt(0).toUpperCase() + task.priority.slice(1)"></span>
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
                                    <div class="mt-6 flex justify-between items-center"
                                        x-show="availableTasks.total > 0">
                                        <p class="text-sm text-gray-700">Menampilkan <span
                                                x-text="availableTasks.from || 0"></span> sampai <span
                                                x-text="availableTasks.to || 0"></span> dari <span
                                                x-text="availableTasks.total || 0"></span> hasil</p>
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
                isLoadingTasks: true,
                availableTasks: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                search: '',
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },

                init() {
                    this.getDashboardData();
                },
                getDashboardData(page = 1) {
                    if (page === 1 && this.search === '') { this.isLoading = true; }
                    this.isLoadingTasks = true;
                    const params = new URLSearchParams({ page: page, search: this.search }).toString();

                    fetch(`{{ route('api.dashboard.stats') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        this.stats = data;
                        if (data.role_type === 'staff') { this.availableTasks = data.available_tasks; }
                        this.isLoading = false;
                        this.isLoadingTasks = false;
                        this.$nextTick(() => {
                            if (this.stats.role_type === 'admin') {
                                this.createTaskStatusChart();
                                this.createAssetStatusChart(); // Panggil chart aset yang baru
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
                createAssetStatusChart() { // FUNGSI CHART DIPERBARUI
                    const ctx = document.getElementById('assetStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if(this.assetChart) this.assetChart.destroy();
                    this.assetChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Aset Tetap', 'Barang Habis Pakai'],
                            datasets: [{
                                label: 'Komposisi Aset',
                                data: [ this.stats.assets.total_fixed, this.stats.assets.total_consumable ],
                                backgroundColor: [ 'rgba(59, 130, 246, 0.7)', 'rgba(34, 197, 94, 0.7)' ],
                                borderColor: '#fff', borderWidth: 2
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
                    });
                },
                async claimTask(taskId) {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/tasks/${taskId}/claim`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                    })
                    .then(async res => {
                        if (res.ok) { window.location.href = '{{ route('tasks.my_tasks') }}'; }
                        else { const err = await res.json(); throw new Error(err.message || 'Gagal mengambil tugas.'); }
                    })
                    .catch(err => { this.showNotification(err.message, 'error'); })
                    .finally(() => { this.isSubmitting = false; });
                },
                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>