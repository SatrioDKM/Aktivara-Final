<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Tugas Selesai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="reviewTasks()">
                <div class="space-y-4">
                    <template x-if="isLoading">
                        <p class="text-center text-gray-500">Memuat tugas untuk direview...</p>
                    </template>

                    <template x-for="task in tasks" :key="task.id">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a :href="`/tasks/${task.id}`"
                                        class="font-bold text-lg text-indigo-600 hover:underline"
                                        x-text="task.title"></a>
                                    <div class="text-sm text-gray-600">
                                        Dikerjakan oleh: <strong x-text="task.staff.name"></strong>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Lokasi: <span
                                            x-text="task.room ? task.room.name_room : 'Tidak spesifik'"></span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <x-secondary-button @click="submitReview(task.id, 'rejected')">Tolak
                                    </x-secondary-button>
                                    <x-primary-button @click="submitReview(task.id, 'completed')">Setujui
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <p class="text-gray-500">Tidak ada tugas yang perlu direview saat ini.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function reviewTasks() {
            return {
                tasks: [], isLoading: true,
                async init() {
                    // Cukup panggil getTasks, karena getTasks sudah akan memanggil csrf-cookie
                    this.getTasks();
                },
                async getTasks() {
                    this.isLoading = true;
                    await fetch('/sanctum/csrf-cookie'); // Pastikan cookie segar setiap kali data diambil
                    fetch('{{ route('api.tasks.review_list_data') }}', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json()).then(data => { this.tasks = data; this.isLoading = false; });
                },

                // --- PERBAIKAN DI SINI ---
                async submitReview(taskId, decision) {
                    // 1. Dapatkan cookie CSRF yang segar sebelum mengirim POST
                    await fetch('/sanctum/csrf-cookie');

                    // 2. Lanjutkan dengan mengirim permintaan
                    fetch(`/api/tasks/${taskId}/review`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify({ decision: decision })
                    }).then(res => {
                        if (res.ok) {
                            this.getTasks(); // Refresh list setelah berhasil
                        } else {
                            alert('Gagal mengirim review.');
                        }
                    });
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) {
                        return decodeURIComponent(csrfCookie.split('=')[1]);
                    }
                    return '';
                }
            }
        }
    </script>
</x-app-layout>