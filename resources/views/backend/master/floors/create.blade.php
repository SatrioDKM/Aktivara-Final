<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ __('Tambah Lantai Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100" x-data="floorForm()" x-cloak>
                    <form @submit.prevent="saveFloor()">
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
                                <template x-if="errors.building_id">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.building_id[0]"></p>
                                </template>
                            </div>

                            {{-- Nama Lantai --}}
                            <div>
                                <label for="name_floor"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lantai <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-layer-group text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.name_floor" id="name_floor"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Lantai 1, Basement 2, atau Rooftop" required>
                                </div>
                                <template x-if="errors.name_floor">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.name_floor[0]"></p>
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
                            <a href="{{ route('master.floors.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Lantai'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function floorForm() {
            return {
                isSubmitting: false,
                formData: {
                    name_floor: '',
                    building_id: '',
                    status: 'active'
                },
                errors: {},

                init() {
                    const self = this;
                    // Inisialisasi Select2
                    $('#building_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Gedung --'
                    }).on('change', function() {
                        self.formData.building_id = $(this).val();
                    });

                    $('#status').select2({
                        theme: "classic",
                        width: '100%',
                        minimumResultsForSearch: Infinity
                    }).on('change', function() {
                        self.formData.status = $(this).val();
                    });
                },

                saveFloor() {
                    this.isSubmitting = true;
                    this.errors = {};

                    axios.post('/api/floors', this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Lantai baru berhasil ditambahkan!');
                        window.location.href = "{{ route('master.floors.index') }}";
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