<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-boxes mr-2"></i>
            {{ __('Manajemen Stok Barang') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="stockManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Daftar Stok Barang Habis
                        Pakai</h3>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative flex-grow w-full md:w-1/2">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                    class="fas fa-search text-gray-400"></i></div>
                            <input type="search" x-model.debounce.500ms="search" placeholder="Cari nama barang..."
                                class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="lowStockOnly"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:checked:bg-indigo-600">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Hanya tampilkan stok
                                menipis</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Nama Barang</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Stok Saat Ini</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Stok Minimum</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Status Simpan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="5" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && stocks.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak
                                            ada data ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="item in stocks" :key="item.id">
                                    <tr
                                        :class="{ 'bg-red-50 dark:bg-red-900/20': item.current_stock <= item.minimum_stock && item.minimum_stock > 0 }">
                                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200"
                                            x-text="item.name_asset"></td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400" x-text="item.category">
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-lg text-gray-800 dark:text-gray-200"
                                            x-text="item.current_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            {{-- ========================================================= --}}
                                            {{-- === PERBAIKAN UTAMA DI SINI === --}}
                                            {{-- ========================================================= --}}
                                            <input type="number" min="0" x-model.number="item.minimum_stock"
                                                @input.debounce.750ms="updateMinimumStock(item)"
                                                class="w-24 text-center rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm">
                                            <span x-show="item.saving"
                                                class="text-xs text-gray-500 italic flex items-center justify-center">
                                                <i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center"
                        x-show="!isLoading && pagination.total > 0">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2 md:mb-0">
                            Menampilkan <span class="font-medium" x-text="pagination.from || 0"></span> - <span
                                class="font-medium" x-text="pagination.to || 0"></span> dari <span class="font-medium"
                                x-text="pagination.total || 0"></span> hasil
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
        function stockManager() {
            return {
                stocks: [],
                pagination: {},
                isLoading: true,
                search: '',
                lowStockOnly: false,
                init() {
                    this.fetchStocks();
                    this.$watch('search', () => this.fetchStocks(1));
                    this.$watch('lowStockOnly', () => this.fetchStocks(1));
                },
                fetchStocks(page = 1) {
                    this.isLoading = true;
                    const params = new URLSearchParams({ page, search: this.search, low_stock_only: this.lowStockOnly, perPage: 15 });

                    axios.get(`/api/stock-management?${params.toString()}`)
                    .then(response => {
                        this.stocks = response.data.data.map(item => ({...item, saving: false}));
                        response.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = response.data;
                    })
                    .catch(error => {
                        console.error('Gagal mengambil data stok:', error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil data stok dari server.', position: 'topRight' });
                    })
                    .finally(() => this.isLoading = false);
                },
                changePage(url) {
                    if (!url) return;
                    this.fetchStocks(new URL(url).searchParams.get('page'));
                },
                updateMinimumStock(item) {
                    item.saving = true;
                    axios.put(`/api/stock-management/${item.id}`, {
                        minimum_stock: item.minimum_stock
                    })
                    .then(response => {
                        window.iziToast.success({
                            title: 'Berhasil!',
                            message: 'Stok minimum berhasil diperbarui.',
                            position: 'topRight',
                            timeout: 2000
                        });
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan.';
                        if (error.response?.data?.errors) {
                            msg = Object.values(error.response.data.errors).flat().join('<br>');
                        } else if (error.response?.data?.message) {
                            msg = error.response.data.message;
                        }
                        window.iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                    })
                    .finally(() => item.saving = false);
                },
            }
        }
    </script>
    @endpush
</x-app-layout>