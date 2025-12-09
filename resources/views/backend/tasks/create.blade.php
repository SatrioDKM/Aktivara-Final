<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ __('Buat Tugas Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Inisialisasi komponen Alpine.js dan passing data dari controller --}}
            <div x-data="createTaskForm({
                buildings: {{ Js::from($data['buildings']) }},
                assets: {{ Js::from($data['assets']) }}
            })" x-cloak>

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

                {{-- Card Form Utama --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <form @submit.prevent="submitForm" class="p-6 md:p-8" novalidate>
                        <div class="space-y-6">

                            {{-- Judul Tugas --}}
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Tugas <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                            class="fas fa-heading text-gray-400"></i></div>
                                    <input type="text" id="title" x-model="formData.title"
                                        class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Perbaikan AC di Ruang Meeting" required>
                                </div>
                                <template x-if="errors.title">
                                    <p x-text="errors.title[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>

                            {{-- Prioritas & Jenis Tugas --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="priority"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat
                                        Prioritas <span class="text-red-500">*</span></label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-exclamation-triangle text-gray-400"></i>
                                        </div>
                                        <select id="priority" x-model="formData.priority"
                                            class="block w-full pl-10 border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            required>
                                            <option value="low">Rendah (Low)</option>
                                            <option value="medium">Sedang (Medium)</option>
                                            <option value="high">Tinggi (High)</option>
                                            <option value="critical">Kritis (Critical)</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="task_type_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Tugas
                                        <span class="text-red-500">*</span></label>
                                    <div class="mt-1" wire:ignore>
                                        <select id="task_type_id" class="block w-full" required>
                                            <option></option> {{-- Option kosong untuk placeholder Select2 --}}
                                            @foreach($data['taskTypes'] as $type)
                                            <option value="{{ $type->id }}">{{ $type->name_task }}
                                                ({{$type->departemen}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <template x-if="errors.task_type_id">
                                        <p x-text="errors.task_type_id[0]" class="text-xs text-red-500 mt-1"></p>
                                    </template>
                                </div>
                            </div>

                            {{-- Detail Lokasi & Aset (Opsional) --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-6">
                                <x-input-label for="description">
    {{ __('Detail Lokasi & Aset') }} 
    <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span>
</x-input-label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Gedung --}}
                                    <div>
                                        <label for="building_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gedung</label>
                                        <div class="mt-1" wire:ignore>
                                            <select id="building_id" class="block w-full">
                                                <option></option>
                                                @foreach($data['buildings'] as $building)
                                                <option value="{{ $building->id }}">{{ $building->name_building }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Lantai --}}
                                    <div>
                                        <label for="floor_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lantai</label>
                                        <div class="mt-1" wire:ignore>
                                            <select id="floor_id" class="block w-full" :disabled="!selected.building">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Ruangan --}}
                                    <div>
                                        <label for="room_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <div class="mt-1" wire:ignore>
                                            <select id="room_id" class="block w-full" :disabled="!selected.floor">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Aset Terkait --}}
                                    <div>
                                        <label for="asset_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset
                                            Terkait <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <div class="mt-1" wire:ignore>
                                            <select id="asset_id" class="block w-full">
                                                <option></option>
                                                @foreach($data['assets'] as $asset)
                                                <option value="{{ $asset->id }}">{{ $asset->name_asset }} ({{
                                                    $asset->serial_number ?? 'No S/N' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi Tugas --}}
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Tugas
                                    <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none"><i
                                            class="fas fa-align-left text-gray-400"></i></div>
                                    <textarea id="description" x-model="formData.description" rows="4"
                                        class="block w-full pl-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Jelaskan detail pekerjaan, instruksi khusus, atau informasi pendukung lainnya..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div
                            class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" :disabled="isSubmitting"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-paper-plane mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Buat Tugas'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('createTaskForm', (data) => ({
                formData: {
                    title: '',
                    priority: 'medium',
                    description: '',
                    task_type_id: '',
                    room_id: '',
                    asset_id: ''
                },
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },
                errors: {},

                selected: { building: '', floor: '' },

                init() {
                    const self = this;

                    $('#task_type_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Jenis Tugas --' }).on('change', function () { self.formData.task_type_id = $(this).val(); });
                    $('#building_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Gedung --' }).on('change', function () { self.selected.building = $(this).val(); });
                    const floorSelect = $('#floor_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Lantai --' }).on('change', function () { self.selected.floor = $(this).val(); });
                    const roomSelect = $('#room_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Ruangan --' }).on('change', function () { self.formData.room_id = $(this).val(); });
                    $('#asset_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Aset --' }).on('change', function () { self.formData.asset_id = $(this).val(); });

                    this.$watch('selected.building', (buildingId) => {
                        self.selected.floor = ''; floorSelect.val(null).trigger('change');
                        floorSelect.empty().append($('<option>')).select2({
                            theme: "classic", width: '100%', placeholder: '-- Loading... --', data: []
                        });
                        if(buildingId) {
                            axios.get(`/api/floors/list?building_id=${buildingId}`).then(res => {
                                // --- PERBAIKAN DI SINI ---
                                // Mengakses `res.data` karena controller mengembalikan array langsung
                                const dataArray = Array.isArray(res.data) ? res.data : [];
                                const floors = dataArray.map(f => ({ id: f.id, text: f.name_floor }));
                                floorSelect.select2({
                                    theme: "classic", width: '100%', placeholder: '-- Pilih Lantai --', data: floors
                                });
                            });
                        }
                    });

                    this.$watch('selected.floor', (floorId) => {
                        self.formData.room_id = ''; roomSelect.val(null).trigger('change');
                        roomSelect.empty().append($('<option>')).select2({
                            theme: "classic", width: '100%', placeholder: '-- Loading... --', data: []
                        });
                        if(floorId) {
                             axios.get(`/api/rooms/list?floor_id=${floorId}`).then(res => {
                                // --- PERBAIKAN DI SINI ---
                                const dataArray = Array.isArray(res.data) ? res.data : [];
                                const rooms = dataArray.map(r => ({ id: r.id, text: r.name_room }));
                                roomSelect.select2({
                                    theme: "classic", width: '100%', placeholder: '-- Pilih Ruangan --', data: rooms
                                });
                            });
                        }
                    });
                },

                submitForm() {
                    this.isSubmitting = true; this.errors = {};
                    axios.post('{{ route("api.tasks.store") }}', this.formData)
                    .then(response => {
                        this.showNotification(response.data.message, 'success');
                        setTimeout(() => {
                            if (response.data.redirect_url) { window.location.href = response.data.redirect_url; }
                        }, 1500);
                    })
                    .catch(error => {
                        let errorMessage = 'Gagal membuat tugas. Silakan coba lagi.';
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                            errorMessage = 'Harap periksa kembali isian form Anda.';
                        } else if(error.response && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                        this.showNotification(errorMessage, 'error');
                    })
                    .finally(() => { this.isSubmitting = false; });
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