<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-edit mr-2"></i>
            {{ __('Edit Gedung: ') . $data['building']->name_building }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100" x-data="buildingForm()"
                    x-init="initData(@js($data['building']))" x-cloak>
                    <form @submit.prevent="saveBuilding()">
                        <div class="space-y-6">
                            {{-- Nama Gedung --}}
                            <div>
                                <label for="name_building"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Gedung <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-building text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.name_building" id="name_building"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Gedung Tower A" required>
                                </div>
                                <template x-if="errors.name_building">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.name_building[0]"></p>
                                </template>
                            </div>

                            {{-- Alamat --}}
                            <div>
                                <label for="address"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat
                                    (Opsional)</label>
                                <div class="relative mt-1">
                                    <div class="absolute top-3 left-0 ps-3 flex items-start pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.address" id="address" rows="3"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Jl. Jend. Sudirman Kav. 52-53, Jakarta"></textarea>
                                </div>
                                <template x-if="errors.address">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.address[0]"></p>
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
                            <a href="{{ route('master.buildings.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function buildingForm() {
            return {
                isSubmitting: false,
                formData: {
                    id: null,
                    name_building: '',
                    address: '',
                    status: ''
                },
                errors: {},

                initData(building) {
                    // Isi formData dengan data dari controller
                    this.formData = {
                        id: building.id,
                        name_building: building.name_building,
                        address: building.address,
                        status: building.status
                    };

                    // Gunakan $nextTick untuk memastikan elemen sudah ada di DOM sebelum inisialisasi Select2
                    this.$nextTick(() => {
                        const self = this;
                        // Inisialisasi Select2 dengan nilai yang sudah ada
                        $('#status').val(this.formData.status).trigger('change');
                        // Hubungkan perubahan Select2 ke data Alpine
                        $('#status').on('change', function() {
                            self.formData.status = $(this).val();
                        });

                        $('#status').select2({
                            theme: "classic",
                            width: '100%',
                            minimumResultsForSearch: Infinity
                        });
                    });
                },

                saveBuilding() {
                    this.isSubmitting = true;
                    this.errors = {};

                    // Kirim data ke API menggunakan Axios
                    axios.put(`/api/buildings/${this.formData.id}`, this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Data gedung berhasil diperbarui!');
                        window.location.href = "{{ route('master.buildings.index') }}";
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