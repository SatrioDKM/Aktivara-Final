<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-archive mr-2"></i>
            {{ __('Riwayat Tugas Selesai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="completedHistory()" x-cloak>

                {{-- Card untuk Tabel Hasil --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Daftar Tugas yang Telah
                            Selesai</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Judul Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dikerjakan Oleh</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dibuat Oleh</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal Selesai</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 relative">
                                {{-- Overlay Loading --}}
                                <template x-if="isLoading">
                                    <tr
                                        class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-50 dark:bg-opacity-50 flex items-center justify-center z-10">
                                        <td class="text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                                            <p class="mt-2">Memuat riwayat...</p>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="task in history.data" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="task.title"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.assignee ? task.assignee.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.creator ? task.creator.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="new Date(task.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a :href="`/tasks/${task.id}`"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Lihat
                                                Detail</a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && (!history.data || history.data.length === 0)">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                            <p class="mt-4 text-gray-500 dark:text-gray-400">Belum ada riwayat tugas
                                                yang selesai.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    {{-- Kontrol Paginasi --}}
                    <div class="p-4 flex justify-between items-center bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
                        x-show="history.total > 0">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Menampilkan <span class="font-medium" x-text="history.from || 0"></span> - <span
                                class="font-medium" x-text="history.to || 0"></span> dari <span class="font-medium"
                                x-text="history.total || 0"></span> hasil
                        </p>
                        <div class="flex space-x-2">
                            <button @click="fetchHistory(history.current_page - 1)" :disabled="!history.prev_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Sebelumnya</button>
                            <button @click="fetchHistory(history.current_page + 1)" :disabled="!history.next_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('completedHistory', () => ({
                isLoading: true,
                history: { data: [], from: 0, to: 0, total: 0, current_page: 1, prev_page_url: null, next_page_url: null, last_page: 1 },

                init() {
                    this.fetchHistory(1);
                },

                fetchHistory(page) {
                    if (page < 1 || (page > this.history.last_page && this.history.last_page !== null)) return;

                    this.isLoading = true;
                    const params = new URLSearchParams({ page: page }).toString();

                    axios.get(`{{ route('api.tasks.completed_history_data') }}?${params}`)
                        .then(response => {
                            this.history = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal memuat riwayat tugas selesai:', error);
                            window.iziToast.error({
                                title: 'Gagal!',
                                message: 'Gagal memuat riwayat. Silakan coba lagi.',
                                position: 'topRight'
                            });
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>