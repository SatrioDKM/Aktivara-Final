<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="assetsCRUD()">

                <!-- Notifikasi Global -->
                <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path x-show="notification.type === 'success'"
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            <path x-show="notification.type === 'error'"
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
                        </svg>
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700">Daftar Aset</h3>
                            <!-- Grup Tombol Aksi -->
                            <div class="flex space-x-3">
                                <!-- TOMBOL EKSPOR BARU -->
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
                                <!-- Tombol Tambah Aset -->
                                <button @click="openModal()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Aset
                                </button>
                            </div>
                        </div>

                        <!-- Tabel Data Aset -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Aset</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Memuat data...
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(asset, index) in assets" :key="asset.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                <div x-text="asset.name_asset"></div>
                                                <div class="text-xs text-gray-500"
                                                    x-text="asset.serial_number ? 'SN: ' + asset.serial_number : ''">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.category"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="asset.room ? `${asset.room.floor.building.name_building} / ${asset.room.floor.name_floor} / ${asset.room.name_room}` : 'Gudang'">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span x-text="asset.current_stock"></span>
                                                <span class="text-xs text-gray-400"
                                                    x-text="'(min: ' + asset.minimum_stock + ')'"></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="{
                                                        'bg-green-100 text-green-800': asset.status === 'available',
                                                        'bg-blue-100 text-blue-800': asset.status === 'in_use',
                                                        'bg-yellow-100 text-yellow-800': asset.status === 'maintenance',
                                                        'bg-gray-100 text-gray-800': asset.status === 'disposed'
                                                      }"
                                                    x-text="asset.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())">
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <button @click="editAsset(asset)"
                                                        class="text-indigo-600 hover:text-indigo-900" title="Edit"><svg
                                                            class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z">
                                                            </path>
                                                        </svg></button>
                                                    <button @click="confirmDelete(asset.id)"
                                                        class="text-red-600 hover:text-red-900" title="Hapus"><svg
                                                            class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && assets.length === 0">
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                                aset ditemukan.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Tambah/Edit -->
                <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                            <form @submit.prevent="saveAsset()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        x-text="isEditMode ? 'Edit Aset' : 'Tambah Aset Baru'"></h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Kolom Kiri -->
                                        <div class="space-y-4">
                                            <div>
                                                <label for="name_asset"
                                                    class="block text-sm font-medium text-gray-700">Nama Aset</label>
                                                <input type="text" id="name_asset" x-model="formData.name_asset"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                            </div>
                                            <div>
                                                <label for="category"
                                                    class="block text-sm font-medium text-gray-700">Kategori</label>
                                                <input type="text" id="category" x-model="formData.category"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                            </div>
                                            <div>
                                                <label for="room_id"
                                                    class="block text-sm font-medium text-gray-700">Lokasi
                                                    (Ruangan)</label>
                                                <select id="room_id" x-model="formData.room_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                    <option value="">-- Tidak ada lokasi (Gudang) --</option>
                                                    <template x-for="room in rooms" :key="room.id">
                                                        <option :value="room.id"
                                                            x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                        </option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="serial_number"
                                                    class="block text-sm font-medium text-gray-700">Nomor Seri</label>
                                                <input type="text" id="serial_number" x-model="formData.serial_number"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            </div>
                                            <div>
                                                <label for="purchase_date"
                                                    class="block text-sm font-medium text-gray-700">Tanggal
                                                    Pembelian</label>
                                                <input type="date" id="purchase_date" x-model="formData.purchase_date"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            </div>
                                        </div>
                                        <!-- Kolom Kanan -->
                                        <div class="space-y-4">
                                            <div>
                                                <label for="condition"
                                                    class="block text-sm font-medium text-gray-700">Kondisi</label>
                                                <input type="text" id="condition" x-model="formData.condition"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                            </div>
                                            <div>
                                                <label for="status"
                                                    class="block text-sm font-medium text-gray-700">Status</label>
                                                <select id="status" x-model="formData.status"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                                    <option value="available">Tersedia (Available)</option>
                                                    <option value="in_use">Digunakan (In Use)</option>
                                                    <option value="maintenance">Perawatan (Maintenance)</option>
                                                    <option value="disposed">Dibuang (Disposed)</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label for="current_stock"
                                                        class="block text-sm font-medium text-gray-700">Stok Saat
                                                        Ini</label>
                                                    <input type="number" id="current_stock"
                                                        x-model.number="formData.current_stock" min="0"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                        required>
                                                </div>
                                                <div>
                                                    <label for="minimum_stock"
                                                        class="block text-sm font-medium text-gray-700">Stok
                                                        Minimum</label>
                                                    <input type="number" id="minimum_stock"
                                                        x-model.number="formData.minimum_stock" min="0"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="description"
                                                    class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                                <textarea id="description" x-model="formData.description" rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
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

                <!-- Modal Konfirmasi Hapus -->
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
                formData: {
                    id: null, name_asset: '', room_id: '', category: '', serial_number: '',
                    purchase_date: '', condition: '', status: 'available',
                    current_stock: 0, minimum_stock: 0, description: ''
                },
                notification: { show: false, message: '', type: 'success' },
                showDeleteModal: false,
                assetToDeleteId: null,

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
                        id: null, name_asset: '', room_id: '', category: '', serial_number: '',
                        purchase_date: '', condition: '', status: 'available',
                        current_stock: 0, minimum_stock: 0, description: ''
                    };
                    this.showModal = true;
                },

                closeModal() { this.showModal = false; },

                editAsset(asset) {
                    this.isEditMode = true;
                    // Salin data, pastikan room_id di-set dengan benar (bisa null)
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
                        this.showNotification(this.isEditMode ? 'Aset berhasil diperbarui' : 'Aset berhasil ditambahkan', 'success');
                        this.getAssets();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Terjadi kesalahan.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
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
                    if (csrfCookie) {
                        return decodeURIComponent(csrfCookie.split('=')[1]);
                    }
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