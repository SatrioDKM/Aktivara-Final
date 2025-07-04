<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="roomsCRUD()">

                <!-- Notifikasi Global (reuse dari modul sebelumnya) -->
                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <!-- ... (kode notifikasi) ... -->
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700">Daftar Ruangan</h3>
                            <button @click="openModal()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Ruangan
                            </button>
                        </div>

                        <!-- Tabel Data Ruangan -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Ruangan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lantai</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Gedung</th>
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
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data...
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(room, index) in rooms" :key="room.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="room.name_room"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="room.floor ? room.floor.name_floor : 'N/A'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="room.floor && room.floor.building ? room.floor.building.name_building : 'N/A'">
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    :class="room.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    <span
                                                        x-text="room.status === 'active' ? 'Aktif' : 'Tidak Aktif'"></span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-4">
                                                    <button @click="editRoom(room)"
                                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <!-- Ikon Edit -->
                                                    </button>
                                                    <button @click="confirmDelete(room.id)"
                                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <!-- Ikon Hapus -->
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && rooms.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                                ruangan ditemukan.</td>
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
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="saveRoom()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        x-text="isEditMode ? 'Edit Ruangan' : 'Tambah Ruangan Baru'"></h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="building_filter"
                                                class="block text-sm font-medium text-gray-700">Filter Gedung</label>
                                            <select id="building_filter" x-model="filter.building_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">-- Pilih Gedung --</option>
                                                <template x-for="building in buildings" :key="building.id">
                                                    <option :value="building.id" x-text="building.name_building">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="floor_id"
                                                class="block text-sm font-medium text-gray-700">Lantai</label>
                                            <select id="floor_id" x-model="formData.floor_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required :disabled="!filter.building_id">
                                                <option value="">-- Pilih Lantai --</option>
                                                <template x-for="floor in filteredFloors" :key="floor.id">
                                                    <option :value="floor.id" x-text="floor.name_floor"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="name_room" class="block text-sm font-medium text-gray-700">Nama
                                                Ruangan</label>
                                            <input type="text" id="name_room" x-model="formData.name_room"
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
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                                    <button type="button" @click="closeModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function roomsCRUD() {
            return {
                rooms: [],
                buildings: @json($buildings),
                allFloors: @json($floors),
                filteredFloors: [],
                isLoading: true,
                showModal: false,
                isEditMode: false,
                formData: { id: null, name_room: '', floor_id: '', status: 'active' },
                filter: { building_id: '' },
                notification: { show: false, message: '', type: 'success' },

                async init() {
                    // Panggilan pemanasan untuk mencegah error 419 CSRF Mismatch
                    await fetch('/sanctum/csrf-cookie');
                    this.getRooms();

                    // Watcher untuk memfilter lantai saat gedung berubah
                    this.$watch('filter.building_id', (value) => {
                        this.formData.floor_id = ''; // Reset pilihan lantai
                        if (value) {
                            this.filteredFloors = this.allFloors.filter(floor => floor.building_id == value);
                        } else {
                            this.filteredFloors = [];
                        }
                    });
                },

                getRooms() {
                    this.isLoading = true;
                    fetch('/api/rooms', { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json()).then(data => { this.rooms = data; this.isLoading = false; })
                    .catch(err => { console.error(err); this.isLoading = false; });
                },

                openModal() {
                    this.isEditMode = false;
                    this.formData = { id: null, name_room: '', floor_id: '', status: 'active' };
                    this.filter.building_id = '';
                    this.showModal = true;
                },

                closeModal() { this.showModal = false; },

                async editRoom(room) {
                    this.isEditMode = true;
                    this.filter.building_id = room.floor.building_id;

                    // Tunggu DOM update setelah filter.building_id berubah
                    await this.$nextTick();

                    this.formData = {
                        id: room.id,
                        name_room: room.name_room,
                        floor_id: room.floor_id,
                        status: room.status
                    };
                    this.showModal = true;
                },

                saveRoom() {
                    const url = this.isEditMode ? `/api/rooms/${this.formData.id}` : '/api/rooms';
                    const method = this.isEditMode ? 'PUT' : 'POST';
                    fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification(this.isEditMode ? 'Ruangan berhasil diperbarui' : 'Ruangan berhasil ditambahkan', 'success');
                        this.getRooms();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Terjadi kesalahan.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        this.showNotification(`Error: ${msg}`, 'error');
                    });
                },

                // Salin fungsi confirmDelete, deleteRoom, getCsrfToken, dan showNotification dari modul sebelumnya
                // ...
            }
        }
    </script>
</x-app-layout>