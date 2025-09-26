<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="usersPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Tambah Pengguna --}}
                    <div class="flex justify-end mb-4">
                        <x-primary-button @click="openModal()">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Pengguna
                        </x-primary-button>
                    </div>

                    {{-- Card Filter & Pencarian --}}
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label for="perPage" class="text-sm text-gray-600 dark:text-gray-300">Tampilkan</label>
                            <select x-model="perPage" id="perPage"
                                class="w-20 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select id="roleFilter" class="w-full">
                                <option value="">Semua Peran</option>
                                @foreach ($data['roles'] as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <select id="statusFilter" class="w-full">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search"
                                    placeholder="Cari nama atau email..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Peran</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && users.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="(user, index) in users" :key="user.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="user.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="user.email"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="user.role ? user.role.role_name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                                x-text="user.status === 'active' ? 'Aktif' : 'Tidak Aktif'">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="editUser(user)"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-700 transition"
                                                    title="Edit"><i class="fas fa-edit fa-lg"></i></button>
                                                <button @click="confirmDelete(user.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-700 transition"
                                                    title="Hapus"><i class="fas fa-trash fa-lg"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                            <template x-for="link in pagination.links">
                                <button @click="changePage(link.url)" :disabled="!link.url" :class="{
                                            'bg-indigo-600 text-white': link.active,
                                            'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-600': !link.active && link.url,
                                            'text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url
                                        }" class="px-3 py-2 rounded-md text-sm font-medium transition"
                                    x-html="link.label">
                                </button>
                            </template>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div x-show="showModal" x-transition x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form @submit.prevent="saveUser()">
                    <div class="p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4"
                            x-text="isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label for="modal-name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Lengkap</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i></div>
                                    <input type="text" x-model="formData.name" id="modal-name"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Nama lengkap pengguna" required>
                                </div>
                            </div>
                            <div>
                                <label for="modal-email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat
                                    Email</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i></div>
                                    <input type="email" x-model="formData.email" id="modal-email"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="alamat@email.com" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="modal-role_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Peran
                                        (Role)</label>
                                    <select x-model="formData.role_id" id="modal-role_id" class="mt-1 block w-full"
                                        required>
                                        <option value="">-- Pilih Peran --</option>
                                        @foreach ($data['roles'] as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="modal-status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select x-model="formData.status" id="modal-status" class="mt-1 block w-full"
                                        required>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="modal-password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i></div>
                                        <input type="password" x-model="formData.password" id="modal-password"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Wajib untuk user baru">
                                    </div>
                                </div>
                                <div>
                                    <label for="modal-password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi
                                        Password</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i></div>
                                        <input type="password" x-model="formData.password_confirmation"
                                            id="modal-password_confirmation"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Ketik ulang password">
                                    </div>
                                </div>
                            </div>
                            <p x-show="isEditMode" class="text-xs text-gray-500 dark:text-gray-400">Kosongkan password
                                jika tidak ingin mengubahnya.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-primary-button type="submit" ::disabled="isSubmitting">
                            <span x-show="!isSubmitting">Simpan</span>
                            <span x-show="isSubmitting">Menyimpan...</span>
                        </x-primary-button>
                        <x-secondary-button type="button" @click="closeModal()" class="me-3">Batal</x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    {{-- iziToast & Select2 CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    {{-- iziToast & Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function usersPage() {
                return {
                    users: [],
                    pagination: {},
                    isLoading: true,
                    showModal: false,
                    isEditMode: false,
                    isSubmitting: false,
                    formData: { id: null, name: '', email: '', role_id: '', status: 'active', password: '', password_confirmation: '' },

                    perPage: 10,
                    search: '',
                    roleFilter: '',
                    statusFilter: '',

                    init() {
                        this.fetchUsers();
                        this.$watch('perPage', () => this.applyFilters());
                        this.$watch('search', () => this.applyFilters());

                        $('#roleFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Peran', allowClear: true }).on('change', (e) => this.roleFilter = e.target.value);
                        $('#statusFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Status', allowClear: true }).on('change', (e) => this.statusFilter = e.target.value);
                        this.$watch('roleFilter', () => this.applyFilters());
                        this.$watch('statusFilter', () => this.applyFilters());

                        $('#modal-role_id').select2({ theme: "classic", width: '100%', dropdownParent: $('#userModal') });
                        $('#modal-status').select2({ theme: "classic", width: '100%', dropdownParent: $('#userModal') });
                    },
                    applyFilters() {
                        this.fetchUsers(1);
                    },
                    fetchUsers(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({
                            page: page,
                            perPage: this.perPage,
                            search: this.search,
                            role: this.roleFilter,
                            status: this.statusFilter
                        });

                        fetch(`/api/users?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                            .then(res => res.json())
                            .then(data => {
                                this.users = data.data;
                                data.links.forEach(link => {
                                    if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                                    if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                                });
                                this.pagination = data;
                                this.isLoading = false;
                            });
                    },
                    changePage(url) {
                        if (!url) return;
                        const pageNumber = new URL(url).searchParams.get('page');
                        this.fetchUsers(pageNumber);
                    },
                    openModal() {
                        this.isEditMode = false;
                        this.formData = { id: null, name: '', email: '', role_id: '', status: 'active', password: '', password_confirmation: '' };
                        // Reset Select2
                        $('#modal-role_id').val('').trigger('change');
                        $('#modal-status').val('active').trigger('change');
                        this.showModal = true;
                    },
                    editUser(user) {
                        this.isEditMode = true;
                        this.formData = { ...user, password: '', password_confirmation: '' };
                        // Set Select2 values
                        $('#modal-role_id').val(user.role_id).trigger('change');
                        $('#modal-status').val(user.status).trigger('change');
                        this.showModal = true;
                    },
                    closeModal() {
                        this.showModal = false;
                    },
                    async saveUser() {
                        this.isSubmitting = true;

                        let dataToSave = { ...this.formData };
                        dataToSave.role_id = $('#modal-role_id').val();
                        dataToSave.status = $('#modal-status').val();

                        if (this.isEditMode && !dataToSave.password) {
                            delete dataToSave.password;
                            delete dataToSave.password_confirmation;
                        }

                        const url = this.isEditMode ? `/api/users/${dataToSave.id}` : '/api/users';
                        const method = this.isEditMode ? 'PUT' : 'POST';

                        await fetch('/sanctum/csrf-cookie');
                        fetch(url, {
                            method: method,
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(dataToSave)
                        })
                        .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                        .then(data => {
                            iziToast.success({ title: 'Berhasil!', message: this.isEditMode ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan', position: 'topRight' });
                            this.fetchUsers(this.pagination.current_page);
                            this.closeModal();
                        })
                        .catch(err => {
                            let msg = 'Terjadi kesalahan.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                        })
                        .finally(() => {
                            this.isSubmitting = false;
                        });
                    },
                    confirmDelete(id) {
                        iziToast.question({
                            timeout: 20000, close: false, overlay: true, displayMode: 'once', id: 'question', zindex: 1050,
                            title: 'Konfirmasi Hapus',
                            message: 'Apakah Anda yakin ingin menghapus pengguna ini?',
                            position: 'center',
                            buttons: [
                                ['<button><b>YA, Hapus</b></button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    this.deleteUser(id);
                                }, true],
                                ['<button>Batal</button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                }],
                            ],
                        });
                    },
                    async deleteUser(id) {
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/users/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        })
                        .then(res => {
                            if (!res.ok) { throw new Error('Gagal menghapus pengguna.'); }
                            iziToast.success({ title: 'Berhasil!', message: 'Pengguna berhasil dihapus.', position: 'topRight' });
                            this.fetchUsers(this.pagination.current_page);
                        })
                        .catch(err => iziToast.error({ title: 'Gagal!', message: err.message, position: 'topRight' }))
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>