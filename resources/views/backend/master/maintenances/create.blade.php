<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ __('Lapor Kerusakan Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="maintenanceForm()" x-cloak>
                    <form @submit.prevent="save()">
                        <div class="space-y-6">

                            {{-- Aset yang Rusak --}}
                            <div wire:ignore>
                                <label for="asset_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset yang Rusak
                                    <span class="text-red-500">*</span></label>
                                <select id="asset_id" class="mt-1 block w-full" required>
                                    <option value=""></option>
                                    @foreach ($data['assets'] as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name_asset }} (S/N: {{
                                        $asset->serial_number ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                                <template x-if="errors.asset_id">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.asset_id[0]"></p>
                                </template>
                            </div>

                            {{-- Deskripsi Kerusakan --}}
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                    Kerusakan <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute top-3 left-0 ps-3 flex items-start pointer-events-none">
                                        <i class="fas fa-comment-dots text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.description" id="description" rows="4"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Jelaskan kerusakan secara detail (minimal 10 karakter)..."
                                        required></textarea>
                                </div>
                                <template x-if="errors.description">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.description[0]"></p>
                                </template>
                            </div>

                            {{-- Prioritas Tugas Perbaikan --}}
                            <div wire:ignore>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat Prioritas
                                    Perbaikan <span class="text-red-500">*</span></label>
                                <select id="priority" class="mt-1 block w-full" required>
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                    <option value="critical">Kritis</option>
                                </select>
                                <template x-if="errors.priority">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.priority[0]"></p>
                                </template>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('master.maintenances.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-paper-plane mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Buat Laporan & Tugas'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function maintenanceForm() {
            return {
                isSubmitting: false,
                formData: {
                    asset_id: '',
                    description: '',
                    priority: 'high',
                    maintenance_type: 'repair' // Tipe maintenance default adalah 'repair'
                },
                errors: {},

                init() {
                    const self = this;
                    $('#asset_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Aset Tetap yang Rusak --'
                    }).on('change', function () {
                        self.formData.asset_id = $(this).val();
                    });

                    $('#priority').select2({
                        theme: "classic",
                        width: '100%'
                    }).on('change', function () {
                        self.formData.priority = $(this).val();
                    }).val('high').trigger('change'); // Set nilai default
                },

                save() {
                    this.isSubmitting = true;
                    this.errors = {};

                    axios.post('/api/maintenances', this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Laporan kerusakan berhasil dibuat dan tugas perbaikan telah dibuat!');
                        window.location.href = "{{ route('master.maintenances.index') }}";
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan. Pastikan semua field terisi dengan benar.';
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                            msg = 'Terdapat kesalahan pada input Anda. Silakan periksa kembali.';
                        } else if(error.response && error.response.data.message) {
                            msg = error.response.data.message;
                        }

                        window.iziToast.error({
                            title: 'Gagal!',
                            message: msg,
                            position: 'topRight',
                            timeout: 5000
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