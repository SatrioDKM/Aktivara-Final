<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Jenis Tugas: ') . $data['taskType']->name_task }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="taskTypeForm()" x-init="initData(@js($data['taskType']))">
                    <form @submit.prevent="save()">
                        <div class="space-y-6">
                            <div>
                                <label for="name_task"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Tugas</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-clipboard-list text-gray-400"></i></div>
                                    <input type="text" x-model="formData.name_task" id="name_task"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Pembersihan Rutin Kamar" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="departemen"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen</label>
                                    <select id="departemen" class="mt-1 block w-full" required>
                                        <option value="HK">Housekeeping</option>
                                        <option value="TK">Teknisi</option>
                                        <option value="SC">Security</option>
                                        <option value="PK">Parking</option>
                                        <option value="WH">Warehouse</option>
                                        <option value="UMUM">Umum</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="priority_level"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas
                                        Default</label>
                                    <select id="priority_level" class="mt-1 block w-full" required>
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                        <option value="critical">Kritis</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                    (Opsional)</label>
                                <textarea x-model="formData.description" id="description" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Jelaskan secara singkat tentang jenis tugas ini"></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.task_types.index') }}"
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
        function taskTypeForm() {
                return {
                    isSubmitting: false,
                    formData: { id: null, name_task: '', departemen: '', priority_level: '', description: '' },
                    initData(taskType) {
                        this.formData = { ...taskType };
                        this.$nextTick(() => {
                            $('#departemen').val(this.formData.departemen).trigger('change');
                            $('#priority_level').val(this.formData.priority_level).trigger('change');
                            $('#departemen, #priority_level').select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity });
                        });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.departemen = $('#departemen').val();
                        this.formData.priority_level = $('#priority_level').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/task-types/${this.formData.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Data jenis tugas berhasil diperbarui!');
                            window.location.href = "{{ route('master.task_types.index') }}";
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