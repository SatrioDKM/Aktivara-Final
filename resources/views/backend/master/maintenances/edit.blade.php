<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-edit mr-2"></i>
            {{ __('Update Status Maintenance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="maintenanceForm()" x-init="initData(@js($data['maintenance']))" x-cloak>

                    {{-- Informasi Aset & Kerusakan --}}
                    <div class="border-b dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Aset:</strong>
                            <span x-text="formData.asset.name_asset"></span>
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Kerusakan:</strong>
                            <span x-text="formData.description"></span>
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Tanggal Lapor:</strong>
                            <span
                                x-text="new Date(formData.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                        </p>
                    </div>

                    <form @submit.prevent="save()">
                        <div class="space-y-6">
                            {{-- Ubah Status --}}
                            <div wire:ignore>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubah Status <span
                                        class="text-red-500">*</span></label>
                                <select id="status" class="mt-1 block w-full" required>
                                    <option value="scheduled">Terjadwal</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                                <template x-if="errors.status">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.status[0]"></p>
                                </template>
                            </div>

                            {{-- Teknisi yang Mengerjakan --}}
                            <div wire:ignore>
                                <label for="user_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teknisi yang
                                    Mengerjakan</label>
                                <select id="user_id" class="mt-1 block w-full">
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach ($data['technicians'] as $technician)
                                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                                <template x-if="errors.user_id">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.user_id[0]"></p>
                                </template>
                            </div>

                            {{-- Catatan Perbaikan --}}
                            <div>
                                <label for="notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Perbaikan
                                    (Opsional)</label>
                                <div class="relative mt-1">
                                    <div class="absolute top-3 left-0 ps-3 flex items-start pointer-events-none">
                                        <i class="fas fa-sticky-note text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.notes" id="notes" rows="4"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Penggantian kompresor berhasil dilakukan. Aset sudah berfungsi normal."></textarea>
                                </div>
                                <template x-if="errors.notes">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.notes[0]"></p>
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
        function maintenanceForm() {
            return {
                isSubmitting: false,
                formData: {
                    id: null,
                    status: '',
                    notes: '',
                    user_id: null,
                    asset: {},
                    description: '',
                    start_date: ''
                },
                errors: {},

                initData(maintenance) {
                    this.formData = { ...maintenance, notes: maintenance.notes || '' };

                    this.$nextTick(() => {
                        const self = this;
                        $('#status').val(this.formData.status).trigger('change').on('change', function() {
                            self.formData.status = $(this).val();
                        });
                        $('#user_id').val(this.formData.user_id).trigger('change').on('change', function() {
                            self.formData.user_id = $(this).val();
                        });

                        $('#status, #user_id').select2({ theme: "classic", width: '100%' });
                    });
                },

                save() {
                    this.isSubmitting = true;
                    this.errors = {};

                    // Pastikan nilai terbaru dari Select2 diambil sebelum mengirim
                    this.formData.status = $('#status').val();
                    this.formData.user_id = $('#user_id').val();

                    axios.put(`/api/maintenances/${this.formData.id}`, this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', 'Status maintenance berhasil diperbarui!');
                        window.location.href = "{{ route('master.maintenances.index') }}";
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan. Periksa kembali isian Anda.';
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                            msg = 'Terdapat kesalahan pada input Anda.';
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