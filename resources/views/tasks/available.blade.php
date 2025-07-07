<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Papan Tugas (Job Board)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="availableTasks()">

                <!-- Notifikasi Global -->
                <div x-show="notification.show" x-transition class="fixed top-20 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-if="isLoading">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                            Memuat tugas yang tersedia...
                        </div>
                    </template>

                    <template x-for="task in tasks" :key="task.id">
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-lg text-gray-800" x-text="task.title"></p>
                                <p class="text-sm text-gray-600">
                                    Dibuat oleh: <strong x-text="task.creator.name"></strong> - <span
                                        x-text="new Date(task.created_at).toLocaleDateString('id-ID')"></span>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Lokasi:
                                    {{-- PERBAIKAN DI SINI --}}
                                    <span
                                        x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                                </p>
                            </div>
                            <div>
                                <x-primary-button @click="claimTask(task.id)" ::disabled="isSubmitting">
                                    <span x-show="!isSubmitting">Ambil Tugas</span>
                                    <span x-show="isSubmitting">Memproses...</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </template>

                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <p class="text-center text-gray-500">Tidak ada tugas yang tersedia untuk departemen Anda
                                saat ini.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function availableTasks() {
            return {
                tasks: [],
                isLoading: true,
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },

                async init() {
                    await fetch('/sanctum/csrf-cookie');
                    this.getTasks();
                },

                getTasks() {
                    this.isLoading = true;
                    fetch('{{ route('api.tasks.available_data') }}', { headers: { 'Accept': 'application/json' } })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal memuat data tugas.');
                            return res.json();
                        })
                        .then(data => {
                            this.tasks = data;
                        })
                        .catch(err => {
                            this.showNotification(err.message, 'error');
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                async claimTask(taskId) {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie'); // Pastikan cookie segar sebelum POST
                    fetch(`/api/tasks/${taskId}/claim`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        }
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
                    if (csrfCookie) {
                        return decodeURIComponent(csrfCookie.split('=')[1]);
                    }
                    return '';
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }
    </script>
</x-app-layout>