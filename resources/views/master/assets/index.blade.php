<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen Aset') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="assetsCRUD({ rooms: {{ Js::from($rooms) }} })">

                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Daftar Aset</h3>
                                <p class="text-sm text-gray-500 mt-1">Kelola semua aset tetap dan barang habis pakai di
                                    sini.</p>
                            </div>
                            <div class="flex space-x-2 mt-4 sm:mt-0">
                                <a href="{{ route('export.assets') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Export
                                </a>
                                <button @click="openModal()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Barang Masuk
                                </button>
                            </div>
                        </div>

                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                                <a href="#" @click.prevent="currentTab = 'fixed_asset'"
                                    :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'fixed_asset', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'fixed_asset' }"
                                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Aset Tetap</a>
                                <a href="#" @click.prevent="currentTab = 'consumable'"
                                    :class="{ 'border-indigo-500 text-indigo-600': currentTab === 'consumable', 'border-transparent text-gray-500 hover:text-gray-700': currentTab !== 'consumable' }"
                                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Barang Habis
                                    Pakai</a>
                            </nav>
                        </div>

                        <div class="mt-6">
                            <div x-show="currentTab === 'fixed_asset'" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Aset</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Lokasi</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Total Maintenance</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200"><template x-if="isLoading">
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Memuat data...</td>
                                            </tr>
                                        </template><template x-for="asset in filteredAssets" :key="asset.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    <div x-text="asset.name_asset"></div>
                                                    <div class="text-xs text-gray-500"
                                                        x-text="asset.serial_number ? 'S/N: ' + asset.serial_number : ''">
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.category">
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500"
                                                    x-text="asset.room ? `${asset.room.floor.building.name_building} / ${asset.room.floor.name_floor} / ${asset.room.name_room}` : 'Gudang'">
                                                </td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500"
                                                    x-text="asset.maintenances.length"></td>
                                                <td class="px-6 py-4 text-center text-sm font-medium">
                                                    <div class="flex items-center justify-center space-x-2"><button
                                                            @click="editAsset(asset)"
                                                            class="text-gray-400 hover:text-indigo-600 p-1"
                                                            title="Edit"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                                </path>
                                                            </svg></button><button @click="openHistoryModal(asset)"
                                                            class="text-gray-400 hover:text-blue-600 p-1"
                                                            title="Lihat History"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                                </path>
                                                            </svg></button><button @click="confirmDelete(asset.id)"
                                                            class="text-gray-400 hover:text-red-600 p-1"
                                                            title="Hapus"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg></button></div>
                                                </td>
                                            </tr>
                                        </template><template x-if="!isLoading && filteredAssets.length === 0">
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Tidak ada data aset tetap.</td>
                                            </tr>
                                        </template></tbody>
                                </table>
                            </div>

                            <div x-show="currentTab === 'consumable'" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Barang</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Stok Saat Ini</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Stok Minimum</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200"><template x-if="isLoading">
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Memuat data...</td>
                                            </tr>
                                        </template><template x-for="asset in filteredAssets" :key="asset.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium" x-text="asset.name_asset">
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500" x-text="asset.category">
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500"
                                                    x-text="asset.current_stock"></td>
                                                <td class="px-6 py-4 text-sm text-gray-500"
                                                    x-text="asset.minimum_stock"></td>
                                                <td class="px-6 py-4 text-center text-sm font-medium">
                                                    <div class="flex items-center justify-center space-x-2"><button
                                                            @click="editAsset(asset)"
                                                            class="text-gray-400 hover:text-indigo-600 p-1"
                                                            title="Edit"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                                </path>
                                                            </svg></button><button @click="openStockOutModal(asset)"
                                                            class="text-gray-400 hover:text-red-600 p-1"
                                                            title="Catat Stok Keluar"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3">
                                                                </path>
                                                            </svg></button><button @click="confirmDelete(asset.id)"
                                                            class="text-gray-400 hover:text-red-600 p-1"
                                                            title="Hapus"><svg class="w-5 h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg></button></div>
                                                </td>
                                            </tr>
                                        </template><template x-if="!isLoading && filteredAssets.length === 0">
                                            <tr>
                                                <td colspan="5" class="text-center py-4">Tidak ada data barang habis
                                                    pakai.</td>
                                            </tr>
                                        </template></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div><span
                            class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                            <form @submit.prevent="saveAssets()">
                                <div class="bg-white px-6 pt-5 pb-4">
                                    <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2">Form Barang Masuk</h3>
                                    <p class="text-sm text-gray-500 mb-6">Masukkan satu atau lebih barang baru ke dalam
                                        sistem.</p>
                                    <div class="space-y-4 max-h-[60vh] overflow-y-auto p-2"><template
                                            x-for="(asset, index) in formData.assets" :key="index">
                                            <div
                                                class="grid grid-cols-12 gap-x-4 gap-y-2 items-start bg-gray-50 p-4 rounded-lg relative">
                                                <div class="col-span-12 md:col-span-3"><label
                                                        class="block text-xs font-medium text-gray-600">Nama
                                                        Aset</label><input type="text" x-model="asset.name_asset"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300" required>
                                                </div>
                                                <div class="col-span-6 md:col-span-2"><label
                                                        class="block text-xs font-medium text-gray-600">Jenis</label><select
                                                        x-model="asset.asset_type"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300">
                                                        <option value="fixed_asset">Aset Tetap</option>
                                                        <option value="consumable">Habis Pakai</option>
                                                    </select></div>
                                                <div class="col-span-6 md:col-span-2"><label
                                                        class="block text-xs font-medium text-gray-600">Kode Kategori (3
                                                        Huruf)</label><input type="text" x-model="asset.category"
                                                        @input="asset.category = asset.category.toUpperCase()"
                                                        maxlength="3"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300" required>
                                                </div>
                                                <div class="col-span-6 md:col-span-1"><label
                                                        class="block text-xs font-medium text-gray-600">Stok</label><input
                                                        type="number" x-model.number="asset.current_stock" min="1"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300" required>
                                                </div>
                                                <div class="col-span-6 md:col-span-2"
                                                    x-show="asset.asset_type === 'fixed_asset'"><label
                                                        class="block text-xs font-medium text-gray-600">Kondisi</label><select
                                                        x-model="asset.condition"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300">
                                                        <option value="Baik">Baik</option>
                                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                                        <option value="Rusak Berat">Rusak Berat</option>
                                                    </select></div>
                                                <div class="col-span-6 md:col-span-2"
                                                    x-show="asset.asset_type === 'consumable'"><label
                                                        class="block text-xs font-medium text-gray-600">Stok
                                                        Min.</label><input type="number"
                                                        x-model.number="asset.minimum_stock" min="0"
                                                        class="mt-1 w-full rounded-md text-sm border-gray-300"></div>
                                                <div class="col-span-12 md:col-span-2 flex items-center md:pt-5"><button
                                                        type="button" @click="removeAssetRow(index)"
                                                        x-show="formData.assets.length > 1"
                                                        class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition"
                                                        title="Hapus Baris"><svg class="w-5 h-5" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd"></path>
                                                        </svg></button></div>
                                            </div>
                                        </template></div><button type="button" @click="addAssetRow()"
                                        class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center"><svg
                                            class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>Tambah Baris</button>
                                </div>
                                <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse"><button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-indigo-600 sm:ml-3 sm:w-auto sm:text-sm text-white font-medium"
                                        :disabled="isSubmitting"><span x-show="!isSubmitting">Simpan Semua</span><span
                                            x-show="isSubmitting">Menyimpan...</span></button><button type="button"
                                        @click="closeModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 bg-white sm:mt-0 sm:w-auto sm:text-sm font-medium text-gray-700">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="closeEditModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div><span
                            class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                            <form @submit.prevent="updateAsset()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Data Aset</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-4">
                                            <div><label class="block text-sm font-medium">Nama Aset</label><input
                                                    type="text" x-model="editFormData.name_asset"
                                                    class="mt-1 w-full rounded-md" required></div>
                                            <div><label class="block text-sm font-medium">Kategori</label><input
                                                    type="text" x-model="editFormData.category"
                                                    class="mt-1 w-full rounded-md" required></div>
                                            <div><label class="block text-sm font-medium">Lokasi</label><select
                                                    x-model="editFormData.room_id" class="mt-1 w-full rounded-md">
                                                    <option value="">-- Gudang --</option><template
                                                        x-for="room in rooms" :key="room.id">
                                                        <option :value="room.id"
                                                            x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                        </option>
                                                    </template>
                                                </select></div>
                                        </div>
                                        <div class="space-y-4">
                                            <div x-show="editFormData.asset_type === 'fixed_asset'"><label
                                                    class="block text-sm font-medium">Nomor Seri</label><input
                                                    type="text" x-model="editFormData.serial_number"
                                                    class="mt-1 w-full rounded-md bg-gray-100"
                                                    :disabled="editFormData.serial_number"></div>
                                            <div x-show="editFormData.asset_type === 'fixed_asset'"><label
                                                    class="block text-sm font-medium">Kondisi</label><select
                                                    x-model="editFormData.condition" class="mt-1 w-full rounded-md">
                                                    <option value="Baik">Baik</option>
                                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                </select></div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div><label class="block text-sm font-medium">Stok</label><input
                                                        type="number" x-model.number="editFormData.current_stock"
                                                        min="0" class="mt-1 w-full rounded-md" required
                                                        :disabled="editFormData.asset_type === 'fixed_asset'"></div>
                                                <div x-show="editFormData.asset_type === 'consumable'"><label
                                                        class="block text-sm font-medium">Stok Min.</label><input
                                                        type="number" x-model.number="editFormData.minimum_stock"
                                                        min="0" class="mt-1 w-full rounded-md" required></div>
                                            </div>
                                            <div><label class="block text-sm font-medium">Deskripsi</label><textarea
                                                    x-model="editFormData.description" rows="3"
                                                    class="mt-1 w-full rounded-md"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"><button
                                        type="submit"
                                        class="w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-indigo-600 sm:ml-3 sm:w-auto sm:text-sm text-white"
                                        :disabled="isSubmitting"><span x-show="!isSubmitting">Update</span><span
                                            x-show="isSubmitting">Menyimpan...</span></button><button type="button"
                                        @click="closeEditModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border bg-white sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="showHistoryModal" @keydown.escape.window="showHistoryModal = false" x-transition
                    class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showHistoryModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-3xl sm:w-full p-6">
                            <h3 class="text-lg font-medium text-gray-900">History untuk: <span
                                    x-text="selectedAsset.name_asset" class="font-bold"></span></h3>
                            <div class="mt-4 max-h-96 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-800 border-b pb-2 mb-2">Riwayat Perbaikan</h4>
                                    <ul class="space-y-3"><template
                                            x-if="selectedAsset.maintenances && selectedAsset.maintenances.length > 0"><template
                                                x-for="maintenance in selectedAsset.maintenances" :key="maintenance.id">
                                                <li class="border p-3 rounded-md text-sm">
                                                    <p><strong>Tanggal:</strong> <span
                                                            x-text="new Date(maintenance.created_at).toLocaleDateString('id-ID')"></span>
                                                    </p>
                                                    <p><strong>Laporan:</strong> <span
                                                            x-text="maintenance.description_text"></span></p>
                                                    <p><strong>Teknisi:</strong> <span
                                                            x-text="maintenance.technician ? maintenance.technician.name : 'N/A'"></span>
                                                    </p>
                                                </li>
                                            </template></template><template
                                            x-if="!selectedAsset.maintenances || selectedAsset.maintenances.length === 0">
                                            <p class="text-gray-500 text-sm">Tidak ada riwayat perbaikan.</p>
                                        </template></ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 border-b pb-2 mb-2">Riwayat Pemakaian/Tugas
                                    </h4>
                                    <ul class="space-y-3"><template
                                            x-if="selectedAsset.tasks && selectedAsset.tasks.length > 0"><template
                                                x-for="task in selectedAsset.tasks" :key="task.id">
                                                <li class="border p-3 rounded-md text-sm">
                                                    <p><strong>Tanggal:</strong> <span
                                                            x-text="new Date(task.created_at).toLocaleDateString('id-ID')"></span>
                                                    </p>
                                                    <p><strong>Tugas:</strong> <span x-text="task.title"></span></p>
                                                    <p><strong>Dikerjakan Oleh:</strong> <span
                                                            x-text="task.staff ? task.staff.name : 'N/A'"></span></p>
                                                </li>
                                            </template></template><template
                                            x-if="!selectedAsset.tasks || selectedAsset.tasks.length === 0">
                                            <p class="text-gray-500 text-sm">Aset ini belum pernah terkait dengan tugas
                                                apapun.</p>
                                        </template></ul>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button @click="showHistoryModal = false">Tutup</x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="showStockOutModal" @keydown.escape.window="showStockOutModal = false" x-transition
                    class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showStockOutModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                            <form @submit.prevent="submitStockOut()">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900">Catat Stok Keluar</h3>
                                    <p class="mt-1 text-sm text-gray-600">Anda akan mengurangi stok untuk: <strong
                                            x-text="selectedAsset.name_asset"></strong></p>
                                    <div class="mt-4"><label for="stock_out_amount"
                                            class="block text-sm font-medium text-gray-700">Jumlah Keluar</label><input
                                            type="number" id="stock_out_amount" x-model.number="stockOutData.amount"
                                            min="1" :max="selectedAsset.current_stock"
                                            class="mt-1 block w-full rounded-md" required></div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <x-danger-button type="submit">Konfirmasi</x-danger-button>
                                    <x-secondary-button type="button" @click="showStockOutModal = false" class="mr-3">
                                        Batal</x-secondary-button>
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
                                        </svg></div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Aset</h3>
                                        <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus data
                                            ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"><button
                                    @click="deleteAsset()"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Ya,
                                    Hapus</button><button @click="showDeleteModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function assetsCRUD(data) {
            return {
                assets: [], rooms: data.rooms || [],
                isLoading: true, isSubmitting: false,
                showModal: false, isEditMode: false, currentTab: 'fixed_asset',
                showHistoryModal: false, showStockOutModal: false, showDeleteModal: false, showEditModal: false,
                selectedAsset: {}, stockOutData: { id: null, amount: 1 }, assetToDeleteId: null,
                formData: { assets: [] }, editFormData: {},
                notification: { show: false, message: '', type: 'success' },
                get filteredAssets() { if (!this.assets) return []; return this.assets.filter(asset => asset.asset_type === this.currentTab); },
                init() { this.getAssets(); this.addAssetRow(); },
                addAssetRow() { this.formData.assets.push({ name_asset: '', asset_type: 'fixed_asset', category: '', condition: 'Baik', current_stock: 1, minimum_stock: 0 }); },
                removeAssetRow(index) { this.formData.assets.splice(index, 1); },
                openModal() { this.isEditMode = false; this.formData.assets = []; this.addAssetRow(); this.showModal = true; },
                closeModal() { this.showModal = false; },
                async getAssets() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/assets', { headers: { 'Accept': 'application/json' } });
                        if (!response.ok) throw new Error('Gagal memuat data.');
                        this.assets = await response.json();
                    } catch (err) { this.showNotification(err.message, 'error'); }
                    finally { this.isLoading = false; }
                },
                async saveAssets() {
                    this.isSubmitting = true;
                    try {
                        await fetch('/sanctum/csrf-cookie');
                        const response = await fetch('/api/assets', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }, body: JSON.stringify(this.formData) });
                        const data = await response.json();
                        if (!response.ok) throw data;
                        this.showNotification(data.message, 'success');
                        this.getAssets();
                        this.closeModal();
                    } catch (err) {
                        let msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Terjadi kesalahan.');
                        this.showNotification(`Error: ${msg}`, 'error');
                    } finally { this.isSubmitting = false; }
                },
                editAsset(asset) { this.editFormData = { ...asset, room_id: asset.room_id || '', condition: asset.condition || 'Baik' }; this.showEditModal = true; },
                closeEditModal() { this.showEditModal = false; },
                async updateAsset() {
                    this.isSubmitting = true;
                    try {
                        await fetch('/sanctum/csrf-cookie');
                        const response = await fetch(`/api/assets/${this.editFormData.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }, body: JSON.stringify(this.editFormData) });
                        const data = await response.json();
                        if (!response.ok) throw data;
                        this.showNotification('Data aset berhasil diperbarui.', 'success');
                        this.getAssets();
                        this.closeEditModal();
                    } catch (err) {
                        let msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Terjadi kesalahan.');
                        this.showNotification(`Error: ${msg}`, 'error');
                    } finally { this.isSubmitting = false; }
                },
                openHistoryModal(asset) { this.selectedAsset = asset; this.showHistoryModal = true; },
                openStockOutModal(asset) { this.selectedAsset = asset; this.stockOutData.id = asset.id; this.stockOutData.amount = 1; this.showStockOutModal = true; },
                async submitStockOut() {
                    this.isSubmitting = true;
                    try {
                        await fetch('/sanctum/csrf-cookie');
                        const response = await fetch(`/api/assets/${this.stockOutData.id}/stock-out`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }, body: JSON.stringify({ amount: this.stockOutData.amount }) });
                        const data = await response.json();
                        if (!response.ok) throw data;
                        this.showNotification('Stok berhasil dikurangi.', 'success');
                        this.getAssets();
                        this.showStockOutModal = false;
                    } catch (err) {
                        this.showNotification(err.message || 'Gagal mengurangi stok.', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                confirmDelete(id) { this.assetToDeleteId = id; this.showDeleteModal = true; },
                async deleteAsset() {
                    this.isSubmitting = true;
                    try {
                        await fetch('/sanctum/csrf-cookie');
                        const response = await fetch(`/api/assets/${this.assetToDeleteId}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() } });
                        if (!response.ok) throw new Error('Gagal menghapus aset.');
                        this.showNotification('Aset berhasil dihapus.', 'success');
                        this.getAssets();
                    } catch (err) {
                        this.showNotification(err.message, 'error');
                    } finally {
                        this.showDeleteModal = false;
                        this.assetToDeleteId = null;
                        this.isSubmitting = false;
                    }
                },
                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>