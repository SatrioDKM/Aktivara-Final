<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pengguna: ') . $data['user']->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                {{-- ================== BAGIAN YANG DIPERBARUI ================== --}}
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100" x-data="userForm()"
                    x-init="initData(@js($data['user']))">
                    {{-- ========================================================== --}}
                    <form @submit.prevent="saveUser()">
                        <div class="space-y-6">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Lengkap</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i></div>
                                    <input type="text" x-model="formData.name" id="name"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nama lengkap pengguna" required>
                                </div>
                            </div>
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat
                                    Email</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i></div>
                                    <input type="email" x-model="formData.email" id="email"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="alamat@email.com" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="role_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Peran
                                        (Role)</label>
                                    <select id="role_id" class="mt-1 block w-full" required>
                                        @foreach ($data['roles'] as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru
                                        (Opsional)</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i></div>
                                        <input type="password" x-model="formData.password" id="password"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Isi untuk mengubah">
                                    </div>
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi
                                        Password</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i></div>
                                        <input type="password" x-model="formData.password_confirmation"
                                            id="password_confirmation"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Ketik ulang password">
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kosongkan password jika tidak ingin
                                mengubahnya.</p>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}"
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
        function userForm() {
                return {
                    isSubmitting: false,
                    formData: { id: null, name: '', email: '', role_id: '', status: '', password: '', password_confirmation: '' },

                    // ================== BAGIAN YANG DIPERBARUI ==================
                    initData(user) {
                        this.formData = {
                            id: user.id, name: user.name, email: user.email, role_id: user.role_id,
                            status: user.status, password: '', password_confirmation: ''
                        };
                        this.$nextTick(() => {
                            $('#role_id').val(this.formData.role_id).trigger('change');
                            $('#status').val(this.formData.status).trigger('change');
                            $('#role_id, #status').select2({ theme: "classic", width: '100%' });
                        });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async saveUser() {
                        this.isSubmitting = true;
                        let dataToSave = { ...this.formData };
                        dataToSave.role_id = $('#role_id').val();
                        dataToSave.status = $('#status').val();

                        if (!dataToSave.password) {
                            delete dataToSave.password;
                            delete dataToSave.password_confirmation;
                        }

                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/users/${dataToSave.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-XSRF-TOKEN': this.getCsrfToken()
                            },
                            body: JSON.stringify(dataToSave)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Data pengguna berhasil diperbarui!');
                            window.location.href = "{{ route('users.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa kembali isian Anda.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                        })
                        .finally(() => this.isSubmitting = false);
                    }
                    // ==========================================================
                }
            }
    </script>
    @endpush
</x-app-layout>