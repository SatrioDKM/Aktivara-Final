<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Papan Tugas Tersedia') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="availableTasks()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-4">
                <template x-if="isLoading">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat tugas...</p>
                    </div>
                </template>
                <template x-if="!isLoading && tasks.length === 0">
                    <div class="bg-white dark:bg-gray-800 text-center p-12 shadow-sm rounded-lg">
                        <i class="fas fa-check-circle fa-3x text-green-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Semua Tugas Selesai!</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Tidak ada tugas yang tersedia untuk departemen
                            Anda saat ini.</p>
                    </div>
                </template>
                <template x-for="task in tasks" :key="task.id">
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row justify-between md:items-center">
                        <div>
                            <p class="font-bold text-lg text-gray-800 dark:text-gray-100" x-text="task.title"></p>
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span>Dibuat oleh: <strong class="text-gray-700 dark:text-gray-200"
                                        x-text="task.creator.name"></strong></span>
                                <span>Prioritas: <strong class="capitalize text-gray-700 dark:text-gray-200"
                                        x-text="task.priority"></strong></span>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center space-x-4">
                            <a :href="`/tasks/${task.id}`"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Lihat Detail</a>
                            <x-primary-button @click="claimTask(task.id)" ::disabled="isClaiming === task.id">
                                <span x-show="isClaiming !== task.id" class="inline-flex items-center">
                                    <i class="fas fa-hand-paper me-2"></i>
                                    Ambil Tugas
                                </span>
                                <span x-show="isClaiming === task.id">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Memproses...
                                </span>
                            </x-primary-button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex justify-end">
                <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                    <template x-for="link in pagination.links">
                        <button @click="changePage(link.url)" :disabled="!link.url"
                            :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                            class="px-3 py-2 rounded-md text-sm font-medium transition" x-html="link.label"></button>
                    </template>
                </nav>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        function availableTasks() {
                return {
                    tasks: [],
                    pagination: {},
                    isLoading: true,
                    isClaiming: null,

                    init() {
                        this.fetchTasks();
                    },
                    fetchTasks(page = 1) {
                        this.isLoading = true;
                        fetch(`/api/tasks/available-list?page=${page}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json())
                        .then(data => {
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
                        this.fetchTasks(new URL(url).searchParams.get('page'));
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async claimTask(taskId) {
                        this.isClaiming = taskId;

                        await fetch('/sanctum/csrf-cookie');

                        fetch(`/api/tasks/${taskId}/claim`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-XSRF-TOKEN': this.getCsrfToken() // <-- Perbaikan ada di sini
                            }
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            iziToast.success({ title: 'Berhasil!', message: data.message, position: 'topRight' });
                            setTimeout(() => window.location.href = `{{ route('tasks.my_history') }}`, 1500);
                        })
                        .catch(err => {
                            iziToast.error({ title: 'Gagal!', message: err.message || 'Gagal mengambil tugas.', position: 'topRight' });
                        })
                        .finally(() => this.isClaiming = null);
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>