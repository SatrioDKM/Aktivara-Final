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
                                        <p class="flex items-center">
                                            <i class="fas fa-box fa-fw mr-2 text-gray-400"></i>
                                            Aset: <span class="ml-1"
                                                x-text="task.asset ? `${task.asset.name_asset} (${task.asset.serial_number})` : 'Tidak spesifik'"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 sm:ml-4 flex-shrink-0">
                                    <button @click="openClaimModal(task)"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150 w-full sm:w-auto">
                                        <i class="fas fa-hand-paper mr-2"></i>
                                        <span>Ambil Tugas</span>
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

                {{-- Modal untuk Klaim Tugas --}}
                <div x-show="showClaimModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div
                        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        {{-- Overlay --}}
                        <div x-show="showClaimModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                        </div>

                        {{-- Konten Modal --}}
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div x-show="showClaimModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-hand-paper text-indigo-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                            id="modal-title">
                                            Ambil Tugas
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Anda akan mengklaim tugas "<strong x-text="currentTaskToClaim.title"></strong>".
                                                Mohon konfirmasi atau perbarui detail lokasi dan aset jika diperlukan.
                                            </p>

                                            <div class="mt-4 space-y-4">
                                                {{-- Lokasi --}}
                                                <div>
                                                    <label for="room_id"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                                                    <template x-if="currentTaskToClaim.room_id">
                                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                                                            x-text="currentTaskToClaim.room && currentTaskToClaim.room.floor && currentTaskToClaim.room.floor.building ? `${currentTaskToClaim.room.floor.building.name_building} / ${currentTaskToClaim.room.floor.name_floor} / ${currentTaskToClaim.room.name_room}` : 'Tidak spesifik'">
                                                        </p>
                                                    </template>
                                                    <template x-if="!currentTaskToClaim.room_id">
                                                        <select id="room_id" name="room_id" x-model="selectedRoomId"
                                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                                            <option value="">Pilih Lokasi (Opsional)</option>
                                                            <template x-for="room in allRooms" :key="room.id">
                                                                <option :value="room.id"
                                                                    x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </template>
                                                </div>

                                                {{-- Aset --}}
                                                <div>
                                                    <label for="asset_id"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset</label>
                                                    <template x-if="currentTaskToClaim.asset_id">
                                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                                                            x-text="currentTaskToClaim.asset ? `${currentTaskToClaim.asset.name_asset} (${currentTaskToClaim.asset.serial_number})` : 'Tidak spesifik'">
                                                        </p>
                                                    </template>
                                                    <template x-if="!currentTaskToClaim.asset_id">
                                                        <select id="asset_id" name="asset_id" x-model="selectedAssetId"
                                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                                            <option value="">Pilih Aset (Opsional)</option>
                                                            <template x-for="asset in allAssets" :key="asset.id">
                                                                <option :value="asset.id"
                                                                    x-text="`${asset.name_asset} ${asset.serial_number ? '(' + asset.serial_number + ')' : ''} - Stok: ${asset.current_stock}`">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" @click="claimTask()" :disabled="isSubmitting.includes(currentTaskToClaim.id)"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                    <template x-if="isSubmitting.includes(currentTaskToClaim.id)">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>
                                    </template>
                                    Klaim Tugas
                                </button>
                                <button type="button" @click="showClaimModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('availableTasks', () => ({
                tasks: [],
                allRooms: [],
                allAssets: [],
                isLoading: true,
                isSubmitting: [], // Array untuk melacak ID tugas yang sedang diproses
                showClaimModal: false,
                currentTaskToClaim: null,
                selectedRoomId: '',
                selectedAssetId: '',

                init() {
                    this.getTasks();
                    this.getRooms();
                    this.getAssets();
                },

                getTasks() {
                    this.isLoading = true;
                    axios.get('{{ route("api.tasks.available_data") }}')
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => {
                            window.iziToast.error({
                                title: 'Gagal!',
                                message: 'Gagal memuat data tugas. Silakan refresh halaman.',
                                position: 'topRight'
                            });
                            console.error(error);
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                getRooms() {
                    axios.get('{{ route("api.rooms.list") }}')
                        .then(response => {
                            this.allRooms = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal memuat daftar ruangan:', error);
                        });
                },

                getAssets() {
                    axios.get('{{ route("api.assets.list_for_dropdown") }}')
                        .then(response => {
                            this.allAssets = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal memuat daftar aset:', error);
                        });
                },

                openClaimModal(task) {
                    this.currentTaskToClaim = task;
                    this.selectedRoomId = task.room_id || ''; // Pre-fill if exists, otherwise empty
                    this.selectedAssetId = task.asset_id || ''; // Pre-fill if exists, otherwise empty
                    this.showClaimModal = true;
                },

                claimTask() {
                    if (!this.currentTaskToClaim) return;

                    const taskId = this.currentTaskToClaim.id;
                    this.isSubmitting.push(taskId);

                    const payload = {};
                    if (this.selectedRoomId) {
                        payload.room_id = this.selectedRoomId;
                    }
                    if (this.selectedAssetId) {
                        payload.asset_id = this.selectedAssetId;
                    }

                    axios.post(`/api/tasks/${taskId}/claim`, payload)
                        .then(response => {
                            sessionStorage.setItem('toastMessage', 'Tugas berhasil diambil!');
                            window.location.href = '{{ route("tasks.my_tasks") }}';
                        })
                        .catch(error => {
                            let message = 'Gagal mengambil tugas.';
                            if (error.response && error.response.data && error.response.data.message) {
                                message = error.response.data.message;
                            } else if (error.response && error.response.data && error.response.data.errors) {
                                // Handle validation errors from the backend
                                message = Object.values(error.response.data.errors).flat().join(', ');
                            }
                            window.iziToast.error({
                                title: 'Gagal!',
                                message: message,
                                position: 'topRight'
                            });

                            this.isSubmitting = this.isSubmitting.filter(id => id !== taskId);
                        })
                        .finally(() => {
                            this.showClaimModal = false; // Close modal regardless of success/failure
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