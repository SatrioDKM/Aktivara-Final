<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-check-double mr-2"></i>
            {{ __('Review Laporan Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="reviewTasks()" x-cloak>
                <div class="space-y-4">
                    {{-- State: Loading --}}
                    <template x-if="isLoading">
                        <div
                            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                            <p class="mt-2">Memuat tugas untuk direview...</p>
                        </div>
                    </template>

                    {{-- Daftar Tugas --}}
                    <template x-for="task in tasks" :key="task.id">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                            <div class="p-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="flex-grow">
                                    <a :href="`/tasks/${task.id}`"
                                        class="font-bold text-lg text-indigo-600 hover:underline dark:text-indigo-400"
                                        x-text="task.title"></a>
                                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        <p class="flex items-center">
                                            <i class="fas fa-user-cog fa-fw mr-2 text-gray-400"></i>
                                            Dikerjakan oleh:
                                            {{-- PERBAIKAN: Mengganti task.staff menjadi task.assignee --}}
                                            <strong class="ml-1 text-gray-700 dark:text-gray-200"
                                                x-text="task.assignee ? task.assignee.name : 'N/A'"></strong>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-400"></i>
                                            Lokasi: <span class="ml-1"
                                                x-text="task.room ? task.room.name_room : 'Tidak spesifik'"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex items-center space-x-2 w-full sm:w-auto">
                                    <button @click="openRejectModal(task)"
                                        class="w-1/2 sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900">
                                        <i class="fas fa-times mr-2"></i> Tolak
                                    </button>
                                    <button @click="approveTask(task.id)"
                                        class="w-1/2 sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900">
                                        <i class="fas fa-check mr-2"></i> Setujui
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- State: Kosong --}}
                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                            <i class="fas fa-thumbs-up text-5xl text-gray-400"></i>
                            <p class="mt-4 font-semibold text-lg text-gray-700 dark:text-gray-200">Luar Biasa!</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Tidak ada laporan tugas yang perlu Anda
                                review saat ini.</p>
                        </div>
                    </template>
                </div>

                {{-- Modal untuk Alasan Penolakan --}}
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div @click="showModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full"
                            @click.away="showModal = false">
                            <form @submit.prevent="submitRejection">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Alasan Penolakan
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tulis alasan mengapa tugas
                                        "<span class="font-bold" x-text="selectedTask.title"></span>" ditolak.</p>
                                    <div class="mt-4">
                                        <textarea x-model="rejectionNotes" rows="4"
                                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Contoh: Lampiran foto kurang jelas, mohon ulangi."
                                            required></textarea>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 flex justify-end space-x-3">
                                    <x-secondary-button type="button" @click="showModal = false">Batal
                                    </x-secondary-button>
                                    <x-danger-button type="submit">Kirim Penolakan</x-danger-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reviewTasks', () => ({
                tasks: [],
                isLoading: true,
                showModal: false,
                selectedTask: {},
                rejectionNotes: '',

                init() {
                    this.getTasks();
                },

                getTasks() {
                    this.isLoading = true;
                    axios.get('{{ route("api.tasks.review_list_data") }}')
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => alert('Gagal memuat daftar tugas.'))
                        .finally(() => this.isLoading = false);
                },

                approveTask(taskId) {
                    if (!confirm('Apakah Anda yakin ingin menyetujui dan menyelesaikan tugas ini?')) return;
                    this.submitReview(taskId, 'completed');
                },

                openRejectModal(task) {
                    this.selectedTask = task;
                    this.rejectionNotes = '';
                    this.showModal = true;
                },

                submitRejection() {
                    if (!this.rejectionNotes.trim()) {
                        alert('Alasan penolakan tidak boleh kosong.');
                        return;
                    }
                    this.submitReview(this.selectedTask.id, 'rejected', this.rejectionNotes);
                    this.showModal = false;
                },

                submitReview(taskId, decision, notes = null) {
                    axios.post(`/api/tasks/${taskId}/review`, {
                        decision: decision,
                        rejection_notes: notes
                    })
                    .then(response => {
                        this.tasks = this.tasks.filter(t => t.id !== taskId);
                        // Menggunakan iziToast untuk notifikasi yang lebih baik
                        window.iziToast.success({
                            title: 'Berhasil',
                            message: 'Review berhasil dikirim.',
                            position: 'topRight'
                        });
                    })
                    .catch(error => {
                        let message = 'Gagal mengirim review.';
                        if (error.response && error.response.data && error.response.data.message) {
                           message = error.response.data.message;
                        }
                        window.iziToast.error({
                            title: 'Error',
                            message: message,
                            position: 'topRight'
                        });
                    });
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>