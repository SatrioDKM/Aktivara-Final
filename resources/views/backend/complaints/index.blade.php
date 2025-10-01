<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Masuk & Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="complaintsPage(@js($data['taskTypes']))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Daftar Laporan
                        </h3>
                        <a href="{{ route('complaints.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-plus me-2"></i>
                            Catat Laporan Baru
                        </a>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select id="statusFilter" x-model="statusFilter"
                                class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="open">Terbuka</option>
                                <option value="converted_to_task">Jadi Tugas</option>
                                <option value="closed">Ditutup</option>
                            </select>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search"
                                    placeholder="Cari judul atau pelapor..."
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
                                        Judul Laporan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pelapor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dicatat Oleh</th>
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
                                        <td colspan="5" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && complaints.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak
                                            ada data ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="item in complaints" :key="item.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="item.title"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="item.reporter_name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="item.creator ? item.creator.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="statusClass(item.status)"
                                                x-text="statusText(item.status)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/complaints/${item.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                                    title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                                <button @click="openConversionModal(item)"
                                                    x-show="item.status === 'open'"
                                                    class="p-2 rounded-full text-green-500 hover:bg-green-100 dark:hover:bg-gray-600 transition"
                                                    title="Konversi Jadi Tugas"><i class="fas fa-retweet"></i></button>
                                                <button @click="confirmDelete(item.id)" x-show="item.status === 'open'"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600 transition"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
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
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition"
                                    x-html="link.label"></button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showConversionModal" x-transition x-cloak style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div @click="showConversionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                    <form @submit.prevent="convertItem()">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Konversi Laporan
                                Menjadi Tugas</h3>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border dark:border-gray-600 mb-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Laporan: <strong
                                        class="text-gray-900 dark:text-gray-100" x-text="conversionData.title"></strong>
                                </p>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis
                                        Tugas</label>
                                    <select x-model="conversionData.task_type_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="">-- Pilih Jenis Tugas --</option>
                                        <template x-for="tt in taskTypes" :key="tt.id">
                                            <option :value="tt.id" x-text="`${tt.name_task} (${tt.departemen})`">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas</label>
                                    <select x-model="conversionData.priority"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                        <option value="critical">Kritis</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" ::disabled="isSubmitting"><span
                                    x-show="!isSubmitting">Konversi</span><span
                                    x-show="isSubmitting">Memproses...</span></x-primary-button>
                            <x-secondary-button type="button" @click="showConversionModal = false" class="me-3">Batal
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    {{-- jQuery dan Select2 tidak lagi dibutuhkan di halaman ini --}}

    <script>
        function complaintsPage(taskTypes) {
                return {
                    complaints: [],
                    pagination: {},
                    isLoading: true,
                    taskTypes: taskTypes,
                    search: '',
                    statusFilter: '',
                    showConversionModal: false,
                    isSubmitting: false,
                    conversionData: {},

                    init() {
                        this.fetchComplaints();
                        this.$watch('search', () => this.applyFilters());
                        this.$watch('statusFilter', () => this.applyFilters());

                        const toastMessage = sessionStorage.getItem('toastMessage');
                        if (toastMessage) {
                            iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                            sessionStorage.removeItem('toastMessage');
                        }
                    },
                    applyFilters() {
                        this.fetchComplaints(1);
                    },
                    fetchComplaints(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({ page, perPage: 10, search: this.search, status: this.statusFilter });
                        fetch(`/api/complaints?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.complaints = data.data;
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
                        this.fetchComplaints(new URL(url).searchParams.get('page'));
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    openConversionModal(item) {
                        this.conversionData = { id: item.id, title: item.title, task_type_id: '', priority: 'medium' };
                        this.showConversionModal = true;
                    },
                    async convertItem() {
                        this.isSubmitting = true;
                        const payload = {
                            task_type_id: this.conversionData.task_type_id,
                            priority: this.conversionData.priority
                        };
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/complaints/${this.conversionData.id}/convert`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            iziToast.success({ title: 'Berhasil!', message: data.message, position: 'topRight' });
                            this.fetchComplaints(this.pagination.current_page);
                            this.showConversionModal = false;
                        })
                        .catch(err => iziToast.error({ title: 'Gagal!', message: err.message || 'Gagal mengonversi laporan.', position: 'topRight' }))
                        .finally(() => this.isSubmitting = false);
                    },
                    confirmDelete(id) {
                        iziToast.question({
                            timeout: 20000, close: false, overlay: true,
                            title: 'Konfirmasi Hapus', message: 'Hanya laporan berstatus "Terbuka" yang bisa dihapus. Lanjutkan?', position: 'center',
                            buttons: [
                                ['<button><b>YA</b></button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    this.deleteComplaint(id);
                                }, true],
                                ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                            ]
                        });
                    },
                    async deleteComplaint(id) {
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/complaints/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                        })
                        .then(response => {
                            if (!response.ok) { return response.json().then(err => Promise.reject(err)); }
                            iziToast.success({ title: 'Berhasil', message: 'Laporan telah dihapus.', position: 'topRight' });
                            this.fetchComplaints(this.pagination.current_page);
                        })
                        .catch(error => iziToast.error({ title: 'Gagal', message: error.message || 'Gagal menghapus data.', position: 'topRight' }));
                    },
                    statusClass(status) {
                        const colors = { open: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300', converted_to_task: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' };
                        return colors[status] || 'bg-gray-100';
                    },
                    statusText(status) {
                        return { open: 'Terbuka', converted_to_task: 'Jadi Tugas', closed: 'Ditutup' }[status] || status;
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>