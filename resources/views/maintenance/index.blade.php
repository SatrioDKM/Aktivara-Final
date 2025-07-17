<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Maintenance Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="maintenanceCRUD({ assets: {{ Js::from($assets) }} })">

                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700">Riwayat Maintenance Aset Tetap</h3>
                            <button @click="openModal()"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Laporkan Kerusakan
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aset</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Laporan Kerusakan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Teknisi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
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
                                    <template x-for="item in maintenances" :key="item.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"
                                                x-text="item.asset ? item.asset.name_asset : 'Aset Dihapus'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="item.description_text.substring(0, 40) + (item.description_text.length > 40 ? '...' : '')">
                                            </td>
                                            <td class="px-6 py-4"><span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(item.status)"
                                                    x-text="item.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="item.technician ? item.technician.name : '-'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="new Date(item.start_date || item.created_at).toLocaleDateString('id-ID')">
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <button @click="editItem(item)"
                                                    class="text-indigo-600 hover:text-indigo-900">Detail</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && maintenances.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada
                                                riwayat maintenance.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div @click="closeModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity">
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="isEditMode ? updateItem() : saveItem()">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        x-text="isEditMode ? 'Detail & Update Status' : 'Lapor Kerusakan Baru'"></h3>

                                    <template x-if="!isEditMode">
                                        <div class="space-y-4">
                                            <div>
                                                <label for="asset_id"
                                                    class="block text-sm font-medium text-gray-700">Aset yang
                                                    Rusak</label>
                                                <select x-model="formData.asset_id" id="asset_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                                    <option value="">-- Pilih Aset --</option>
                                                    <template x-for="asset in assets" :key="asset.id">
                                                        <option :value="asset.id" x-text="asset.name_asset"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="description_text"
                                                    class="block text-sm font-medium text-gray-700">Deskripsi
                                                    Kerusakan</label>
                                                <textarea x-model="formData.description_text" id="description_text"
                                                    rows="4"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required></textarea>
                                            </div>
                                            <input type="hidden" x-model="formData.maintenance_type" value="repair">
                                        </div>
                                    </template>

                                    <template x-if="isEditMode">
                                        <div class="space-y-4">
                                            <div class="p-3 bg-gray-50 rounded-md border">
                                                <p class="text-sm font-medium text-gray-600">Aset: <span
                                                        class="font-bold text-gray-900"
                                                        x-text="formData.asset.name_asset"></span></p>
                                                <p class="text-sm font-medium text-gray-600 mt-1">Laporan: <span
                                                        class="text-gray-800" x-text="formData.description_text"></span>
                                                </p>
                                            </div>
                                            <hr>
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Ubah
                                                    Status</label>
                                                <select x-model="formData.status" id="status"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                    required>
                                                    <option value="scheduled">Terjadwal</option>
                                                    <option value="in_progress">Dikerjakan</option>
                                                    <option value="completed">Selesai</option>
                                                    <option value="cancelled">Dibatalkan</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="notes"
                                                    class="block text-sm font-medium text-gray-700">Catatan
                                                    Perbaikan</label>
                                                <textarea x-model="formData.notes" id="notes" rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm"
                                        :disabled="isSubmitting">
                                        <span x-show="!isSubmitting">Simpan</span>
                                        <span x-show="isSubmitting">Menyimpan...</span>
                                    </button>
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
        function maintenanceCRUD(data) {
            return {
                maintenances: [],
                assets: data.assets || [],
                isLoading: true,
                isSubmitting: false,
                showModal: false,
                isEditMode: false,
                formData: {},
                notification: { show: false, message: '', type: 'success' },

                async init() {
                    await fetch('/sanctum/csrf-cookie');
                    this.getItems();
                    this.resetForm();
                },

                resetForm() {
                    this.formData = { id: null, asset_id: '', maintenance_type: 'repair', description_text: '', status: 'scheduled', notes: '' };
                },

                getItems() {
                    this.isLoading = true;
                    fetch('/api/maintenances', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => { this.maintenances = data; })
                        .catch(err => this.showNotification('Gagal memuat data.', 'error'))
                        .finally(() => this.isLoading = false);
                },

                openModal() {
                    this.isEditMode = false;
                    this.resetForm();
                    this.showModal = true;
                },

                closeModal() { this.showModal = false; },

                editItem(item) {
                    this.isEditMode = true;
                    this.formData = { ...item };
                    this.showModal = true;
                },

                saveItem() {
                    this.isSubmitting = true;
                    fetch('/api/maintenances', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(() => {
                        this.showNotification('Laporan kerusakan berhasil dibuat dan tugas perbaikan telah dibuat.', 'success');
                        this.getItems();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Gagal menyimpan laporan.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        this.showNotification(`Error: ${msg}`, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },

                updateItem() {
                    this.isSubmitting = true;
                    fetch(`/api/maintenances/${this.formData.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(() => {
                        this.showNotification('Status maintenance berhasil diperbarui.', 'success');
                        this.getItems();
                        this.closeModal();
                    })
                    .catch(err => {
                        let msg = 'Gagal memperbarui status.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        this.showNotification(`Error: ${msg}`, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },

                statusColor(status) {
                    const colors = {
                        scheduled: 'bg-gray-100 text-gray-800', in_progress: 'bg-blue-100 text-blue-800',
                        completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800',
                    };
                    return colors[status] || 'bg-gray-100';
                },

                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>