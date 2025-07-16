<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Tugas Aktif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="monitoringTasks()">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Tugas</label>
                                <input type="text" x-model.debounce.500ms="filters.search" @input="applyFilters"
                                    id="search" placeholder="Cari berdasarkan judul atau nama staff..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select x-model="filters.status" @change="applyFilters" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua Status Aktif</option>
                                    {{-- TAMBAHKAN OPSI INI --}}
                                    <option value="unassigned">Belum Diambil</option>
                                    <option value="in_progress">Sedang Dikerjakan</option>
                                    <option value="pending_review">Menunggu Review</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Daftar Tugas</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul Tugas</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dikerjakan Oleh</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                    <template x-for="(task, index) in tasks" :key="task.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="task.title">
                                            </td>
                                            {{-- UBAH LOGIKA INI untuk menangani staff yang null --}}
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.staff ? task.staff.name : 'Belum Diambil'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.room ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor}` : 'Tidak spesifik'">
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(task.status)"
                                                    x-text="task.status.replace('_', ' ')"></span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a :href="`/tasks/${task.id}`"
                                                    class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && tasks.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas
                                                yang cocok dengan filter Anda.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function monitoringTasks() {
            return {
                tasks: [],
                isLoading: true,
                filters: {
                    search: '',
                    status: ''
                },
                init() {
                    this.applyFilters();
                },
                applyFilters() {
                    this.isLoading = true;
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v != ''));
                    const params = new URLSearchParams(activeFilters).toString();

                    // UBAH ENDPOINT API DI SINI
                    fetch(`{{ route('api.tasks.active_data') }}?${params}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal memuat data tugas.');
                        return res.json();
                    })
                    .then(data => {
                        this.tasks = data;
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },
                statusColor(status) {
                    const colors = {
                        // TAMBAHKAN WARNA UNTUK STATUS BARU
                        'unassigned': 'bg-gray-200 text-gray-800',
                        'in_progress': 'bg-blue-100 text-blue-800',
                        'pending_review': 'bg-yellow-100 text-yellow-800',
                    };
                    return colors[status] || 'bg-gray-100';
                }
            }
        }
    </script>
</x-app-layout>