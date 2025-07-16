<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="assetsCRUD()">

                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="#" @click.prevent="currentTab = 'fixed_asset'"
                            :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'fixed_asset', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'fixed_asset' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Aset Tetap
                        </a>
                        <a href="#" @click.prevent="currentTab = 'consumable'"
                            :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'consumable', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'consumable' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Barang Habis Pakai
                        </a>
                    </nav>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700"
                                x-text="currentTab === 'fixed_asset' ? 'Daftar Aset Tetap' : 'Daftar Barang Habis Pakai'">
                            </h3>
                            <div class="flex space-x-3">
                                <a href="{{ route('export.assets') }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Excel
                                </a>
                                <button @click="openModal()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah <span x-text="currentTab === 'fixed_asset' ? 'Aset' : 'Barang'"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="currentTab === 'fixed_asset'" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Aset</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Seri</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Maintenance</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="asset in filteredAssets" :key="asset.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="asset.name_asset"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="asset.serial_number || '-'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.category"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="asset.room ? `${asset.room.floor.building.name_building} / ${asset.room.floor.name_floor} / ${asset.room.name_room}` : 'Gudang'">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-500"
                                                x-text="asset.maintenances.length"></td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <button @click="editAsset(asset)"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                        title="Edit">Edit</button>
                                                    <button @click="openHistoryModal(asset)"
                                                        class="text-blue-600 hover:text-blue-900"
                                                        title="History">History</button>
                                                    <button @click="confirmDelete(asset.id)"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Hapus">Hapus</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && filteredAssets.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                                aset tetap.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div x-show="currentTab === 'consumable'" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Barang</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok Saat Ini</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok Minimum</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="asset in filteredAssets" :key="asset.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="asset.name_asset"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.category"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.current_stock">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.minimum_stock">
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <button @click="editAsset(asset)"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                        title="Edit">Edit</button>
                                                    <button @click="openStockOutModal(asset)"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Stock Keluar">Stock Keluar</button>
                                                    <button @click="confirmDelete(asset.id)"
                                                        class="text-gray-500 hover:text-gray-700"
                                                        title="Hapus">Hapus</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && filteredAssets.length === 0">
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                                barang habis pakai.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                            <form @submit.prevent="saveAsset()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        x-text="isEditMode ? 'Edit ' + (formData.asset_type === 'fixed_asset' ? 'Aset' : 'Barang') : 'Tambah ' + (currentTab === 'fixed_asset' ? 'Aset Baru' : 'Barang Baru')">
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-4">
                                            <input type="hidden" x-model="formData.asset_type">
                                            <div>
                                                <label for="name_asset"
                                                    class="block text-sm font-medium text-gray-700">Nama <span
                                                        x-text="formData.asset_type === 'fixed_asset' ? 'Aset' : 'Barang'"></span></label>
                                                <input type="text" x-model="formData.name_asset"
                                                    class="mt-1 block w-full rounded-md" required>
                                            </div>
                                            <div>
                                                <label for="category"
                                                    class="block text-sm font-medium text-gray-700">Kategori</label>
                                                <input type="text" x-model="formData.category"
                                                    class="mt-1 block w-full rounded-md" required>
                                            </div>
                                            <div x-show="formData.asset_type === 'fixed_asset'">
                                                <label for="condition"
                                                    class="block text-sm font-medium text-gray-700">Kondisi</label>
                                                <select x-model="formData.condition"
                                                    class="mt-1 block w-full rounded-md">
                                                    <option value="Baik">Baik</option>
                                                    <option value="Rusak">Rusak</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="room_id"
                                                    class="block text-sm font-medium text-gray-700">Lokasi
                                                    (Opsional)</label>
                                                <select x-model="formData.room_id" class="mt-1 block w-full rounded-md">
                                                    <option value="">-- Tidak ada lokasi (Gudang) --</option>
                                                    <template x-for="room in rooms" :key="room.id">
                                                        <option :value="room.id"
                                                            x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                        </option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div x-show="formData.asset_type === 'fixed_asset'">
                                                <label for="serial_number"
                                                    class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                                                <input type="text" x-model="formData.serial_number"
                                                    class="mt-1 block w-full rounded-md">
                                            </div>
                                            <div x-show="formData.asset_type === 'fixed_asset'">
                                                <label for="purchase_date"
                                                    class="block text-sm font-medium text-gray-700">Tanggal Pembelian
                                                    (Opsional)</label>
                                                <input type="date" x-model="formData.purchase_date"
                                                    class="mt-1 block w-full rounded-md">
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label for="current_stock"
                                                        class="block text-sm font-medium text-gray-700">Stok Saat
                                                        Ini</label>
                                                    <input type="number" x-model.number="formData.current_stock" min="0"
                                                        class="mt-1 block w-full rounded-md" required>
                                                </div>
                                                <div>
                                                    <label for="minimum_stock"
                                                        class="block text-sm font-medium text-gray-700">Stok
                                                        Minimum</label>
                                                    <input type="number" x-model.number="formData.minimum_stock" min="0"
                                                        class="mt-1 block w-full rounded-md" required>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="description"
                                                    class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                                <textarea x-model="formData.description" rows="3"
                                                    class="mt-1 block w-full rounded-md"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                                    <button type="button" @click="closeModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-show="showHistoryModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showHistoryModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl sm:w-full p-6">
                            <h3 class="text-lg font-medium text-gray-900">Riwayat Maintenance: <span
                                    x-text="selectedAsset.name_asset"></span></h3>
                            <div class="mt-4 max-h-96 overflow-y-auto">
                                <template x-if="selectedAsset.maintenances.length > 0">
                                    <ul class="space-y-4">
                                        <template x-for="maintenance in selectedAsset.maintenances"
                                            :key="maintenance.id">
                                            <li class="border p-3 rounded-md">
                                                <p><strong>Tanggal:</strong> <span
                                                        x-text="new Date(maintenance.created_at).toLocaleDateString('id-ID')"></span>
                                                </p>
                                                <p><strong>Laporan:</strong> <span
                                                        x-text="maintenance.description_text"></span></p>
                                                <p><strong>Teknisi:</strong> <span
                                                        x-text="maintenance.technician ? maintenance.technician.name : 'N/A'"></span>
                                                </p>
                                                <p><strong>Status:</strong> <span x-text="maintenance.status"></span>
                                                </p>
                                                <p x-show="maintenance.notes"><strong>Catatan:</strong> <span
                                                        x-text="maintenance.notes"></span></p>
                                            </li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="selectedAsset.maintenances.length === 0">
                                    <p class="text-gray-500">Tidak ada riwayat maintenance untuk aset ini.</p>
                                </template>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button @click="showHistoryModal = false">Tutup</x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="showStockOutModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showStockOutModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                            <form @submit.prevent="submitStockOut()">
                                <h3 class="text-lg font-medium text-gray-900">Catat Stok Keluar</h3>
                                <p class="mt-1 text-sm text-gray-600">Anda akan mengurangi stok untuk: <strong
                                        x-text="selectedAsset.name_asset"></strong></p>
                                <div class="mt-4">
                                    <label for="stock_out_amount" class="block text-sm font-medium text-gray-700">Jumlah
                                        Keluar</label>
                                    <input type="number" id="stock_out_amount" x-model.number="stockOutData.amount"
                                        min="1" :max="selectedAsset.current_stock" class="mt-1 block w-full rounded-md"
                                        required>
                                </div>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <x-secondary-button type="button" @click="showStockOutModal = false">Batal
                                    </x-secondary-button>
                                    <x-danger-button type="submit">Konfirmasi</x-danger-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="showDeleteModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Aset</h3>
                                        <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus data
                                            ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button @click="deleteAsset()"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Ya,
                                    Hapus</button>
                                <button @click="showDeleteModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function assetsCRUD() {
            return {
                assets: [],
                rooms: @json($rooms),
                isLoading: true,
                showModal: false,
                isEditMode: false,
                currentUser: {{ Js::from(Auth::user()) }},
                currentTab: 'fixed_asset', // Tab default

                // State untuk modal-modal baru
                showHistoryModal: false,
                showStockOutModal: false,
                selectedAsset: { maintenances: [] }, // Untuk menampung data aset yang dipilih
                stockOutData: { id: null, amount: 1 },

                formData: {
                    id: null, name_asset: '', room_id: '', asset_type: 'fixed_asset',
                    category: '', condition: 'Baik', serial_number: '', purchase_date: '',
                    status: 'available', current_stock: 1, minimum_stock: 0, description: ''
                },
                notification: { show: false, message: '', type: 'success' },
                showDeleteModal: false,
                assetToDeleteId: null,

                // Computed property untuk memfilter aset berdasarkan tab
                get filteredAssets() {
                    return this.assets.filter(asset => asset.asset_type === this.currentTab);
                },

                async init() {
                    await fetch('/sanctum/csrf-cookie');
                    this.getAssets();
                },

                getAssets() {
                    this.isLoading = true;
                    fetch('/api/assets', { headers: { 'Accept': 'application/json' } })
                    .then(res => { if (!res.ok) throw new Error('Gagal memuat data.'); return res.json(); })
                    .then(data => { this.assets = data; this.isLoading = false; })
                    .catch(err => { this.showNotification(err.message, 'error'); this.isLoading = false; });
                },

                openModal() {
                    this.isEditMode = false;
                    this.formData = {
                        id: null, name_asset: '', room_id: '', asset_type: this.currentTab,
                        category: '', condition: 'Baik', serial_number: '', purchase_date: '',
                        status: 'available', current_stock: 1, minimum_stock: 0, description: ''
                    };
                    // Barang habis pakai tidak punya stok 1 by default, bisa 0 atau lebih
                    if (this.currentTab === 'consumable') {
                        this.formData.current_stock = 0;
                    }
                    this.showModal = true;
                },

                closeModal() { this.showModal = false; },

                editAsset(asset) {
                    this.isEditMode = true;
                    this.formData = { ...asset, room_id: asset.room_id || '' };
                    this.showModal = true;
                },

                saveAsset() {
                    const url = this.isEditMode ? `/api/assets/${this.formData.id}` : '/api/assets';
                    const method = this.isEditMode ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification(this.isEditMode ? 'Data berhasil diperbarui' : 'Data berhasil ditambahkan', 'success');
                        this.getAssets();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Terjadi kesalahan.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        this.showNotification(`Error: ${msg}`, 'error');
                    });
                },

                openHistoryModal(asset) {
                    this.selectedAsset = asset;
                    this.showHistoryModal = true;
                },

                openStockOutModal(asset) {
                    this.stockOutData.id = asset.id;
                    this.stockOutData.amount = 1;
                    this.selectedAsset = asset;
                    this.showStockOutModal = true;
                },

                submitStockOut() {
                    fetch(`/api/assets/${this.stockOutData.id}/stock-out`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify({ amount: this.stockOutData.amount })
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification('Stok berhasil dikurangi.', 'success');
                        this.getAssets();
                        this.showStockOutModal = false;
                    })
                    .catch(err => {
                        let msg = err.message || 'Gagal mengurangi stok.';
                        this.showNotification(`Error: ${msg}`, 'error');
                    });
                },

                confirmDelete(id) {
                    this.assetToDeleteId = id;
                    this.showDeleteModal = true;
                },

                 deleteAsset() {
                    fetch(`/api/assets/${this.assetToDeleteId}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                    })
                    .then(res => { if (!res.ok) throw new Error('Gagal menghapus.'); this.showNotification('Aset berhasil dihapus', 'success'); this.getAssets(); })
                    .catch(err => this.showNotification(err.message, 'error'))
                    .finally(() => {
                        this.showDeleteModal = false;
                        this.assetToDeleteId = null;
                    });
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) return decodeURIComponent(csrfCookie.split('=')[1]);
                    return '';
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }
    </script>
</x-app-layout>