<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Maintenance: ') . $data['maintenance']->asset->name_asset }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="maintenanceForm()" x-init="initData(@js($data['maintenance']))">
                    <div class="border-b dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><strong>Aset:</strong> <span
                                x-text="formData.asset.name_asset"></span></p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"><strong>Kerusakan:</strong> <span
                                x-text="formData.description"></span></p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"><strong>Tanggal Lapor:</strong> <span
                                x-text="new Date(formData.start_date).toLocaleDateString('id-ID')"></span></p>
                    </div>
                    <form @submit.prevent="save()">
                        <div class="space-y-6">
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubah
                                    Status</label>
                                <select id="status" class="mt-1 block w-full" required>
                                    <option value="scheduled">Terjadwal</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label for="user_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teknisi yang
                                    Mengerjakan</label>
                                <select id="user_id" class="mt-1 block w-full">
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach ($data['technicians'] as $technician)
                                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Perbaikan
                                    (Opsional)</label>
                                <textarea x-model="formData.notes" id="notes" rows="4"
                                    class="mt-1 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700"
                                    placeholder="Contoh: Penggantian kompresor berhasil dilakukan."></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.maintenances.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Simpan Perubahan</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function maintenanceForm() {
                return {
                    isSubmitting: false,
                    formData: { id: null, status: '', notes: '', user_id: null, asset: {}, description: '', start_date: '' },
                    initData(maintenance) {
                        this.formData = { ...maintenance, notes: maintenance.notes || '' };
                        this.$nextTick(() => {
                            $('#status').val(this.formData.status).trigger('change');
                            $('#user_id').val(this.formData.user_id).trigger('change');
                            $('#status, #user_id').select2({ theme: "classic", width: '100%' });
                        });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.status = $('#status').val();
                        this.formData.user_id = $('#user_id').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/maintenances/${this.formData.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Status maintenance berhasil diperbarui!');
                            window.location.href = "{{ route('master.maintenances.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa isian Anda.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                            this.isSubmitting = false;
                        });
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>