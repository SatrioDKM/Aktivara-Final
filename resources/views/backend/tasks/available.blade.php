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

                <div class="space-y-4">
                    {{-- State: Loading --}}
                    <template x-if="isLoading">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                            <p class="mt-2">Memuat tugas yang tersedia...</p>
                        </div>
                    </template>

                    {{-- Daftar Tugas --}}
                    <template x-for="task in tasks" :key="task.id">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border-l-4 transition-transform transform hover:-translate-y-1 mb-4"
                            :class="priorityBorderColor(task.priority)">
                            
                            <div class="p-6 flex flex-col sm:flex-row justify-between sm:items-center">
                                {{-- Info Kiri --}}
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-lg text-gray-900 dark:text-gray-100" x-text="task.title"></p>
                                        
                                        {{-- Badge Prioritas (Mobile Friendly) --}}
                                        <span class="sm:hidden px-2 py-1 rounded text-xs font-bold text-white"
                                              :class="{
                                                  'bg-gray-500': task.priority === 'low',
                                                  'bg-blue-500': task.priority === 'medium',
                                                  'bg-orange-500': task.priority === 'high',
                                                  'bg-red-600': task.priority === 'critical'
                                              }"
                                              x-text="task.priority.toUpperCase()">
                                        </span>
                                    </div>

                                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        <p class="flex items-center">
                                            <i class="fas fa-user-tie fa-fw mr-2 text-gray-400"></i>
                                            Dibuat oleh: <strong class="ml-1 text-gray-700 dark:text-gray-200" x-text="task.complaint ? (task.complaint.reporter_name + ' (Tamu)') : (task.creator ? task.creator.name : 'System')"></strong>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-calendar-alt fa-fw mr-2 text-gray-400"></i>
                                            Tanggal: <span class="ml-1" x-text="new Date(task.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-400"></i>
                                            Lokasi: <span class="ml-1" x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                                        </p>
                                        <p class="flex items-center">
                                            <i class="fas fa-box fa-fw mr-2 text-gray-400"></i>
                                            Aset: <span class="ml-1" x-text="task.asset ? `${task.asset.name_asset} (${task.asset.serial_number})` : '-'"></span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Tombol Kanan --}}
                                <div class="mt-4 sm:mt-0 sm:ml-4 flex-shrink-0 flex flex-col items-end space-y-2">
                                    {{-- Badge Prioritas (Desktop) --}}
                                    <span class="hidden sm:inline-block px-2 py-1 rounded text-xs font-bold text-white mb-2"
                                          :class="{
                                              'bg-gray-500': task.priority === 'low',
                                              'bg-blue-500': task.priority === 'medium',
                                              'bg-orange-500': task.priority === 'high',
                                              'bg-red-600': task.priority === 'critical'
                                          }"
                                          x-text="task.priority.toUpperCase()">
                                    </span>

                                    {{-- >>> TOMBOL AMBIL TUGAS <<< --}}
                                    <button @click="openClaimModal(task)"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150 w-full sm:w-auto shadow-sm">
                                        <i class="fas fa-hand-paper mr-2"></i>
                                        <span>Ambil</span>
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
                            <p class="mt-4 font-semibold text-lg text-gray-700 dark:text-gray-200">Semua Tugas Selesai!</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Tidak ada tugas yang tersedia untuk departemen Anda saat ini. Kerja bagus!</p>
                        </div>
                    </template>
                </div>

                {{-- MODAL KONFIRMASI (PREVIEW ONLY) --}}
                <div x-show="showClaimModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showClaimModal = false"></div>

                    {{-- Modal Content --}}
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            
                            {{-- Header --}}
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900" x-text="currentTaskToClaim?.title"></h3>
                                        
                                        <div class="mt-4 grid grid-cols-1 gap-y-3 text-sm text-gray-600">
                                            {{-- Prioritas --}}
                                            <div class="flex justify-between border-b pb-2">
                                                <span class="font-semibold">Prioritas:</span>
                                                <span class="px-2 py-1 rounded text-xs font-bold text-white"
                                                      :class="{
                                                          'bg-gray-500': currentTaskToClaim?.priority === 'low',
                                                          'bg-blue-500': currentTaskToClaim?.priority === 'medium',
                                                          'bg-orange-500': currentTaskToClaim?.priority === 'high',
                                                          'bg-red-600': currentTaskToClaim?.priority === 'critical'
                                                      }"
                                                      x-text="currentTaskToClaim?.priority ? currentTaskToClaim.priority.toUpperCase() : '-'">
                                                </span>
                                            </div>

                                            {{-- Lokasi --}}
                                            <div class="flex justify-between border-b pb-2">
                                                <span class="font-semibold">Lokasi:</span>
                                                <span class="text-right" x-text="currentTaskToClaim?.room 
                                                    ? (currentTaskToClaim.room.floor.building.name_building + ' - ' + currentTaskToClaim.room.name_room) 
                                                    : 'Lokasi Tidak Spesifik'"></span>
                                            </div>

                                            {{-- Aset --}}
                                            <div class="flex justify-between border-b pb-2">
                                                <span class="font-semibold">Aset:</span>
                                                <span class="text-right" x-text="currentTaskToClaim?.asset 
                                                    ? (currentTaskToClaim.asset.name_asset + ' (' + currentTaskToClaim.asset.serial_number + ')') 
                                                    : '-'"></span>
                                            </div>

                                            {{-- Deskripsi --}}
                                            <div class="bg-gray-50 p-3 rounded-md mt-2">
                                                <span class="font-semibold block mb-1 text-xs text-gray-500 uppercase">Deskripsi Tugas:</span>
                                                <p class="italic text-gray-700" x-text="currentTaskToClaim?.description || 'Tidak ada deskripsi'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer (Tombol Aksi) --}}
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" 
                                        @click="claimTask()"
                                        class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                                    Ya, Ambil Tugas Ini
                                </button>
                                <button type="button" 
                                        @click="showClaimModal = false"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END MODAL --}}

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('availableTasks', () => ({
                tasks: [],
                isLoading: true,
                showClaimModal: false,
                currentTaskToClaim: null,

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
                            // Fallback kalau pake SweetAlert
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal', 'Gagal memuat data tugas.', 'error');
                            } else {
                                alert('Gagal memuat data tugas.');
                            }
                            console.error(error);
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                openClaimModal(task) {
                    this.currentTaskToClaim = task;
                    this.showClaimModal = true;
                },

                claimTask() {
                    if (!this.currentTaskToClaim) return;

                    const taskId = this.currentTaskToClaim.id;

                    axios.post(`/api/tasks/${taskId}/claim`)
                        .then(response => {
                            // Gunakan SweetAlert jika ada, atau reload biasa
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Tugas berhasil diambil!',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = '{{ route("tasks.my_tasks") }}';
                                });
                            } else {
                                window.location.href = '{{ route("tasks.my_tasks") }}';
                            }
                        })
                        .catch(error => {
                            let message = 'Gagal mengambil tugas.';
                            if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            }
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal', message, 'error');
                            } else {
                                alert(message);
                            }
                        })
                        .finally(() => {
                            this.showClaimModal = false;
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
            }));
        });
    </script>
    @endpush
</x-app-layout>