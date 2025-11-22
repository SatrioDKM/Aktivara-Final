<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-edit mr-2"></i>
            {{ __('Edit Jenis Tugas: ') . $data['taskType']->name_task }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="taskTypeForm()" x-init="initData(@js($data['taskType']))" x-cloak>
                    <form @submit.prevent="save()">
                        <div class="space-y-6">

                            {{-- Nama Tugas --}}
                            <div>
                                <label for="name_task"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Tugas <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-clipboard-list text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.name_task" id="name_task"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Pembersihan Rutin Kamar" required>
                                </div>
                                <template x-if="errors.name_task">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.name_task[0]"></p>
                                </template>
                            </div>

                            {{-- Departemen & Prioritas --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div wire:ignore>
                                    <label for="departemen"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen
                                        <span class="text-red-500">*</span></label>
                                    <select id="departemen" class="mt-1 block w-full" required>
                                        <option value="HK">Housekeeping</option>
                                        <option value="TK">Teknisi</option>
                                        <option value="SC">Security</option>
                                        <option value="PK">Parking</option>
                                        <option value="WH">Warehouse</option>
                                        <option value="UMUM">Umum</option>
                                    </select>
                                    <template x-if="errors.departemen">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.departemen[0]"></p>
                                    </template>
                                </div>
                                <div wire:ignore>
                                    <label for="priority_level"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas
                                        Default <span class="text-red-500">*</span></label>
                                    <select id="priority_level" class="mt-1 block w-full" required>
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                        <option value="critical">Kritis</option>
                                    </select>
                                    <template x-if="errors.priority_level">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.priority_level[0]"></p>
                                    </template>
                                </div>
                            </div>

                            {{-- Otomatisasi Status Aset --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Otomatisasi Status Aset</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Saat Tugas Dibuat --}}
                                    <div wire:ignore>
                                        <label for="asset_condition_on_create"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi Aset (Saat Tugas Dibuat)</label>
                                        <select id="asset_condition_on_create" class="mt-1 block w-full">
                                            <option value="">-- Pilih --</option>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                    <div wire:ignore>
                                        <label for="asset_status_on_create"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Aset (Saat Tugas Dibuat)</label>
                                        <select id="asset_status_on_create" class="mt-1 block w-full">
                                            <option value="">-- Pilih --</option>
                                            <option value="available">Available</option>
                                            <option value="in_use">In Use</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="disposed">Disposed</option>
                                        </select>
                                    </div>

                                    {{-- Saat Tugas Selesai --}}
                                    <div wire:ignore>
                                        <label for="asset_condition_on_complete"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi Aset (Saat Tugas Selesai)</label>
                                        <select id="asset_condition_on_complete" class="mt-1 block w-full">
                                            <option value="">-- Pilih --</option>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                    <div wire:ignore>
                                        <label for="asset_status_on_complete"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Aset (Saat Tugas Selesai)</label>
                                        <select id="asset_status_on_complete" class="mt-1 block w-full">
                                            <option value="">-- Pilih --</option>
                                            <option value="available">Available</option>
                                            <option value="in_use">In Use</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="disposed">Disposed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                    (Opsional)</label>
                                <div class="relative mt-1">
                                    <div class="absolute top-3 left-0 ps-3 flex items-start pointer-events-none">
                                        <i class="fas fa-align-left text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.description" id="description" rows="3"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Jelaskan secara singkat tentang jenis tugas ini"></textarea>
                                </div>
                                <template x-if="errors.description">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.description[0]"></p>
                                </template>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('master.task_types.index') }}"
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
        function taskTypeForm() {
            return {
                isSubmitting: false,
                formData: {
                    id: null,
                    name_task: '',
                    departemen: '',
                    priority_level: '',
                    asset_condition_on_create: '',
                    asset_status_on_create: '',
                    asset_condition_on_complete: '',
                    asset_status_on_complete: '',
                    description: ''
                },
                errors: {},

                initData(taskType) {
                    this.formData = { ...taskType };

                    this.$nextTick(() => {
                        const self = this;
                        $('#departemen').val(this.formData.departemen).trigger('change');
                        $('#priority_level').val(this.formData.priority_level).trigger('change');

                        $('#departemen').on('change', function() { self.formData.departemen = $(this).val(); });
                        $('#priority_level').on('change', function() { self.formData.priority_level = $(this).val(); });

                        $('#departemen, #priority_level').select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity });

                        // Initialize new fields
                        $('#asset_condition_on_create').val(this.formData.asset_condition_on_create).trigger('change');
                        $('#asset_status_on_create').val(this.formData.asset_status_on_create).trigger('change');
                        $('#asset_condition_on_complete').val(this.formData.asset_condition_on_complete).trigger('change');
                        $('#asset_status_on_complete').val(this.formData.asset_status_on_complete).trigger('change');

                        $('#asset_condition_on_create').on('change', function() { self.formData.asset_condition_on_create = $(this).val(); });
                        $('#asset_status_on_create').on('change', function() { self.formData.asset_status_on_create = $(this).val(); });
                        $('#asset_condition_on_complete').on('change', function() { self.formData.asset_condition_on_complete = $(this).val(); });
                        $('#asset_status_on_complete').on('change', function() { self.formData.asset_status_on_complete = $(this).val(); });

                        $('#asset_condition_on_create, #asset_status_on_create, #asset_condition_on_complete, #asset_status_on_complete')
                            .select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity });
                    });
                },

                save() {
                    this.isSubmitting = true;
                    this.errors = {};

                    axios.put(`/api/task-types/${this.formData.id}`, this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Data jenis tugas berhasil diperbarui!');
                        window.location.href = "{{ route('master.task_types.index') }}";
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan. Periksa isian Anda.';
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                            msg = 'Terdapat kesalahan pada input Anda.';
                        } else if (error.response && error.response.data.message) {
                            msg = error.response.data.message;
                        }
                        window.iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                    })
                    .finally(() => this.isSubmitting = false);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>