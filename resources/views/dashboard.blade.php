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

                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Analitik Aset</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                        <input type="date" x-model="filters.start_date"
                                            class="mt-1 block w-full rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                                        <input type="date" x-model="filters.end_date"
                                            class="mt-1 block w-full rounded-md">
                                    </div>
                                    <button @click="getDashboardData()"
                                        class="w-full sm:w-auto inline-flex justify-center py-2 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Filter
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div class="lg:col-span-1">
                                        <h4 class="font-semibold text-center mb-2">Pergerakan Barang</h4>
                                        <div class="h-64"><canvas id="assetMovementChart"></canvas></div>
                                    </div>
                                    <div class="lg:col-span-1">
                                        <h4 class="font-semibold text-center mb-2">Status Tugas</h4>
                                        <div class="h-64"><canvas id="taskStatusChart"></canvas></div>
                                    </div>
                                    <div class="lg:col-span-1">
                                        <h4 class="font-semibold text-center mb-2">Komposisi Aset</h4>
                                        <div class="h-64"><canvas id="assetStatusChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="stats.role_type === 'leader'">
                        <div x-data="leaderDashboard({
                            initialTasks: stats.tasks,
                            staffList: stats.staff_list,
                            taskTypes: {{ Js::from(App\Models\TaskType::where('departemen', substr(Auth::user()->role_id, 0, 2))->orWhere('departemen', 'UMUM')->get()) }}
                        })">
                            <div class="flex justify-end mb-4"><button @click="openCreateModal()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">Buat
                                    Tugas Baru</button></div>
                            <div x-show="notification.show" x-transition
                                class="fixed top-20 right-5 z-50 p-4 rounded-lg shadow-lg"
                                :class="notification.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                <span x-text="notification.message"></span></div>

                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                                    <div><label class="block text-sm">Dari Tanggal</label><input type="date"
                                            x-model="filters.start_date" class="mt-1 block w-full rounded-md"></div>
                                    <div><label class="block text-sm">Sampai Tanggal</label><input type="date"
                                            x-model="filters.end_date" class="mt-1 block w-full rounded-md"></div>
                                    <div><label class="block text-sm">Status</label><select x-model="filters.status"
                                            class="mt-1 block w-full rounded-md">
                                            <option value="">Semua Status</option>
                                            <option value="unassigned">Belum Dikerjakan</option>
                                            <option value="dikerjakan">Sudah Dikerjakan</option>
                                            <option value="pending_review">Perlu Review</option>
                                            <option value="completed">Selesai</option>
                                        </select></div>
                                    <div><label class="block text-sm">Staff</label><select x-model="filters.staff_id"
                                            class="mt-1 block w-full rounded-md">
                                            <option value="">Semua Staff</option><template x-for="staff in staffList"
                                                :key="staff.id">
                                                <option :value="staff.id" x-text="staff.name"></option>
                                            </template>
                                        </select></div>
                                    <div><button @click="applyFilters"
                                            class="w-full py-2 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
                                    </div>
                                </div>
                                <div class="mt-4"><input type="text" x-model.debounce.500ms="filters.search"
                                        @input="applyFilters" placeholder="Cari judul tugas..."
                                        class="block w-full rounded-md"></div>
                            </div>

                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tugas</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tgl.
                                                    Update</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium uppercase">Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-if="isLoadingTasks">
                                                <tr>
                                                    <td colspan="4" class="py-4 text-center">Memuat tugas...</td>
                                                </tr>
                                            </template>
                                            <template x-for="task in tasks.data" :key="task.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 text-sm font-medium">
                                                        <div x-text="task.title"></div>
                                                        <div class="text-xs text-gray-500"
                                                            x-text="`Jenis: ${task.task_type.name_task}`"></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm"
                                                        x-html="formatStatus(task.status, task.staff ? task.staff.name : null)">
                                                    </td>
                                                    <td class="px-6 py-4 text-sm"
                                                        x-text="new Date(task.updated_at).toLocaleDateString('id-ID')">
                                                    </td>
                                                    <td class="px-6 py-4 text-center"><a :href="`/tasks/${task.id}`"
                                                            class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template
                                                x-if="!isLoadingTasks && (!tasks.data || tasks.data.length === 0)">
                                                <tr>
                                                    <td colspan="4" class="py-4 text-center">Tidak ada data.</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-6 flex justify-between items-center" x-show="tasks.total > 0">
                                    <p class="text-sm">Menampilkan <span x-text="tasks.from || 0"></span>-<span
                                            x-text="tasks.to || 0"></span> dari <span x-text="tasks.total || 0"></span>
                                    </p>
                                    <div><button @click="fetchTasks(tasks.current_page - 1)"
                                            :disabled="!tasks.prev_page_url"
                                            class="px-3 py-1 rounded-md disabled:opacity-50">Sebelumnya</button><button
                                            @click="fetchTasks(tasks.current_page + 1)" :disabled="!tasks.next_page_url"
                                            class="px-3 py-1 rounded-md disabled:opacity-50">Berikutnya</button></div>
                                </div>
                            </div>

                            <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                                <div class="flex items-center justify-center min-h-screen">
                                    <div @click="showCreateModal = false"
                                        class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                                    <div
                                        class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                                        <form @submit.prevent="saveTask()">
                                            <h3 class="text-lg font-medium mb-4">Buat Tugas Baru</h3>
                                            <div class="space-y-4">
                                                <div><label class="block text-sm">Judul Tugas</label><input type="text"
                                                        x-model="formData.title" class="mt-1 w-full rounded-md"
                                                        required></div>
                                                <div><label class="block text-sm">Jenis Tugas</label><select
                                                        x-model="formData.task_type_id" class="mt-1 w-full rounded-md"
                                                        required>
                                                        <option value="">-- Pilih Jenis --</option><template
                                                            x-for="tt in taskTypes" :key="tt.id">
                                                            <option :value="tt.id" x-text="tt.name_task"></option>
                                                        </template>
                                                    </select></div>
                                                <div><label class="block text-sm">Prioritas</label><select
                                                        x-model="formData.priority" class="mt-1 w-full rounded-md"
                                                        required>
                                                        <option value="low">Rendah</option>
                                                        <option value="medium">Sedang</option>
                                                        <option value="high">Tinggi</option>
                                                        <option value="critical">Kritis</option>
                                                    </select></div>
                                                <div><label class="block text-sm">Deskripsi</label><textarea
                                                        x-model="formData.description" rows="3"
                                                        class="mt-1 w-full rounded-md"></textarea></div>
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

                    <template x-if="stats.role_type === 'staff'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6"><a href="{{ route('tasks.my_history') }}"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                                    <h3 class="text-sm font-medium text-blue-600 truncate">Tugas Aktif Anda</h3>
                                    <p class="mt-1 text-3xl font-semibold text-blue-600"
                                        x-text="stats.my_active_tasks_count"></p>
                                </a><a href="{{ route('tasks.my_history') }}?status=completed"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-green-50 transition">
                                    <h3 class="text-sm font-medium text-green-600 truncate">Tugas Selesai</h3>
                                    <p class="mt-1 text-3xl font-semibold text-green-600"
                                        x-text="stats.my_completed_tasks_count"></p>
                                </a></div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Papan Tugas Tersedia</h3>
                                    <div class="mb-4"><input type="text" x-model.debounce.500ms="search"
                                            @input="getDashboardData(1)" placeholder="Cari berdasarkan judul tugas..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm"></div>
                                    <div class="space-y-4"><template x-if="isLoadingTasks">
                                            <p class="text-center text-gray-500 py-4">Mencari tugas...</p>
                                        </template><template x-for="task in availableTasks.data" :key="task.id">
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
                                                        ::disabled="isSubmitting"><span
                                                            x-show="!isSubmitting">Ambil</span><span
                                                            x-show="isSubmitting">Memproses...</span></x-primary-button>
                                                </div>
                                            </div>
                                        </template><template
                                            x-if="!isLoadingTasks && (!availableTasks.data || availableTasks.data.length === 0)">
                                            <p class="text-center text-gray-500 py-4">Tidak ada tugas yang cocok dengan
                                                pencarian Anda.</p>
                                        </template></div>
                                    <div class="mt-6 flex justify-between items-center"
                                        x-show="availableTasks.total > 0">
                                        <p class="text-sm text-gray-700">Menampilkan <span
                                                x-text="availableTasks.from || 0"></span> sampai <span
                                                x-text="availableTasks.to || 0"></span> dari <span
                                                x-text="availableTasks.total || 0"></span> hasil</p>
                                        <div class="flex space-x-2"><button
                                                @click="getDashboardData(availableTasks.current_page - 1)"
                                                :disabled="!availableTasks.prev_page_url"
                                                class="px-3 py-1 text-sm rounded-md bg-gray-200 disabled:opacity-50">Sebelumnya</button><button
                                                @click="getDashboardData(availableTasks.current_page + 1)"
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
                isLoading: true, stats: {},
                filters: { start_date: '', end_date: '' },
                isLoadingTasks: true,
                availableTasks: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null },
                search: '', isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },

                init() {
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setDate(endDate.getDate() - 30);
                    this.filters.start_date = startDate.toISOString().split('T')[0];
                    this.filters.end_date = endDate.toISOString().split('T')[0];
                    this.getDashboardData();
                },

                getDashboardData(page = 1) {
                    if (page === 1 && this.search === '') { this.isLoading = true; }
                    this.isLoadingTasks = true;
                    const params = new URLSearchParams({ page: page, search: this.search, ...this.filters }).toString();
                    fetch(`{{ route('api.dashboard.stats') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        this.stats = data;
                        if (data.role_type === 'staff') { this.availableTasks = data.available_tasks; }
                        this.isLoading = false; this.isLoadingTasks = false;
                        this.$nextTick(() => {
                            if (this.stats.role_type === 'admin') {
                                this.createTaskStatusChart();
                                this.createAssetStatusChart();
                                this.createAssetMovementChart();
                            }
                        });
                    });
                },

                createTaskStatusChart() {
                    const ctx = document.getElementById('taskStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if(window.taskChart instanceof Chart) { window.taskChart.destroy(); }
                    window.taskChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['Belum Dikerjakan', 'Dikerjakan', 'Perlu Review', 'Selesai'], datasets: [{ label: 'Status Tugas', data: [ this.stats.tasks.unassigned, this.stats.tasks.in_progress, this.stats.tasks.pending_review, this.stats.tasks.completed ], backgroundColor: [ 'rgba(156, 163, 175, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(34, 197, 94, 0.7)' ], borderColor: '#fff', borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } } });
                },

                createAssetStatusChart() {
                    const ctx = document.getElementById('assetStatusChart')?.getContext('2d');
                    if (!ctx) return;
                    if(window.assetChart instanceof Chart) { window.assetChart.destroy(); }
                    window.assetChart = new Chart(ctx, { type: 'pie', data: { labels: ['Aset Tetap', 'Barang Habis Pakai'], datasets: [{ label: 'Komposisi Aset', data: [ this.stats.assets.total_fixed, this.stats.assets.total_consumable ], backgroundColor: [ 'rgba(59, 130, 246, 0.7)', 'rgba(34, 197, 94, 0.7)' ], borderColor: '#fff', borderWidth: 2 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } } });
                },

                createAssetMovementChart() {
                    const ctx = document.getElementById('assetMovementChart')?.getContext('2d');
                    if (!ctx) return;
                    if(window.assetMovementChart instanceof Chart) { window.assetMovementChart.destroy(); }
                    window.assetMovementChart = new Chart(ctx, { type: 'bar', data: { labels: ['Barang Masuk', 'Barang Keluar'], datasets: [{ label: 'Jumlah Unit', data: [ this.stats.asset_movement.in, this.stats.asset_movement.out ], backgroundColor: [ 'rgba(34, 197, 94, 0.7)', 'rgba(239, 68, 68, 0.7)' ], borderColor: [ 'rgba(34, 197, 94, 1)', 'rgba(239, 68, 68, 1)' ], borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
                },

                async claimTask(taskId) {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/tasks/${taskId}/claim`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() } })
                    .then(async res => res.ok ? res.json() : Promise.reject(await res.json()))
                    .then(() => { window.location.href = '{{ route('tasks.my_history') }}'; })
                    .catch(err => { this.showNotification(err.message || 'Gagal.', 'error'); })
                    .finally(() => { this.isSubmitting = false; });
                },
                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }

        function leaderDashboard(data) {
            return {
                tasks: data.initialTasks, staffList: data.staffList, taskTypes: data.taskTypes,
                isLoadingTasks: false,
                filters: { start_date: '', end_date: '', status: '', staff_id: '', search: '' },
                showCreateModal: false, isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },
                formData: {},
                init() { this.resetForm(); },
                resetForm() { this.formData = { title: '', task_type_id: '', priority: 'medium', description: '' }; },
                applyFilters() { this.fetchTasks(1); },
                fetchTasks(page) {
                    if (page < 1) return;
                    this.isLoadingTasks = true;
                    const params = new URLSearchParams({ page: page, ...this.filters }).toString();
                    fetch(`{{ route('api.dashboard.stats') }}?${params}`, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json()).then(data => { this.tasks = data.tasks; })
                        .finally(() => this.isLoadingTasks = false);
                },
                formatStatus(status, staffName) {
                    const statusMap = {
                        unassigned: '<span class="text-gray-600">Belum Dikerjakan</span>',
                        rejected: '<span class="text-red-600">Belum Dikerjakan (Ditolak)</span>',
                        in_progress: `<span class="text-blue-600">Dikerjakan oleh <strong>${staffName || ''}</strong></span>`,
                        pending_review: '<span class="text-yellow-600">Perlu Review</span>',
                        completed: '<span class="text-green-600">Selesai</span>',
                    };
                    return statusMap[status] || status;
                },
                openCreateModal() { this.resetForm(); this.showCreateModal = true; },
                async saveTask() {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch('{{ route('api.tasks.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                    .then(data => {
                        this.showNotification(data.message, 'success');
                        this.showCreateModal = false;
                        this.applyFilters();
                    })
                    .catch(() => this.showNotification('Gagal membuat tugas.', 'error'))
                    .finally(() => this.isSubmitting = false);
                },
                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>