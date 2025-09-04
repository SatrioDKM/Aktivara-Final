<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Stok Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="stockManager()">

                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Stok Barang Habis Pakai</h3>

                        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                            <input type="text" x-model.debounce.500ms="filters.search" @input="fetchStocks(1)"
                                placeholder="Cari nama barang..."
                                class="w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm">
                            <label class="inline-flex items-center">
                                <input type="checkbox" x-model="filters.low_stock_only" @change="fetchStocks(1)"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-gray-600">Hanya tampilkan stok menipis</span>
                            </label>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama Barang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kategori</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium uppercase">Stok Saat Ini
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium uppercase">Stok Minimum
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Memuat data...</td>
                                        </tr>
                                    </template>
                                    <template x-for="item in stocks.data" :key="item.id">
                                        <tr
                                            :class="{ 'bg-red-50': item.current_stock <= item.minimum_stock && item.minimum_stock > 0 }">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="item.name_asset"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.category"></td>
                                            <td class="px-6 py-4 text-sm text-center font-bold"
                                                x-text="item.current_stock"></td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <input type="number" min="0" x-model.number="item.minimum_stock"
                                                    @input.debounce.500ms="updateMinimumStock(item)"
                                                    class="w-24 text-center rounded-md border-gray-300 shadow-sm">
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm">
                                                <span x-show="item.saving"
                                                    class="text-xs text-gray-500">Menyimpan...</span>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && (!stocks.data || stocks.data.length === 0)">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data
                                                ditemukan.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4" x-show="stocks.total > 0">{{ $stocks->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function stockManager() {
            return {
                stocks: { data: [] },
                isLoading: true,
                filters: { search: '', low_stock_only: false },
                notification: { show: false, message: '', type: 'success' },

                init() { this.fetchStocks(1); },

                fetchStocks(page) {
                    this.isLoading = true;
                    const params = new URLSearchParams({ page: page, ...this.filters }).toString();
                    fetch(`/api/stock-management?${params}`, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => { this.stocks = data; })
                        .finally(() => this.isLoading = false);
                },

                async updateMinimumStock(item) {
                    item.saving = true; // Show saving indicator
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/stock-management/${item.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify({ minimum_stock: item.minimum_stock })
                    })
                    .then(res => { if (!res.ok) throw new Error('Gagal menyimpan.'); return res.json(); })
                    .then(() => { this.showNotification('Stok minimum berhasil diperbarui.', 'success'); })
                    .catch(err => this.showNotification(err.message, 'error'))
                    .finally(() => delete item.saving);
                },

                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>