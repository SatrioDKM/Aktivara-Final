<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Maintenance Aset') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="maintenancePage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Riwayat
                            Maintenance Aset Tetap</h3>
                        <a href="{{ route('master.maintenances.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-700 focus:ring ring-red-300">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Lapor Kerusakan
                        </a>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select id="statusFilter" class="w-full">
                                <option value="">Semua Status</option>
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Dikerjakan</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                            <select id="technicianFilter" class="w-full">
                                <option value="">Semua Teknisi</option>
                                @foreach ($data['technicians'] as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                @endforeach
                            </select>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search"
                                    placeholder="Cari aset atau laporan..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aset
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laporan
                                        Kerusakan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teknisi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && maintenances.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="item in maintenances" :key="item.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium"
                                            x-text="item.asset ? item.asset.name_asset : 'Aset Dihapus'"></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"
                                            x-text="item.description ? (item.description.substring(0, 40) + (item.description.length > 40 ? '...' : '')) : ''">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                                :class="statusClass(item.status)"
                                                x-text="item.status.replace('_', ' ')"></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500"
                                            x-text="item.technician ? item.technician.name : '-'"></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"
                                            x-text="new Date(item.start_date || item.created_at).toLocaleDateString('id-ID')">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/maintenances/${item.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200"
                                                    title="Lihat"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/maintenances/${item.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(item.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100"
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
        function maintenancePage() {
                return {
                    maintenances: [], pagination: {}, isLoading: true,
                    search: '', statusFilter: '', technicianFilter: '',
                    init() {
                        this.fetchMaintenances();
                        this.$watch('search', () => this.applyFilters());

                        $('#statusFilter, #technicianFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Opsi', allowClear: true });
                        $('#statusFilter').on('change', (e) => this.statusFilter = e.target.value);
                        $('#technicianFilter').on('change', (e) => this.technicianFilter = e.target.value);

                        this.$watch('statusFilter', () => this.applyFilters());
                        this.$watch('technicianFilter', () => this.applyFilters());

                        const toastMessage = sessionStorage.getItem('toastMessage');
                        if (toastMessage) {
                            iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                            sessionStorage.removeItem('toastMessage');
                        }
                    },
                    applyFilters() { this.fetchMaintenances(1); },
                    fetchMaintenances(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({ page, perPage: 10, search: this.search, status: this.statusFilter, technician: this.technicianFilter });
                        fetch(`/api/maintenances?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.maintenances = data.data;
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
                        this.fetchMaintenances(new URL(url).searchParams.get('page'));
                    },
                    statusClass(status) {
                        const colors = { scheduled: 'bg-gray-100 text-gray-800', in_progress: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' };
                        return colors[status] || 'bg-gray-100';
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    confirmDelete(id) {
                        iziToast.question({
                            timeout: 20000, close: false, overlay: true,
                            title: 'Konfirmasi Hapus', message: 'Anda yakin ingin menghapus data ini?', position: 'center',
                            buttons: [
                                ['<button><b>YA</b></button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    this.deleteMaintenance(id);
                                }, true],
                                ['<button>Batal</button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                }],
                            ]
                        });
                    },
                    async deleteMaintenance(id) {
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/maintenances/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                        })
                        .then(response => {
                            if (response.ok) {
                                iziToast.success({ title: 'Berhasil', message: 'Data maintenance telah dihapus.', position: 'topRight' });
                                this.fetchMaintenances(this.pagination.current_page);
                            } else { throw new Error('Gagal menghapus data.'); }
                        })
                        .catch(error => iziToast.error({ title: 'Gagal', message: error.message, position: 'topRight' }));
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>