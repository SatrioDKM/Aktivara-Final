<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-person-running mr-2"></i>
            {{ __('Tugas Saya yang Sedang Aktif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="myTasks()" x-cloak>
                <div class="space-y-4">
                    {{-- State: Loading --}}
                    <template x-if="isLoading">
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                            <p class="mt-2">Memuat tugas aktif Anda...</p>
                        </div>
                    </template>

                    {{-- Daftar Tugas dalam bentuk Card --}}
                    <template x-for="task in tasks" :key="task.id">
                        <a :href="`/tasks/${task.id}`" class="block group">
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border-l-4 transition-all duration-300 group-hover:shadow-xl group-hover:-translate-y-1"
                                :class="statusBorderColor(task.status)">
                                <div class="p-6">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                                        {{-- Informasi Utama Tugas --}}
                                        <div class="flex-grow">
                                            <div class="flex items-center mb-1">
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(task.status)"
                                                    x-text="statusText(task.status)"></span>
                                            </div>
                                            <p class="font-bold text-lg text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition"
                                                x-text="task.title"></p>
                                            <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                                <p class="flex items-center">
                                                    <i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-400"></i>
                                                    Lokasi: <span
                                                        class="ml-1 font-medium text-gray-800 dark:text-gray-200"
                                                        x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                                                </p>
                                                <p class="flex items-center">
                                                    <i class="fas fa-tags fa-fw mr-2 text-gray-400"></i>
                                                    Jenis: <span
                                                        class="ml-1 font-medium text-gray-800 dark:text-gray-200"
                                                        x-text="task.task_type.name_task"></span>
                                                </p>
                                            </div>
                                        </div>
                                        {{-- Tombol Aksi --}}
                                        <div class="mt-4 sm:mt-0 sm:ml-4 flex-shrink-0 flex items-center">
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                Lihat Detail <i
                                                    class="fas fa-arrow-right ml-1 transform transition-transform group-hover:translate-x-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Pesan Revisi jika ditolak --}}
                                    <template x-if="task.status === 'rejected' && task.rejection_notes">
                                        <div class="mt-4 border-t border-red-200 dark:border-red-900 pt-3">
                                            <p class="text-sm text-red-600 dark:text-red-400 flex items-start">
                                                <i class="fas fa-exclamation-circle fa-fw mr-2 mt-1"></i>
                                                <span><strong>Catatan Revisi:</strong> <span
                                                        x-text="task.rejection_notes"></span></span>
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </a>
                    </template>

                    {{-- State: Kosong --}}
                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                            <div class="flex justify-center items-center">
                                <i class="fas fa-inbox text-5xl text-gray-400"></i>
                            </div>
                            <p class="mt-4 font-semibold text-lg text-gray-700 dark:text-gray-200">Tidak Ada Tugas Aktif
                            </p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Saat ini Anda tidak memiliki pekerjaan yang
                                sedang berlangsung atau perlu direvisi.</p>
                            <div class="mt-6">
                                <a href="{{ route('tasks.available') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-clipboard-list mr-2"></i>
                                    Lihat Papan Tugas
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('myTasks', () => ({
                tasks: [],
                isLoading: true,

                init() {
                    this.getTasks();
                },

                getTasks() {
                    this.isLoading = true;
                    axios.get('{{ route("api.tasks.my_tasks_data") }}')
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => {
                            console.error("Gagal memuat tugas:", error);
                            alert('Tidak dapat memuat tugas Anda. Silakan coba refresh halaman.');
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                statusColor(status) {
                    const colors = {
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                },

                statusBorderColor(status) {
                    const colors = {
                        'in_progress': 'border-blue-500',
                        'rejected': 'border-red-500',
                    };
                    return colors[status] || 'border-gray-300';
                },

                statusText(status) {
                    if (status === 'in_progress') return 'Sedang Dikerjakan';
                    if (status === 'rejected') return 'Ditolak / Perlu Revisi';
                    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>