<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-user-plus mr-2"></i>
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100" x-data="userForm()" x-cloak>
                    <form @submit.prevent="saveUser()">
                        <div class="space-y-6">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap
                                    <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.name" id="name"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nama lengkap pengguna" required>
                                </div>
                                <template x-if="errors.name">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.name[0]"></p>
                                </template>
                            </div>

                            {{-- Alamat Email --}}
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Email
                                    <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" x-model="formData.email" id="email"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="alamat@email.com" required>
                                </div>
                                <template x-if="errors.email">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.email[0]"></p>
                                </template>
                            </div>

                            {{-- Peran & Status --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div wire:ignore>
                                    <label for="role_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Peran (Role)
                                        <span class="text-red-500">*</span></label>
                                    <select id="role_id" class="mt-1 block w-full" required>
                                        <option value=""></option>
                                        @foreach ($data['roles'] as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="errors.role_id">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.role_id[0]"></p>
                                    </template>
                                </div>
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

                            {{-- Password & Konfirmasi --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password
                                        <span class="text-red-500">*</span></label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" x-model="formData.password" id="password"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Minimal 8 karakter" required>
                                    </div>
                                    <template x-if="errors.password">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.password[0]"></p>
                                    </template>
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi
                                        Password <span class="text-red-500">*</span></label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" x-model="formData.password_confirmation"
                                            id="password_confirmation"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Ketik ulang password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('users.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Pengguna'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function userForm() {
            return {
                isSubmitting: false,
                formData: {
                    name: '',
                    email: '',
                    role_id: '',
                    status: 'active',
                    password: '',
                    password_confirmation: ''
                },
                errors: {},

                init() {
                    const self = this;

                    // Inisialisasi Select2 untuk Peran
                    $('#role_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Peran --'
                    }).on('change', function() {
                        self.formData.role_id = $(this).val();
                    });

                    // Inisialisasi Select2 untuk Status
                    $('#status').select2({
                        theme: "classic",
                        width: '100%'
                    }).on('change', function() {
                        self.formData.status = $(this).val();
                    });
                },

                saveUser() {
                    this.isSubmitting = true;
                    this.errors = {}; // Bersihkan error validasi sebelumnya

                    // Kirim data ke API menggunakan Axios
                    axios.post('/api/users', this.formData)
                        .then(response => {
                            // Simpan pesan sukses di session storage untuk ditampilkan setelah redirect
                            sessionStorage.setItem('toastMessage', 'Pengguna baru berhasil ditambahkan!');
                            // Arahkan ke halaman index
                            window.location.href = "{{ route('users.index') }}";
                        })
                        .catch(error => {
                            let errorMessage = 'Gagal menyimpan. Silakan periksa kembali isian Anda.';
                            if (error.response && error.response.status === 422) {
                                // Tangani error validasi dari Laravel
                                this.errors = error.response.data.errors;
                                errorMessage = 'Terdapat kesalahan pada input Anda.';
                            } else if (error.response && error.response.data.message) {
                                // Tangani error server lainnya
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