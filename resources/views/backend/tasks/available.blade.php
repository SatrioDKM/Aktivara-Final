<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-clipboard-list mr-2"></i>
            {{ __('Papan Tugas (Job Board)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="availableTasks()" x-cloak>

                {{-- Komponen Notifikasi Global --}}
                <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="fixed top-20 right-5 z-50 rounded-lg shadow-lg"
                    :class="{ 'bg-green-500 text-white': notification.type === 'success', 'bg-red-500 text-white': notification.type === 'error' }">
                    <div class="flex items-center p-4">
                        <i class="fas"
                            :class="{ 'fa-check-circle': notification.type === 'success', 'fa-times-circle': notification.type === 'error' }"></i>
                        <div class="ml-3">
                            <p class="font-bold" x-text="notification.type === 'success' ? 'Berhasil!' : 'Oops!'"></p>
                            <p class="text-sm" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- State: Loading --}}
                    <template x-if="isLoading">
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                            <p class="mt-2">Memuat tugas yang tersedia...</p>
                        </div>
                    </template>

                    {{-- Daftar Tugas --}}
                    <template x-for="task in tasks" :key="task.id">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border-l-4 transition-transform transform hover:-translate-y-1"
                            :class="priorityBorderColor(task.priority)">
                            <div class="p-6 flex flex-col sm:flex-row justify-between sm:items-center">
                                <div class="flex-grow">
                                    <p class="font-bold text-lg text-gray-900 dark:text-gray-100" x-text="task.title">
                                    </p>
                                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        <p class="flex items-center">
                                            <i class="fas fa-user-tie fa-fw mr-2 text-gray-400"></i>
                                            Dibuat oleh: <strong class="ml-1 text-gray-700 dark:text-gray-200"
                                                x-text="task.creator.name"></strong>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-calendar-alt fa-fw mr-2 text-gray-400"></i>
                                            Tanggal: <span class="ml-1"
                                                x-text="new Date(task.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-400"></i>
                                            Lokasi: <span class="ml-1"
                                                x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 sm:ml-4 flex-shrink-0">
                                    <button @click="claimTask(task.id)" :disabled="isSubmitting.includes(task.id)"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150 w-full sm:w-auto">
                                        <template x-if="isSubmitting.includes(task.id)">
                                            <i class="fas fa-circle-notch fa-spin mr-2"></i>
                                        </template>
                                        <i class="fas fa-hand-paper mr-2" x-show="!isSubmitting.includes(task.id)"></i>
                                        <span
                                            x-text="isSubmitting.includes(task.id) ? 'Memproses...' : 'Ambil Tugas'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- State: Kosong --}}
                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                            <div class="flex justify-center items-center">
                                <i class="fas fa-check-circle text-5xl text-green-400"></i>
                            </div>
                            <p class="mt-4 font-semibold text-lg text-gray-700 dark:text-gray-200">Semua Tugas Selesai!
                            </p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Tidak ada tugas yang tersedia untuk
                                departemen Anda saat ini. Kerja bagus!</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('availableTasks', () => ({
                tasks: [],
                isLoading: true,
                isSubmitting: [], // Array untuk melacak ID tugas yang sedang diproses
                notification: { show: false, message: '', type: 'success' },

                init() {
                    this.getTasks();
                },

                getTasks() {
                    this.isLoading = true;
                    axios.get('{{ route("api.tasks.available_data") }}')
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => {
                            this.showNotification('Gagal memuat data tugas. Silakan refresh halaman.', 'error');
                            console.error(error);
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                claimTask(taskId) {
                    // Tambahkan taskId ke array isSubmitting untuk menonaktifkan tombol yang benar
                    this.isSubmitting.push(taskId);

                    axios.post(`/api/tasks/${taskId}/claim`)
                        .then(response => {
                            // Alihkan ke halaman "Tugas Saya" setelah berhasil
                            window.location.href = '{{ route("tasks.my_tasks") }}';
                        })
                        .catch(error => {
                            let message = 'Gagal mengambil tugas.';
                            if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            }
                            this.showNotification(message, 'error');
                            // Hapus taskId dari array isSubmitting jika gagal
                            this.isSubmitting = this.isSubmitting.filter(id => id !== taskId);
                        });
                },

                priorityBorderColor(priority) {
                    const colors = {
                        critical: 'border-red-500',
                        high: 'border-yellow-500',
                        medium: 'border-blue-500',
                        low: 'border-green-500',
                    };
                    return colors[priority] || 'border-gray-300';
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>