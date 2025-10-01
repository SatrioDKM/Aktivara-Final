<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Review Laporan Staff') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="reviewPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl.
                                        Laporan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="4" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && tasks.length === 0">
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-gray-500">Tidak ada laporan yang
                                            perlu di-review.</td>
                                    </tr>
                                </template>
                                <template x-for="task in tasks" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium" x-text="task.title"></td>
                                        <td class="px-6 py-4" x-text="task.staff.name"></td>
                                        <td class="px-6 py-4"
                                            x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                        <td class="px-6 py-4 text-center">
                                            <a :href="`/tasks/${task.id}`"
                                                class="text-indigo-600 hover:underline text-sm me-4">Lihat Laporan</a>
                                            <button @click="openReviewModal(task, 'completed')"
                                                class="text-green-600 hover:text-green-800 text-sm">Setujui</button>
                                            <button @click="openReviewModal(task, 'rejected')"
                                                class="text-red-600 hover:text-red-800 text-sm ms-2">Tolak</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showModal" x-transition x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitReview()">
                        <div class="p-6">
                            <h3 class="text-lg font-medium"
                                x-text="reviewData.decision === 'completed' ? 'Setujui Laporan?' : 'Tolak Laporan?'">
                            </h3>
                            <div x-show="reviewData.decision === 'rejected'" class="mt-4">
                                <label for="rejection_notes" class="block text-sm font-medium">Alasan Penolakan</label>
                                <textarea x-model="reviewData.notes" id="rejection_notes" rows="4"
                                    class="mt-1 block w-full rounded-md"
                                    placeholder="Jelaskan mengapa laporan ini ditolak..." required></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" ::disabled="isSubmitting">Kirim Review</x-primary-button>
                            <x-secondary-button type="button" @click="closeModal()" class="me-3">Batal
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function reviewPage() {
                return {
                    tasks: [], isLoading: true, isSubmitting: false, showModal: false,
                    reviewData: { id: null, decision: '', notes: '' },
                    init() { this.fetchTasks(); },
                    fetchTasks() { /* ... */ },
                    openReviewModal(task, decision) {
                        this.reviewData = { id: task.id, decision: decision, notes: '' };
                        this.showModal = true;
                    },
                    closeModal() { this.showModal = false; },
                    getCsrfToken() { /* ... */ },
                    async submitReview() { /* ... Logika AJAX POST untuk submit review ... */ }
                }
            }
    </script>
    @endpush
</x-app-layout>