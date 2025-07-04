<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Lantai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Komponen Alpine.js Utama -->
            <div x-data="floorsCRUD()">

                <!-- Notifikasi Global -->
                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700">Daftar Lantai</h3>
                            <button @click="openModal()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Lantai
                            </button>
                        </div>

                        <!-- Tabel Data Lantai -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Lantai</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Gedung</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dibuat Oleh</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data...
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(floor, index) in floors" :key="floor.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="floor.name_floor"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="floor.building ? floor.building.name_building : 'N/A'"></td>
                                            <td class="px-6 py-4">
                                                <span
                                                    :class="floor.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    <span
                                                        x-text="floor.status === 'active' ? 'Aktif' : 'Tidak Aktif'"></span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="floor.creator ? floor.creator.name : 'N/A'"></td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <button @click="editFloor(floor)"
                                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button @click="confirmDelete(floor.id)"
                                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && floors.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                                lantai ditemukan.</td>
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
                        <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="saveFloor()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        x-text="isEditMode ? 'Edit Lantai' : 'Tambah Lantai Baru'"></h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="building_id"
                                                class="block text-sm font-medium text-gray-700">Gedung</label>
                                            <select id="building_id" x-model="formData.building_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                                <option value="">-- Pilih Gedung --</option>
                                                <template x-for="building in buildings" :key="building.id">
                                                    <option :value="building.id" x-text="building.name_building">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="name_floor" class="block text-sm font-medium text-gray-700">Nama
                                                Lantai</label>
                                            <input type="text" id="name_floor" x-model="formData.name_floor"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                        </div>
                                        <div>
                                            <label for="status"
                                                class="block text-sm font-medium text-gray-700">Status</label>
                                            <select id="status" x-model="formData.status"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                                <option value="active">Aktif</option>
                                                <option value="inactive">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                                    <button type="button" @click="closeModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi Hapus -->
                <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <!-- ... (Salin kode modal konfirmasi hapus dari modul Gedung) ... -->
                </div>

            </div>
        </div>
    </div>

    <!-- Logika Alpine.js -->
    <script>
        function floorsCRUD() {
            return {
                floors: [],
                buildings: @json($buildings),
                isLoading: true,
                showModal: false,
                isEditMode: false,
                formData: { id: null, name_floor: '', building_id: '', status: 'active' },
                notification: { show: false, message: '', type: 'success' },
                showDeleteModal: false,
                floorToDeleteId: null,

                async init() {
                    // Panggilan "pemanasan" untuk mendapatkan CSRF cookie yang valid
                    await fetch('/sanctum/csrf-cookie');
                    this.getFloors();
                },

                getFloors() {
                    this.isLoading = true;
                    fetch('/api/floors', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => { if (!res.ok) throw new Error('Gagal memuat data.'); return res.json(); })
                    .then(data => { this.floors = data; this.isLoading = false; })
                    .catch(err => { console.error(err); this.isLoading = false; });
                },

                openModal() {
                    this.isEditMode = false;
                    this.formData = { id: null, name_floor: '', building_id: '', status: 'active' };
                    this.showModal = true;
                },

                closeModal() { this.showModal = false; },

                editFloor(floor) {
                    this.isEditMode = true;
                    // Pastikan building_id juga di-set dengan benar
                    this.formData = {
                        id: floor.id,
                        name_floor: floor.name_floor,
                        building_id: floor.building_id,
                        status: floor.status
                    };
                    this.showModal = true;
                },

                saveFloor() {
                    const url = this.isEditMode ? `/api/floors/${this.formData.id}` : '/api/floors';
                    const method = this.isEditMode ? 'PUT' : 'POST';
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification(this.isEditMode ? 'Lantai berhasil diperbarui' : 'Lantai berhasil ditambahkan', 'success');
                        this.getFloors();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Terjadi kesalahan.';
                        if (err.errors) {
                            msg = Object.values(err.errors).flat().join(' ');
                        } else if (err.status === 419) {
                            msg = 'Sesi Anda telah berakhir. Silakan muat ulang halaman.';
                        }
                        this.showNotification(`Error: ${msg}`, 'error');
                    });
                },

                confirmDelete(id) {
                    this.floorToDeleteId = id;
                    this.showDeleteModal = true;
                },

                deleteFloor() {
                    fetch(`/api/floors/${this.floorToDeleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        }
                    })
                    .then(res => { if (!res.ok) throw new Error('Gagal menghapus.'); this.showNotification('Lantai berhasil dihapus', 'success'); this.getFloors(); })
                    .catch(err => this.showNotification(err.message, 'error'))
                    .finally(() => { this.showDeleteModal = false; this.floorToDeleteId = null; });
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