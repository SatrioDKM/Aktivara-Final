<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Ruangan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="roomForm(@js($data['floors']))">
                    <form @submit.prevent="saveRoom()">
                        <div class="space-y-6">
                            <div>
                                <label for="building_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gedung</label>
                                <select id="building_id" class="mt-1 block w-full" required>
                                    <option value="">-- Pilih Gedung --</option>
                                    @foreach ($data['buildings'] as $building)
                                    <option value="{{ $building->id }}">{{ $building->name_building }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="floor_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lantai</label>
                                <select id="floor_id" class="mt-1 block w-full" required>
                                    <option value="">-- Pilih Gedung Terlebih Dahulu --</option>
                                </select>
                            </div>
                            <div>
                                <label for="name_room"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Ruangan</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-door-open text-gray-400"></i></div>
                                    <input type="text" x-model="formData.name_room" id="name_room"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: Ruang Rapat Sakura" required>
                                </div>
                            </div>
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" x-model="formData.status" class="mt-1 block w-full" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.rooms.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Simpan</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function roomForm(allFloors) {
                return {
                    isSubmitting: false,
                    formData: { name_room: '', floor_id: '', status: 'active', building_id: '' },
                    init() {
                        $('#status').select2({ theme: "classic", width: '100%', minimumResultsForSearch: Infinity });

                        $('#building_id').select2({ theme: "classic", width: '100%' })
                            .on('change', (e) => this.updateFloorOptions(e.target.value));

                        $('#floor_id').select2({ theme: "classic", width: '100%' });
                    },
                    updateFloorOptions(buildingId) {
                        const floorSelect = $('#floor_id');
                        floorSelect.empty().append('<option value="">-- Pilih Lantai --</option>');
                        if (buildingId) {
                            const filtered = allFloors.filter(floor => floor.building_id == buildingId);
                            filtered.forEach(floor => {
                                floorSelect.append(new Option(floor.name_floor, floor.id, false, false));
                            });
                        }
                        floorSelect.trigger('change');
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async saveRoom() {
                        this.isSubmitting = true;
                        this.formData.floor_id = $('#floor_id').val();
                        this.formData.status = $('#status').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch('/api/rooms', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Ruangan baru berhasil ditambahkan!');
                            window.location.href = "{{ route('master.rooms.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Pastikan semua field terisi.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                            this.isSubmitting = false;
                        });
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>