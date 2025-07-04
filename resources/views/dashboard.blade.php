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
                    <!-- TAMPILAN UNTUK STAFF                           -->
                    <!-- ============================================= -->
                    <template x-if="stats.role_type === 'staff'">
                        <div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                                <h3 class="text-2xl font-semibold text-gray-900">Selamat datang, {{ Auth::user()->name
                                    }}!</h3>
                                <p class="mt-1 text-gray-600">Berikut ringkasan pekerjaan Anda.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <a href="{{ route('tasks.available') }}"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-indigo-50 transition">
                                    <h3 class="text-sm font-medium text-indigo-600 truncate">Tugas Tersedia Untuk Anda
                                    </h3>
                                    <p class="mt-1 text-3xl font-semibold text-indigo-600"
                                        x-text="stats.available_tasks_count"></p>
                                </a>
                                <a href="{{ route('tasks.my_tasks') }}"
                                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                                    <h3 class="text-sm font-medium text-blue-600 truncate">Tugas Aktif Anda</h3>
                                    <p class="mt-1 text-3xl font-semibold text-blue-600"
                                        x-text="stats.my_active_tasks_count"></p>
                                </a>
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                    <h3 class="text-sm font-medium text-green-600 truncate">Tugas Selesai</h3>
                                    <p class="mt-1 text-3xl font-semibold text-green-600"
                                        x-text="stats.my_completed_tasks_count"></p>
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
                taskChart: null,
                assetChart: null,

                init() {
                    fetch('{{ route('api.dashboard.stats') }}', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            this.stats = data;
                            this.isLoading = false;

                            // --- PERBAIKAN DI SINI ---
                            // Tunggu hingga DOM diperbarui oleh Alpine, baru buat chart.
                            this.$nextTick(() => {
                                if (this.stats.role_type === 'admin') {
                                    this.createTaskStatusChart();
                                    this.createAssetStatusChart();
                                }
                            });
                        });
                },

                createTaskStatusChart() {
                    const ctx = document.getElementById('taskStatusChart').getContext('2d');
                    if(this.taskChart) this.taskChart.destroy();
                    this.taskChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Unassigned', 'In Progress', 'Pending Review', 'Completed'],
                            datasets: [{
                                label: 'Status Tugas',
                                data: [
                                    this.stats.tasks.unassigned,
                                    this.stats.tasks.in_progress,
                                    this.stats.tasks.pending_review,
                                    this.stats.tasks.completed
                                ],
                                backgroundColor: [
                                    'rgba(156, 163, 175, 0.7)', // gray
                                    'rgba(59, 130, 246, 0.7)', // blue
                                    'rgba(245, 158, 11, 0.7)', // amber
                                    'rgba(34, 197, 94, 0.7)'  // green
                                ],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });
                },

                createAssetStatusChart() {
                    const ctx = document.getElementById('assetStatusChart').getContext('2d');
                    if(this.assetChart) this.assetChart.destroy();
                    this.assetChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Available', 'In Use', 'Maintenance', 'Disposed'],
                            datasets: [{
                                label: 'Status Aset',
                                data: [
                                    this.stats.assets.available,
                                    this.stats.assets.in_use,
                                    this.stats.assets.maintenance,
                                    this.stats.assets.disposed
                                ],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.7)',  // green
                                    'rgba(59, 130, 246, 0.7)', // blue
                                    'rgba(245, 158, 11, 0.7)', // amber
                                    'rgba(107, 114, 128, 0.7)' // gray
                                ],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>