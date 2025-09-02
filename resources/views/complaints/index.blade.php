<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Masuk & Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="complaintsCRUD({
                rooms: {{ Js::from($rooms) }},
                assets: {{ Js::from($assets) }},
                taskTypes: {{ Js::from(App\Models\TaskType::orderBy('name_task')->get()) }}
            })">

                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-700">Daftar Laporan</h3>
                            <button @click="openCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                Catat Laporan Baru
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Judul</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Dicatat Oleh</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Memuat data
                                                laporan...</td>
                                        </tr>
                                    </template>
                                    <template x-for="item in complaints" :key="item.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="item.title">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.location_text">
                                            </td>
                                            <td class="px-6 py-4"><span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(item.status)"
                                                    x-text="statusText(item.status)"></span></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.creator.name"></td>
                                            <td class="px-6 py-4 text-center text-sm font-medium">
                                                <button @click="openConversionModal(item)"
                                                    x-show="item.status === 'open'"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    Jadikan Tugas
                                                </button>
                                                <a :href="item.generated_task ? `/tasks/${item.generated_task.id}` : '#'"
                                                    x-show="item.status === 'converted_to_task'"
                                                    class="text-green-600 hover:text-green-800 text-xs italic">
                                                    Lihat Tugas
                                                </a>
                                                <span x-show="item.status === 'closed'"
                                                    class="text-gray-400 text-xs italic">Ditutup</span>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && complaints.length === 0">
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada laporan
                                                yang tercatat.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showCreateModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                            <form @submit.prevent="saveItem()">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catat Laporan / Keluhan Baru</h3>
                                    <div class="space-y-4">
                                        <div><label class="block text-sm font-medium">Judul Laporan</label><input
                                                type="text" x-model="formData.title"
                                                class="mt-1 block w-full rounded-md" required></div>
                                        <div><label class="block text-sm font-medium">Nama Pelapor</label><input
                                                type="text" x-model="formData.reporter_name"
                                                class="mt-1 block w-full rounded-md" required></div>
                                        <div><label class="block text-sm font-medium">Deskripsi Lokasi</label><input
                                                type="text" x-model="formData.location_text"
                                                class="mt-1 block w-full rounded-md" required></div>
                                        <div><label class="block text-sm font-medium">Deskripsi Laporan</label><textarea
                                                x-model="formData.description" rows="4"
                                                class="mt-1 block w-full rounded-md" required></textarea></div>
                                        <div><label class="block text-sm font-medium">Ruangan Terkait
                                                (Opsional)</label><select x-model="formData.room_id"
                                                class="mt-1 block w-full rounded-md">
                                                <option value="">-- Pilih Ruangan --</option><template
                                                    x-for="room in rooms" :key="room.id">
                                                    <option :value="room.id"
                                                        x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                    </option>
                                                </template>
                                            </select></div>
                                        <div><label class="block text-sm font-medium">Aset Terkait
                                                (Opsional)</label><select x-model="formData.asset_id"
                                                class="mt-1 block w-full rounded-md">
                                                <option value="">-- Pilih Aset --</option><template
                                                    x-for="asset in assets" :key="asset.id">
                                                    <option :value="asset.id" x-text="asset.name_asset"></option>
                                                </template>
                                            </select></div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 sm:ml-3 sm:w-auto sm:text-sm"
                                        :disabled="isSubmitting"><span x-show="!isSubmitting">Simpan</span><span
                                            x-show="isSubmitting">Menyimpan...</span></button>
                                    <button type="button" @click="showCreateModal = false"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-show="showConversionModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showConversionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                            <form @submit.prevent="convertItem()">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Konversi Laporan Menjadi Tugas
                                    </h3>
                                    <div class="p-3 bg-gray-50 rounded-md border mb-4">
                                        <p class="text-sm font-medium text-gray-600">Laporan: <strong
                                                class="text-gray-900" x-text="conversionData.title"></strong></p>
                                    </div>
                                    <div class="space-y-4">
                                        <div><label class="block text-sm font-medium">Jenis Tugas</label><select
                                                x-model="conversionData.task_type_id"
                                                class="mt-1 block w-full rounded-md" required>
                                                <option value="">-- Pilih Jenis Tugas --</option><template
                                                    x-for="tt in taskTypes" :key="tt.id">
                                                    <option :value="tt.id"
                                                        x-text="`${tt.name_task} (${tt.departemen})`"></option>
                                                </template>
                                            </select></div>
                                        <div><label class="block text-sm font-medium">Prioritas</label><select
                                                x-model="conversionData.priority" class="mt-1 block w-full rounded-md"
                                                required>
                                                <option value="low">Rendah</option>
                                                <option value="medium">Sedang</option>
                                                <option value="high">Tinggi</option>
                                                <option value="critical">Kritis</option>
                                            </select></div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 sm:ml-3 sm:w-auto sm:text-sm"
                                        :disabled="isSubmitting"><span x-show="!isSubmitting">Konversi</span><span
                                            x-show="isSubmitting">Memproses...</span></button>
                                    <button type="button" @click="showConversionModal = false"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function complaintsCRUD(data) {
            return {
                complaints: [],
                rooms: data.rooms || [],
                assets: data.assets || [],
                taskTypes: data.taskTypes || [],
                isLoading: true, isSubmitting: false,
                showCreateModal: false, showConversionModal: false,
                formData: {},
                conversionData: {},
                notification: { show: false, message: '', type: 'success' },

                async init() {
                    await fetch('/sanctum/csrf-cookie');
                    this.getItems();
                },
                getItems() {
                    this.isLoading = true;
                    fetch('{{ route('api.complaints.index') }}', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => { this.complaints = data; })
                        .finally(() => this.isLoading = false);
                },
                openModal() {
                    this.formData = { title: '', description: '', reporter_name: '', location_text: '', room_id: '', asset_id: '' };
                    this.showCreateModal = true;
                },
                saveItem() {
                    this.isSubmitting = true;
                    fetch('{{ route('api.complaints.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => res.ok ? res.json() : Promise.reject(await res.json()))
                    .then(() => {
                        this.showNotification('Laporan baru berhasil dicatat.', 'success');
                        this.getItems();
                        this.showCreateModal = false;
                    })
                    .catch(err => {
                        let msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Terjadi kesalahan.');
                        this.showNotification(`Error: ${msg}`, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },
                openConversionModal(item) {
                    this.conversionData = { id: item.id, title: item.title, task_type_id: '', priority: 'medium' };
                    this.showConversionModal = true;
                },
                convertItem() {
                    this.isSubmitting = true;
                    fetch(`/api/complaints/${this.conversionData.id}/convert`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify({ task_type_id: this.conversionData.task_type_id, priority: this.conversionData.priority })
                    })
                    .then(async res => res.ok ? res.json() : Promise.reject(await res.json()))
                    .then(data => {
                        this.showNotification(data.message, 'success');
                        this.getItems();
                        this.showConversionModal = false;
                    })
                    .catch(err => this.showNotification(`Error: ${err.message || 'Gagal.'}`, 'error'))
                    .finally(() => this.isSubmitting = false);
                },
                statusColor(status) {
                    return { open: 'bg-yellow-100 text-yellow-800', converted_to_task: 'bg-blue-100 text-blue-800', closed: 'bg-gray-100 text-gray-800' }[status] || '';
                },
                statusText(status) {
                    return { open: 'Terbuka', converted_to_task: 'Jadi Tugas', closed: 'Ditutup' }[status] || status;
                },
                getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
                showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); }
            }
        }
    </script>
</x-app-layout>