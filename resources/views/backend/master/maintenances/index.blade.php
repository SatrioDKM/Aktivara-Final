<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-tools mr-2"></i>
            {{ __('Maintenance Aset') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="maintenancePage()" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Riwayat
                            Maintenance Aset Tetap</h3>
                        <a href="{{ route('master.maintenances.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Lapor Kerusakan
                        </a>
                    </div>

                    {{-- Panel Filter --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div wire:ignore>
                                <label for="statusFilter" class="sr-only">Filter Status</label>
                                <select id="statusFilter" class="w-full">
                                    <option value="">Semua Status</option>
                                    <option value="scheduled">Terjadwal</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div wire:ignore>
                                <label for="technicianFilter" class="sr-only">Filter Teknisi</label>
                                <select id="technicianFilter" class="w-full">
                                    <option value="">Semua Teknisi</option>
                                    @foreach ($data['technicians'] as $technician)
                                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <label for="search" class="sr-only">Pencarian</label>
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="search" id="search" x-model.debounce.500ms="search"
                                    placeholder="Cari aset atau laporan..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Aset</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Laporan Kerusakan</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Teknisi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
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
                                <template x-if="!isLoading && maintenances.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak
                                            ada data ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="item in maintenances" :key="item.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100"
                                            x-text="item.asset ? item.asset.name_asset : 'Aset Dihapus'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="item.description ? (item.description.substring(0, 40) + (item.description.length > 40 ? '...' : '')) : ''">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                                :class="statusClass(item.status)"
                                                x-text="item.status.replace('_', ' ')"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="item.technician ? item.technician.name : '-'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="new Date(item.start_date || item.created_at).toLocaleDateString('id-ID')">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/maintenances/${item.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600"
                                                    title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/maintenances/${item.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-600"
                                                    title="Update Status"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(item.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center"
                        x-show="!isLoading && pagination.total > 0">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
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
        </div>
    </div>

    @push('scripts')
    <script>
        function maintenancePage() {
            return {
                maintenances: [],
                pagination: {},
                isLoading: true,
                search: '',
                statusFilter: '',
                technicianFilter: '',

                init() {
                    this.fetchMaintenances();
                    this.$watch('search', () => this.applyFilters());

                    const self = this;
                    $('#statusFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Status', allowClear: true }).on('change', function() {
                        self.statusFilter = $(this).val();
                        self.applyFilters();
                    });
                    $('#technicianFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Teknisi', allowClear: true }).on('change', function() {
                        self.technicianFilter = $(this).val();
                        self.applyFilters();
                    });

                    const toastMessage = sessionStorage.getItem('toastMessage');
                    if (toastMessage) {
                        window.iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                        sessionStorage.removeItem('toastMessage');
                    }
                },

                applyFilters() {
                    this.fetchMaintenances(1);
                },

                fetchMaintenances(page = 1) {
                    this.isLoading = true;
                    const params = new URLSearchParams({ page, perPage: 10, search: this.search, status: this.statusFilter, technician: this.technicianFilter });

                    axios.get(`/api/maintenances?${params.toString()}`)
                    .then(res => {
                        this.maintenances = res.data.data;
                        res.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = res.data;
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data maintenance:", error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil data dari server.', position: 'topRight' });
                    })
                    .finally(() => this.isLoading = false);
                },

                changePage(url) {
                    if (!url) return;
                    this.fetchMaintenances(new URL(url).searchParams.get('page'));
                },

                statusClass(status) {
                    const colors = {
                        'scheduled': 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                    };
                    return colors[status] || 'bg-gray-100';
                },

                confirmDelete(id) {
                    window.iziToast.question({
                        timeout: 20000, close: false, overlay: true,
                        title: 'Konfirmasi Hapus', message: 'Anda yakin ingin menghapus data maintenance ini?', position: 'center',
                        buttons: [
                            ['<button><b>YA, HAPUS</b></button>', (instance, toast) => {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                this.deleteMaintenance(id);
                            }, true],
                            ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                        ]
                    });
                },

                deleteMaintenance(id) {
                    axios.delete(`/api/maintenances/${id}`)
                    .then(res => {
                        window.iziToast.success({ title: 'Berhasil', message: res.data.message || 'Data maintenance telah dihapus.', position: 'topRight' });
                        const isLastItemOnPage = this.maintenances.length === 1 && this.pagination.current_page > 1;
                        this.fetchMaintenances(isLastItemOnPage ? this.pagination.current_page - 1 : this.pagination.current_page);
                    })
                    .catch(err => window.iziToast.error({ title: 'Gagal', message: err.response?.data?.message || 'Gagal menghapus data.', position: 'topRight' }));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>