<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Stok Barang') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="stockManager(@js($data['stocks']))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Hanya tampilkan stok
                                menipis</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok
                                        Saat Ini</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok
                                        Minimum</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status
                                        Simpan</th>
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
                                        <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada data
                                            ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="item in stocks" :key="item.id">
                                    <tr
                                        :class="{ 'bg-red-50 dark:bg-red-900/20': item.current_stock <= item.minimum_stock && item.minimum_stock > 0 }">
                                        <td class="px-6 py-4 font-medium" x-text="item.name_asset"></td>
                                        <td class="px-6 py-4 text-gray-500" x-text="item.category"></td>
                                        <td class="px-6 py-4 text-center font-bold text-lg" x-text="item.current_stock">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" min="0"
                                                x-model.number.debounce.750ms="item.minimum_stock"
                                                @change="updateMinimumStock(item)"
                                                class="w-24 text-center rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700">
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm">
                                            <span x-show="item.saving"
                                                class="text-xs text-gray-500 italic">Menyimpan...</span>
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
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        function stockManager(initialData) {
                return {
                    stocks: initialData.data,
                    pagination: initialData,
                    isLoading: false,
                    search: '',
                    lowStockOnly: false,

                    init() {
                        this.$watch('search', () => this.fetchStocks(1));
                        this.$watch('lowStockOnly', () => this.fetchStocks(1));
                    },
                    fetchStocks(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({ page, search: this.search, low_stock_only: this.lowStockOnly, perPage: 15 });
                        fetch(`/api/stock-management?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.stocks = data.data;
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
                        this.fetchStocks(new URL(url).searchParams.get('page'));
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async updateMinimumStock(item) {
                        item.saving = true;
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/stock-management/${item.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify({ minimum_stock: item.minimum_stock })
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(() => {
                            iziToast.success({ title: 'Berhasil!', message: 'Stok minimum berhasil diperbarui.', position: 'topRight', timeout: 2000 });
                        })
                        .catch(err => {
                             let msg = 'Gagal menyimpan.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                        })
                        .finally(() => delete item.saving);
                    },
                }
            }
    </script>
    @endpush
</x-app-layout>