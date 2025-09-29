<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="roomsPage(@js($data['floors']))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Daftar Ruangan
                        </h3>
                        <a href="{{ route('master.rooms.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Ruangan
                        </a>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select id="buildingFilter" class="w-full">
                                <option value="">Semua Gedung</option>
                                @foreach ($data['buildings'] as $building)
                                <option value="{{ $building->id }}">{{ $building->name_building }}</option>
                                @endforeach
                            </select>
                            <select id="floorFilter" class="w-full">
                                <option value="">Semua Lantai</option>
                                {{-- Opsi lantai akan diisi oleh Alpine.js secara dinamis --}}
                            </select>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" x-model.debounce.500ms="search" placeholder="Cari nama ruangan..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Ruangan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Lantai</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Gedung</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center py-10"><i
                                                class="fas fa-spinner fa-spin fa-2x text-gray-400"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && rooms.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-500 dark:text-gray-400">Tidak
                                            ada data ditemukan.</td>
                                    </tr>
                                </template>
                                <template x-for="(room, index) in rooms" :key="room.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="room.name_room"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="room.floor ? room.floor.name_floor : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="room.floor && room.floor.building ? room.floor.building.name_building : 'N/A'">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="room.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                                x-text="room.status === 'active' ? 'Aktif' : 'Tidak Aktif'">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a :href="`/master/rooms/${room.id}`"
                                                    class="p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                                    title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                                <a :href="`/master/rooms/${room.id}/edit`"
                                                    class="p-2 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-600 transition"
                                                    title="Edit"><i class="fas fa-edit"></i></a>
                                                <button @click="confirmDelete(room.id)"
                                                    class="p-2 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-600 transition"
                                                    title="Hapus"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                            <template x-for="link in pagination.links">
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition"
                                    x-html="link.label"></button>
                            </template>
                        </nav>
                    </div>
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
        function roomsPage(allFloors) {
                return {
                    rooms: [],
                    pagination: {},
                    isLoading: true,
                    search: '',
                    buildingFilter: '',
                    floorFilter: '',

                    init() {
                        this.fetchRooms();
                        this.$watch('search', () => this.applyFilters());

                        $('#buildingFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Gedung', allowClear: true })
                            .on('change', (e) => {
                                this.buildingFilter = e.target.value;
                                this.updateFloorOptions(); // Panggil fungsi untuk update lantai
                            });

                        $('#floorFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Lantai', allowClear: true })
                            .on('change', (e) => this.floorFilter = e.target.value);

                        this.$watch('floorFilter', () => this.applyFilters());

                        const toastMessage = sessionStorage.getItem('toastMessage');
                        if (toastMessage) {
                            iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                            sessionStorage.removeItem('toastMessage');
                        }
                    },
                    updateFloorOptions() {
                        const selectedBuilding = this.buildingFilter;
                        const floorSelect = $('#floorFilter');

                        // Kosongkan opsi lantai saat gedung berubah
                        floorSelect.empty().append('<option value="">Semua Lantai</option>');

                        if (selectedBuilding) {
                            // Filter lantai berdasarkan gedung yang dipilih
                            const filtered = allFloors.filter(floor => floor.building_id == selectedBuilding);
                            filtered.forEach(floor => {
                                floorSelect.append(new Option(floor.name_floor, floor.id, false, false));
                            });
                        }
                        // Reset filter lantai dan panggil ulang data
                        floorSelect.val('').trigger('change');
                    },
                    applyFilters() {
                        this.fetchRooms(1);
                    },
                    fetchRooms(page = 1) {
                        this.isLoading = true;
                        const params = new URLSearchParams({
                            page: page,
                            perPage: 10,
                            search: this.search,
                            building: this.buildingFilter,
                            floor: this.floorFilter
                        });

                        fetch(`/api/rooms?${params.toString()}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json()).then(data => {
                            this.rooms = data.data;
                            data.links.forEach(link => {
                                if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                                if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                            });
                            this.pagination = data;
                            this.isLoading = false;
                        });
                    },
                    changePage(url) {
                        if (!url) return;
                        this.fetchRooms(new URL(url).searchParams.get('page'));
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    confirmDelete(id) {
                        iziToast.question({
                            timeout: 20000, close: false, overlay: true,
                            title: 'Konfirmasi Hapus',
                            message: 'Anda yakin ingin menghapus data ini?',
                            position: 'center',
                            buttons: [
                                ['<button><b>YA</b></button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    this.deleteRoom(id);
                                }, true],
                                ['<button>Batal</button>', (instance, toast) => {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                }],
                            ]
                        });
                    },
                    async deleteRoom(id) {
                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/rooms/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                        })
                        .then(response => {
                            if (response.ok) {
                                iziToast.success({ title: 'Berhasil', message: 'Data ruangan telah dihapus.', position: 'topRight' });
                                this.fetchRooms(this.pagination.current_page);
                            } else { throw new Error('Gagal menghapus data.'); }
                        })
                        .catch(error => iziToast.error({ title: 'Gagal', message: error.message, position: 'topRight' }));
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>