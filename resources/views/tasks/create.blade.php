<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Tugas Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div x-data="createTaskForm({
                buildings: {{ Js::from($buildings) }},
                floors: {{ Js::from($floors) }},
                rooms: {{ Js::from($rooms) }},
                assets: {{ Js::from($assets) }}
            })">
                <!-- Notifikasi -->
                <div x-show="notification.show" x-transition class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submitForm" x-ref="form" class="p-6 bg-white border-b border-gray-200">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="title" value="Judul Tugas" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" x-model="formData.title"
                                    required autofocus />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="priority" value="Tingkat Prioritas" />
                                    <select x-model="formData.priority" id="priority"
                                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="low">Rendah (Low)</option>
                                        <option value="medium">Sedang (Medium)</option>
                                        <option value="high">Tinggi (High)</option>
                                        <option value="critical">Kritis (Critical)</option>
                                    </select>
                                </div>

                                <!-- Dropdown Departemen hanya untuk Admin/Manager -->
                                @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                                <div>
                                    <x-input-label for="department_code" value="Departemen Tujuan" />
                                    <select x-model="formData.department_code" id="department_code"
                                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Departemen --</option>
                                        <option value="HK">Housekeeping</option>
                                        <option value="TK">Teknisi</option>
                                        <option value="SC">Security</option>
                                        <option value="PK">Parking</option>
                                    </select>
                                </div>
                                @endif
                            </div>

                            <div class="border-t pt-6 space-y-6">
                                <p class="text-sm text-gray-600">Detail Lokasi & Aset (Opsional)</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="building_id" value="Gedung" />
                                        <select x-model="selected.building" id="building_id"
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">-- Pilih Gedung --</option>
                                            <template x-for="building in buildings" :key="building.id">
                                                <option :value="building.id" x-text="building.name_building"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="floor_id" value="Lantai" />
                                        <select x-model="selected.floor" id="floor_id"
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                                            :disabled="!selected.building">
                                            <option value="">-- Pilih Lantai --</option>
                                            <template x-for="floor in filtered.floors" :key="floor.id">
                                                <option :value="floor.id" x-text="floor.name_floor"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="room_id" value="Ruangan" />
                                        <select x-model="formData.room_id" id="room_id"
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                                            :disabled="!selected.floor">
                                            <option value="">-- Pilih Ruangan --</option>
                                            <template x-for="room in filtered.rooms" :key="room.id">
                                                <option :value="room.id" x-text="room.name_room"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="asset_id" value="Aset Terkait" />
                                        <select x-model="formData.asset_id" id="asset_id"
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">-- Pilih Aset --</option>
                                            <template x-for="asset in assets" :key="asset.id">
                                                <option :value="asset.id" x-text="asset.name_asset"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="description" value="Deskripsi Tugas (Opsional)" />
                                <textarea x-model="formData.description" rows="4"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button ::disabled="isSubmitting">
                                    <span x-show="!isSubmitting">Buat Tugas</span>
                                    <span x-show="isSubmitting">Menyimpan...</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createTaskForm(data) {
            return {
                formData: {
                    title: '',
                    priority: 'medium',
                    description: '',
                    department_code: '',
                    due_date: '',
                    room_id: '',
                    asset_id: ''
                },
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },

                // Data dari controller
                buildings: data.buildings,
                allFloors: data.floors,
                allRooms: data.rooms,
                assets: data.assets,

                // State untuk dropdown dinamis
                selected: { building: '', floor: '' },
                filtered: { floors: [], rooms: [] },

                init() {
                    this.$watch('selected.building', (buildingId) => {
                        this.selected.floor = '';
                        this.formData.room_id = '';
                        this.filtered.rooms = [];
                        this.filtered.floors = buildingId ? this.allFloors.filter(f => f.building_id == buildingId) : [];
                    });

                    this.$watch('selected.floor', (floorId) => {
                        this.formData.room_id = '';
                        this.filtered.rooms = floorId ? this.allRooms.filter(r => r.floor_id == floorId) : [];
                    });
                },

                async submitForm() {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch('{{ route('api.tasks.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json(); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification('Tugas berhasil dibuat!', 'success');
                        this.$refs.form.reset();
                        this.formData = { title: '', priority: 'medium', description: '', department_code: '', due_date: '', room_id: '', asset_id: '' };
                        this.selected = { building: '', floor: '' };
                    })
                    .catch(err => {
                        let msg = 'Gagal membuat tugas.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        this.showNotification(`Error: ${msg}`, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) {
                        return decodeURIComponent(csrfCookie.split('=')[1]);
                    }
                    return '';
                }
            }
        }
    </script>
</x-app-layout>