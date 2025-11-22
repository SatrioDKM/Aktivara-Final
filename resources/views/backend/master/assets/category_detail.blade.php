<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data="{ viewMode: 'groups', selectedAssetName: '' }">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Aset Tetap - Kategori: {{ $category->name }}
                </h2>
                <nav class="text-sm">
                    <a href="{{ route('master.assets.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Daftar Aset</a>
                    <span class="text-gray-500 mx-1">/</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="assetsCategoryPage( {{ $category->id }} )" x-cloak>
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Kembali Dinamis --}}
                    <div class="mb-6">
                        <button @click="handleBack()"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span x-text="viewMode === 'list' ? 'Kembali ke Grup' : 'Kembali ke Kategori'"></span>
                        </button>
                    </div>

                    {{-- Mode Grup --}}
                    <div x-show="viewMode === 'groups'">
                        <h3 class="text-lg font-bold mb-4">Pilih Grup Aset</h3>
                        
                        <template x-if="isLoadingGroups">
                            <div class="text-center py-10">
                                <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                            </div>
                        </template>

                        <template x-if="!isLoadingGroups && groups.length === 0">
                            <div class="text-center py-10 text-gray-500">
                                Tidak ada aset dalam kategori ini.
                            </div>
                        </template>

                        <div x-show="!isLoadingGroups && groups.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <template x-for="group in groups" :key="group.name_asset">
                                <div @click="selectGroup(group.name_asset)" 
                                    class="cursor-pointer p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-700 dark:to-gray-800 shadow-lg rounded-lg hover:shadow-xl hover:scale-105 transition-all duration-200 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-2" x-text="group.name_asset"></h3>
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    <i class="fas fa-box mr-2"></i>
                                                    <span x-text="group.total + ' Unit'"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400 text-xl"></i>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Mode List --}}
                    <div x-show="viewMode === 'list'">
                        <h3 class="text-lg font-bold mb-4">
                            Detail Aset: <span class="text-indigo-600 dark:text-indigo-400" x-text="selectedAssetName"></span>
                        </h3>

                        {{-- Filter Status --}}
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                <button @click="filterStatus = ''; applyFilters()"
                                    :class="filterStatus === '' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                    <i class="fas fa-list mr-2"></i> Semua
                                </button>
                                <button @click="filterStatus = 'available'; applyFilters()"
                                    :class="filterStatus === 'available' ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                    <i class="fas fa-check-circle mr-2"></i> Di Gudang
                                </button>
                                <button @click="filterStatus = 'in_use'; applyFilters()"
                                    :class="filterStatus === 'in_use' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                    <i class="fas fa-wrench mr-2"></i> Dipakai
                                </button>
                                <button @click="filterStatus = 'maintenance'; applyFilters()"
                                    :class="filterStatus === 'maintenance' ? 'bg-yellow-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                    <i class="fas fa-tools mr-2"></i> Maintenance
                                </button>
                                <button @click="filterStatus = 'disposed'; applyFilters()"
                                    :class="filterStatus === 'disposed' ? 'bg-red-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Disposed / Keluar
                                </button>
                            </div>
                        </div>

                        {{-- Panel Filter --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex items-center gap-4">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search"
                                    placeholder="Cari S/N aset..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Tabel Aset --}}
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">S/N</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lokasi</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="5" class="text-center py-10"><i
                                                    class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && assets.length === 0">
                                        <tr>
                                            <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada data
                                                ditemukan.</td>
                                        </tr>
                                    </template>
                                    <template x-for="(asset, index) in assets" :key="asset.id">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                            <td class="px-6 py-4 font-medium">
                                                <div x-text="asset.serial_number || 'Non-Serial'"></div>
                                            </td>
                                            <td class="px-6 py-4" x-text="asset.room ? asset.room.name_room : 'Gudang'"></td>
                                            <td class="px-6 py-4 text-center"><span
                                                    class="px-3 py-1 text-xs capitalize font-semibold rounded-full"
                                                    :class="assetStatusClass(asset.status)"
                                                    x-text="asset.status.replace('_', ' ')"></span></td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a :href="`/master/assets/${asset.id}`"
                                                        class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600"
                                                        title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a :href="`/master/assets/${asset.id}/edit`"
                                                        class="p-2 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-600"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @role('SA00', 'MG00')
                                                    <button @click="confirmDelete(asset.id)"
                                                        class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endrole
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
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">Menampilkan <span
                                    x-text="pagination.from || 0"></span> sampai <span x-text="pagination.to || 0"></span>
                                dari <span x-text="pagination.total || 0"></span> entri</p>
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
    </div>

    @push('scripts')
    <script>
        function assetsCategoryPage(categoryId) {
            return {
                categoryId: categoryId,
                viewMode: 'groups', // 'groups' | 'list'
                selectedAssetName: '',
                
                // Data untuk mode groups
                groups: [],
                isLoadingGroups: true,

                // Data untuk mode list
                assets: [],
                pagination: {},
                isLoading: false,
                search: '',
                filterStatus: '', // Filter status untuk detail aset

                init() {
                    this.fetchGroups();
                    this.$watch('search', () => this.applyFilters());
                },

                fetchGroups() {
                    this.isLoadingGroups = true;
                    axios.get(`/api/assets/by-category/${this.categoryId}/groups`)
                    .then(res => {
                        this.groups = res.data;
                    })
                    .catch(error => {
                        console.error("Gagal mengambil grup aset:", error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil data grup.', position: 'topRight' });
                    })
                    .finally(() => this.isLoadingGroups = false);
                },

                selectGroup(assetName) {
                    this.selectedAssetName = assetName;
                    this.viewMode = 'list';
                    this.search = ''; // Reset search
                    this.filterStatus = ''; // Reset filter status
                    this.fetchAssets(1);
                },

                handleBack() {
                    if (this.viewMode === 'list') {
                        this.viewMode = 'groups';
                        this.selectedAssetName = '';
                        this.filterStatus = ''; // Reset filter
                    } else {
                        window.location.href = '{{ route('master.assets.index') }}';
                    }
                },

                applyFilters() {
                    if (this.viewMode === 'list') {
                        this.fetchAssets(1);
                    }
                },

                fetchAssets(page = 1) {
                    this.isLoading = true;
                    const apiUrl = `/api/assets/by-category/${this.categoryId}`;
                    
                    const params = new URLSearchParams({ 
                        page, 
                        perPage: 10, 
                        search: this.search,
                        name_asset: this.selectedAssetName // Filter berdasarkan nama aset yang dipilih
                    });

                    // Tambahkan filter status jika ada
                    if (this.filterStatus) {
                        params.append('status', this.filterStatus);
                    }

                    axios.get(`${apiUrl}?${params.toString()}`)
                    .then(res => {
                        this.assets = res.data.data;
                        res.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = res.data;
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data aset:", error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil data aset dari server.', position: 'topRight' });
                    })
                    .finally(() => this.isLoading = false);
                },

                changePage(url) {
                    if (!url) return;
                    this.fetchAssets(new URL(url).searchParams.get('page'));
                },

                assetStatusClass(status) {
                    const colors = {
                        'available': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'in_use': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'maintenance': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'disposed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                    };
                    return colors[status] || 'bg-gray-100';
                },

                confirmDelete(id) {
                    window.iziToast.question({
                        timeout: 20000, close: false, overlay: true, title: 'Konfirmasi Hapus', message: 'Apakah Anda yakin ingin menghapus data aset ini?', position: 'center',
                        buttons: [
                            ['<button><b>YA, HAPUS</b></button>', (instance, toast) => {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                this.deleteAsset(id);
                            }, true],
                            ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                        ]
                    });
                },

                deleteAsset(id) {
                    axios.delete(`/api/assets/${id}`)
                    .then(res => {
                        window.iziToast.success({ title: 'Berhasil', message: res.data.message || 'Data aset telah dihapus.', position: 'topRight' });
                        const isLastItemOnPage = this.assets.length === 1 && this.pagination.current_page > 1;
                        this.fetchAssets(isLastItemOnPage ? this.pagination.current_page - 1 : this.pagination.current_page);
                    })
                    .catch(err => window.iziToast.error({ title: 'Gagal', message: err.response?.data?.message || 'Gagal menghapus data.', position: 'topRight' }));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>