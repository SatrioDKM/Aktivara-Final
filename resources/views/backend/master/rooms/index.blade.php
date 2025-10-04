<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-door-open mr-2"></i>
            {{ __('Manajemen Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="roomsPage(@js($data['floors']))" x-cloak>
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

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div wire:ignore>
                                <label for="buildingFilter" class="sr-only">Filter Gedung</label>
                                <select id="buildingFilter" class="w-full">
                                    <option value="">Semua Gedung</option>
                                    @foreach ($data['buildings'] as $building)
                                    <option value="{{ $building->id }}">{{ $building->name_building }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div wire:ignore>
                                <label for="floorFilter" class="sr-only">Filter Lantai</label>
                                <select id="floorFilter" class="w-full">
                                    <option value="">Semua Lantai</option>
                                    {{-- Opsi diisi dinamis oleh Alpine.js --}}
                                </select>
                            </div>
                            <div class="relative">
                                <label for="search" class="sr-only">Pencarian</label>
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"><i
                                        class="fas fa-search text-gray-400"></i></div>
                                <input type="search" id="search" x-model.debounce.500ms="search"
                                    placeholder="Cari nama ruangan..."
                                    class="w-full ps-10 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Nama Ruangan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Lantai</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Gedung</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
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
                                        <td class="px-6 py-4" x-text="pagination.from + index"></td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100"
                                            x-text="room.name_room"></td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                            x-text="room.floor ? room.floor.name_floor : 'N/A'"></td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                            x-text="room.floor && room.floor.building ? room.floor.building.name_building : 'N/A'">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                                :class="room.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                                x-text="room.status === 'active' ? 'Aktif' : 'Tidak Aktif'">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
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

                    <div class="mt-4 flex flex-col md:flex-row justify-between items-center"
                        x-show="!isLoading && pagination.total > 0">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            Menampilkan <span x-text="pagination.from || 0"></span> sampai <span
                                x-text="pagination.to || 0"></span> dari <span x-text="pagination.total || 0"></span>
                            entri
                        </p>
                        <nav x-show="pagination.last_page > 1" class="flex items-center space-x-1">
                            <template x-for="link in pagination.links">
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700': !link.active && link.url, 'bg-white dark:bg-gray-800 text-gray-400 cursor-not-allowed dark:text-gray-600': !link.url }"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition border dark:border-gray-600"
                                    x-html="link.label"></button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                    this.$watch('floorFilter', () => this.applyFilters());

                    const self = this;
                    $('#buildingFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Gedung', allowClear: true })
                        .on('change', function() {
                            self.buildingFilter = $(this).val();
                            self.updateFloorOptions();
                        });

                    $('#floorFilter').select2({ theme: "classic", width: '100%', placeholder: 'Filter Lantai', allowClear: true })
                        .on('change', function() { self.floorFilter = $(this).val(); });

                    const toastMessage = sessionStorage.getItem('toastMessage');
                    if (toastMessage) {
                        window.iziToast.success({ title: 'Berhasil!', message: toastMessage, position: 'topRight' });
                        sessionStorage.removeItem('toastMessage');
                    }
                },

                updateFloorOptions() {
                    const floorSelect = $('#floorFilter');
                    floorSelect.empty().append('<option value="">Semua Lantai</option>');
                    if (this.buildingFilter) {
                        const filteredFloors = allFloors.filter(floor => floor.building_id == this.buildingFilter);
                        filteredFloors.forEach(floor => {
                            floorSelect.append(new Option(floor.name_floor, floor.id, false, false));
                        });
                    }
                    floorSelect.val('').trigger('change');
                },

                applyFilters() {
                    this.fetchRooms(1);
                },

                fetchRooms(page = 1) {
                    this.isLoading = true;
                    const params = new URLSearchParams({
                        page: page, perPage: 10, search: this.search,
                        building: this.buildingFilter, floor: this.floorFilter
                    });

                    axios.get(`/api/rooms?${params.toString()}`)
                    .then(res => {
                        this.rooms = res.data.data;
                        res.data.links.forEach(link => {
                            if (link.label.includes('Previous')) link.label = '<i class="fas fa-chevron-left"></i>';
                            if (link.label.includes('Next')) link.label = '<i class="fas fa-chevron-right"></i>';
                        });
                        this.pagination = res.data;
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data ruangan:", error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil data dari server.', position: 'topRight' });
                    })
                    .finally(() => this.isLoading = false);
                },

                changePage(url) {
                    if (!url) return;
                    this.fetchRooms(new URL(url).searchParams.get('page'));
                },

                confirmDelete(id) {
                    window.iziToast.question({
                        timeout: 20000, close: false, overlay: true,
                        title: 'Konfirmasi Hapus', message: 'Anda yakin ingin menghapus data ruangan ini?', position: 'center',
                        buttons: [
                            ['<button><b>YA, HAPUS</b></button>', (instance, toast) => {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                this.deleteRoom(id);
                            }, true],
                            ['<button>Batal</button>', (instance, toast) => instance.hide({ transitionOut: 'fadeOut' }, toast, 'button')],
                        ]
                    });
                },

                deleteRoom(id) {
                    axios.delete(`/api/rooms/${id}`)
                    .then(res => {
                        window.iziToast.success({ title: 'Berhasil', message: res.data.message || 'Data ruangan telah dihapus.', position: 'topRight' });
                        const isLastItemOnPage = this.rooms.length === 1 && this.pagination.current_page > 1;
                        this.fetchRooms(isLastItemOnPage ? this.pagination.current_page - 1 : this.pagination.current_page);
                    })
                    .catch(err => window.iziToast.error({ title: 'Gagal!', message: err.response?.data?.message || 'Gagal menghapus data.', position: 'topRight' }));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>