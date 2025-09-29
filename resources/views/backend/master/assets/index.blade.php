<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Manajemen Aset') }}</h2>
    </x-slot>

    <div class="py-12" x-data="assetsPage()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Daftar Aset</h3>
                        <div class="flex space-x-2">
                            <a href="#"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50">
                                <i class="fas fa-file-export me-2"></i> Export
                            </a>
                            <a href="{{ route('master.assets.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <i class="fas fa-plus me-2"></i> Barang Masuk
                            </a>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                            <a href="#" @click.prevent="changeTab('fixed_asset')"
                                :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': currentTab === 'fixed_asset', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'fixed_asset' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Aset Tetap</a>
                            <a href="#" @click.prevent="changeTab('consumable')"
                                :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': currentTab === 'consumable', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'consumable' }"
                                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Barang Habis
                                Pakai</a>
                        </nav>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-b-lg mb-6 flex items-center gap-4">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                    class="fas fa-search text-gray-400"></i></div>
                            <input type="search" x-model.debounce.500ms="search"
                                placeholder="Cari nama, S/N, atau kategori aset..."
                                class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            {{-- ================== BAGIAN YANG DIPERBARUI ================== --}}

                            {{-- Header untuk Aset Tetap --}}
                            <thead x-show="currentTab === 'fixed_asset'" class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Aset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>

                            {{-- Body untuk Aset Tetap --}}
                            <tbody x-show="currentTab === 'fixed_asset'"
                                class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && assets.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="(asset, index) in assets" :key="asset.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 font-medium">
                                            <div x-text="asset.name_asset"></div>
                                            <div class="text-xs text-gray-500" x-text="asset.serial_number"></div>
                                        </td>
                                        <td class="px-6 py-4" x-text="asset.category"></td>
                                        <td class="px-6 py-4" x-text="asset.room ? asset.room.name_room : 'Gudang'">
                                        </td>
                                        <td class="px-6 py-4 text-center"><span
                                                class="px-3 py-1 text-xs capitalize font-semibold rounded-full"
                                                :class="assetStatusClass(asset.status)"
                                                x-text="asset.status.replace('_', ' ')"></span></td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/assets/${asset.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200"
                                                    title="Lihat"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/assets/${asset.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(asset.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>

                            {{-- Header untuk Barang Habis Pakai --}}
                            <thead x-show="currentTab === 'consumable'" style="display: none;"
                                class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok
                                        Min.</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            {{-- Body untuk Barang Habis Pakai --}}
                            <tbody x-show="currentTab === 'consumable'" style="display: none;"
                                class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && assets.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="(asset, index) in assets" :key="asset.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 font-medium" x-text="asset.name_asset"></td>
                                        <td class="px-6 py-4" x-text="asset.category"></td>
                                        <td class="px-6 py-4 text-center" x-text="asset.current_stock"></td>
                                        <td class="px-6 py-4 text-center" x-text="asset.minimum_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/assets/${asset.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(asset.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            {{-- ==================================================================== --}}
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">Menampilkan <span
                                x-text="pagination.from || 0"></span> sampai <span x-text="pagination.to || 0"></span>
                            dari <span x-text="pagination.total || 0"></span> entri</p>
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
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    {{-- Skrip Alpine.js tidak perlu diubah --}}
    <script>
        function assetsPage() {
                return {
                    assets: [],
                    pagination: {},
                    isLoading: true,
                    currentTab: 'fixed_asset',
                    search: '',
                    init() {
                        this.fetchAssets();
                        this.$watch('search', () => this.applyFilters());

                        const toastMessage = sessionStorage.getItem('toastMessage');
                        if (toastMessage) {
                            iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                            sessionStorage.removeItem('toastMessage');
                        }
                    },
                    changeTab(tab) {
                        this.currentTab = tab;
                        this.applyFilters();
                    },
                    applyFilters() {
                        this.fetchAssets(1);
                    },
                    fetchAssets(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({
                            page: page,
                            perPage: 10,
                            search: this.search,
                            asset_type: this.currentTab
                        });

                        fetch(`/api/assets?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.assets = data.data;
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
                        this.fetchAssets(new URL(url).searchParams.get('page'));
                    },
                    assetStatusClass(status) {
                        switch (status) {
                            case 'available': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                            case 'in_use': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
                            case 'maintenance': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                            case 'disposed': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                            default: return 'bg-gray-100 text-gray-800';
                        }
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    confirmDelete(id) {
                        iziToast.question({
                            timeout: 20000,
                            close: false,
                            overlay: true,
                            title: 'Konfirmasi Hapus',
                            message: 'Anda yakin ingin menghapus data ini?',
                            position: 'center',
                            buttons: [
                                ['<button><b>YA</b></button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    this.deleteAsset(id);
                                }, true],
                                ['<button>Batal</button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                }],
                            ]
                        });
                    },
                    async deleteAsset(id) {
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/assets/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                        })
                        .then(response => {
                            if (response.ok) {
                                iziToast.success({ title: 'Berhasil', message: 'Data aset telah dihapus.', position: 'topRight' });
                                this.fetchAssets(this.pagination.current_page);
                            } else { throw new Error('Gagal menghapus data.'); }
                        })
                        .catch(error => iziToast.error({ title: 'Gagal', message: error.message, position: 'topRight' }));
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>