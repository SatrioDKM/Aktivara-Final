<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ __('Tambah Ruangan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="roomForm(@js($data['floors']))" x-cloak>
                    <form @submit.prevent="saveRoom()">
                        <div class="space-y-6">

                            {{-- Gedung --}}
                            <div wire:ignore>
                                <label for="building_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gedung <span
                                        class="text-red-500">*</span></label>
                                <select id="building_id" class="mt-1 block w-full" required>
                                    <option value=""></option>
                                    @foreach ($data['buildings'] as $building)
                                    <option value="{{ $building->id }}">{{ $building->name_building }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lantai --}}
                            <div wire:ignore>
                                <label for="floor_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lantai <span
                                        class="text-red-500">*</span></label>
                                <select id="floor_id" class="mt-1 block w-full" required>
                                    <option value=""></option>
                                    {{-- Opsi di-generate oleh Alpine.js --}}
                                </select>
                                <template x-if="errors.floor_id">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.floor_id[0]"></p>
                                </template>
                            </div>

                            {{-- Nama Ruangan --}}
                            <div>
                                <label for="name_room"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ruangan
                                    <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-door-open text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.name_room" id="name_room"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Ruang Rapat Sakura" required>
                                </div>
                                <template x-if="errors.name_room">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.name_room[0]"></p>
                                </template>
                            </div>

                            {{-- Status --}}
                            <div wire:ignore>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span
                                        class="text-red-500">*</span></label>
                                <select id="status" class="mt-1 block w-full" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                                <template x-if="errors.status">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.status[0]"></p>
                                </template>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('master.rooms.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Ruangan'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function roomForm(allFloors) {
            return {
                isSubmitting: false,
                formData: {
                    name_room: '',
                    floor_id: '',
                    status: 'active'
                },
                errors: {},

                init() {
                    const self = this;

                    $('#building_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Gedung --' })
                        .on('change', function() {
                            self.updateFloorOptions($(this).val());
                        });

                    $('#floor_id').select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Gedung Terlebih Dahulu --' })
                        .on('change', function() {
                            self.formData.floor_id = $(this).val();
                        });

                    $('#status').select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity })
                        .on('change', function() {
                            self.formData.status = $(this).val();
                        });
                },

                updateFloorOptions(buildingId) {
                    const floorSelect = $('#floor_id');
                    floorSelect.empty().append('<option value=""></option>'); // Reset dan tambahkan option kosong

                    if (buildingId) {
                        floorSelect.select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Lantai --' });
                        const filteredFloors = allFloors.filter(floor => floor.building_id == buildingId);
                        filteredFloors.forEach(floor => {
                            floorSelect.append(new Option(floor.name_floor, floor.id, false, false));
                        });
                    } else {
                         floorSelect.select2({ theme: "classic", width: '100%', placeholder: '-- Pilih Gedung Terlebih Dahulu --' });
                    }
                    floorSelect.trigger('change'); // Trigger change untuk Select2
                },

                saveRoom() {
                    this.isSubmitting = true;
                    this.errors = {};

                    axios.post('/api/rooms', this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Ruangan baru berhasil ditambahkan!');
                        window.location.href = "{{ route('master.rooms.index') }}";
                    })
                    .catch(error => {
                        let errorMessage = 'Gagal menyimpan. Silakan periksa kembali isian Anda.';
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                            errorMessage = 'Terdapat kesalahan pada input Anda.';
                        } else if (error.response && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }

                        window.iziToast.error({
                            title: 'Gagal!',
                            message: errorMessage,
                            position: 'topRight'
                        });
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>