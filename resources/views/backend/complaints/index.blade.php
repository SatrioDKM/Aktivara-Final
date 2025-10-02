<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ __('Laporan Masuk & Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="complaintsPage(@js($data['taskTypes']))" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Aksi dan Tombol Tambah --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Daftar Laporan Masuk
                </h3>
                <a href="{{ route('complaints.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus me-2"></i>
                    Catat Laporan Baru
                </a>
            </div>

            {{-- Panel Filter --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select x-model="statusFilter"
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
                            placeholder="Cari judul atau nama pelapor..."
                            class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            {{-- Indikator Loading --}}
            <div x-show="isLoading" class="text-center py-10">
                <i class="fas fa-spinner fa-spin fa-3x text-gray-400"></i>
            </div>

            {{-- Pesan Data Kosong --}}
            <div x-show="!isLoading && complaints.length === 0"
                class="text-center py-10 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Tidak ada data laporan yang cocok dengan filter Anda.
                </p>
            </div>

            {{-- Grid untuk Card Laporan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!isLoading">
                <template x-for="item in complaints" :key="item.id">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border-l-4"
                        :class="statusBorderClass(item.status)">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <p class="text-sm text-gray-500 dark:text-gray-400"
                                    x-text="`Dicatat oleh: ${item.creator ? item.creator.name : 'N/A'}`"></p>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusClass(item.status)" x-text="statusText(item.status)"></span>
                            </div>
                            <a :href="`/complaints/${item.id}`" class="block mt-2">
                                <p class="text-xl font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 truncate"
                                    x-text="item.title" :title="item.title"></p>
                            </a>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pelapor: <strong
                                    x-text="item.reporter_name"></strong></p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate"
                                x-text="`Lokasi: ${item.location_text}`"></p>
                        </div>
                        <div
                            class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-t dark:border-gray-700 flex justify-end items-center space-x-2">
                            <a :href="`/complaints/${item.id}`"
                                class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                title="Lihat Detail"><i class="fas fa-eye"></i></a>
                            <template x-if="item.status === 'open'">
                                <div>
                                    <button @click="openConversionModal(item)"
                                        class="p-2 rounded-full text-green-500 hover:bg-green-100 dark:hover:bg-gray-600 transition"
                                        title="Konversi Jadi Tugas"><i class="fas fa-retweet"></i></button>
                                    <button @click="confirmDelete(item.id)"
                                        class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600 transition"
                                        title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Paginasi --}}
            <div class="mt-6" x-show="!isLoading && pagination.last_page > 1">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                        Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                            x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span> entri
                    </p>
                    <nav class="flex items-center space-x-1">
                        <template x-for="link in pagination.links">
                            <button @click="changePage(link.url)" :disabled="!link.url"
                                :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'bg-white dark:bg-gray-800 text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                                class="px-3 py-2 rounded-md text-sm font-medium transition border dark:border-gray-600"
                                x-html="link.label"></button>
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Modal Konversi Laporan menjadi Tugas --}}
        <div x-show="showConversionModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
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
                                        Tugas <span class="text-red-500">*</span></label>
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas
                                        <span class="text-red-500">*</span></label>
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
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Konversi</span>
                                <span x-show="isSubmitting">Memproses...</span>
                            </x-primary-button>
                            <x-secondary-button type="button" @click="showConversionModal = false" class="me-3">Batal
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                        window.iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                        sessionStorage.removeItem('toastMessage');
                    }
                },
                applyFilters() {
                    this.fetchComplaints(1);
                },
                fetchComplaints(page = 1) {
                    this.isLoading = true;
                    const params = new URLSearchParams({ page, perPage: 9, search: this.search, status: this.statusFilter });

                    axios.get(`/api/complaints?${params.toString()}`)
                    .then(res => {
                        this.complaints = res.data.data;
                        res.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = res.data;
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data laporan:", error);
                        window.iziToast.error({ title: 'Gagal!', message: 'Tidak dapat mengambil data laporan dari server.', position: 'topRight' });
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },
                changePage(url) {
                    if (!url) return;
                    this.fetchComplaints(new URL(url).searchParams.get('page'));
                },
                openConversionModal(item) {
                    this.conversionData = { id: item.id, title: item.title, task_type_id: '', priority: 'medium' };
                    this.showConversionModal = true;
                },
                convertItem() {
                    this.isSubmitting = true;
                    const payload = {
                        task_type_id: this.conversionData.task_type_id,
                        priority: this.conversionData.priority
                    };
                    axios.post(`/api/complaints/${this.conversionData.id}/convert`, payload)
                    .then(res => {
                        window.iziToast.success({ title: 'Berhasil!', message: res.data.message, position: 'topRight' });
                        this.fetchComplaints(this.pagination.current_page);
                        this.showConversionModal = false;
                    })
                    .catch(err => window.iziToast.error({ title: 'Gagal!', message: err.response?.data?.message || 'Gagal mengonversi laporan.', position: 'topRight' }))
                    .finally(() => this.isSubmitting = false);
                },
                confirmDelete(id) {
                    window.iziToast.question({
                        timeout: 20000, close: false, overlay: true,
                        title: 'Konfirmasi Hapus', message: 'Apakah Anda yakin ingin menghapus laporan ini?', position: 'center',
                        buttons: [
                            ['<button><b>YA, HAPUS</b></button>', (instance, toast) => {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                this.deleteComplaint(id);
                            }, true],
                            ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                        ]
                    });
                },
                deleteComplaint(id) {
                    axios.delete(`/api/complaints/${id}`)
                    .then(res => {
                        window.iziToast.success({ title: 'Berhasil', message: res.data.message || 'Laporan telah dihapus.', position: 'topRight' });
                        this.fetchComplaints(this.pagination.current_page);
})
                    .catch(err => window.iziToast.error({ title: 'Gagal', message: err.response?.data?.message || 'Gagal menghapus data.', position: 'topRight' }));
                },
                statusClass(status) {
                    const colors = { open: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300', converted_to_task: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' };
                    return colors[status] || 'bg-gray-100';
                },
                statusBorderClass(status) {
                    const colors = { open: 'border-yellow-500', converted_to_task: 'border-blue-500', closed: 'border-gray-400' };
                    return colors[status] || 'border-gray-300';
                },
                statusText(status) {
                    return { open: 'Terbuka', converted_to_task: 'Jadi Tugas', closed: 'Ditutup' }[status] || status;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>