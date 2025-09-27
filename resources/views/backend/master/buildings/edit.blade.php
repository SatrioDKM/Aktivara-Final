<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Gedung: ') . $data['building']->name_building }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100" x-data="buildingForm()"
                    x-init="initData(@js($data['building']))">
                    <form @submit.prevent="saveBuilding()">
                        <div class="space-y-6">
                            <div>
                                <label for="name_building"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Gedung</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-building text-gray-400"></i></div>
                                    <input type="text" x-model="formData.name_building" id="name_building"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Gedung Tower A" required>
                                </div>
                            </div>
                            <div>
                                <label for="address"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat
                                    (Opsional)</label>
                                <div class="relative mt-1">
                                    <div
                                        class="absolute inset-y-0 start-0 pt-3 flex items-start ps-3 pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i></div>
                                    <textarea x-model="formData.address" id="address" rows="3"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Jl. Jend. Sudirman Kav. 52-53"></textarea>
                                </div>
                            </div>
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" class="mt-1 block w-full" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.buildings.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting"><span
                                    x-show="!isSubmitting">Simpan Perubahan</span><span
                                    x-show="isSubmitting">Menyimpan...</span></x-primary-button>
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
        function buildingForm() {
                return {
                    isSubmitting: false,
                    formData: { id: null, name_building: '', address: '', status: '' },
                    initData(building) {
                        this.formData = {
                            id: building.id,
                            name_building: building.name_building,
                            address: building.address,
                            status: building.status
                        };
                        this.$nextTick(() => {
                            $('#status').val(this.formData.status).trigger('change');
                            $('#status').select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity });
                        });
                    },
                    // ================== BAGIAN YANG DIPERBARUI ==================
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    // ==========================================================
                    async saveBuilding() {
                        this.isSubmitting = true;
                        this.formData.status = $('#status').val();
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/buildings/${this.formData.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-XSRF-TOKEN': this.getCsrfToken()
                            },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Data gedung berhasil diperbarui!');
                            window.location.href = "{{ route('master.buildings.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa isian Anda.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight' });
                            this.isSubmitting = false;
                        });
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>