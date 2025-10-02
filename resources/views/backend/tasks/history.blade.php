<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-book-open mr-2"></i>
            {{ __('Riwayat & Laporan Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div x-data="taskHistory(@js($data))" x-cloak>
                {{-- Card untuk Panel Filter --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-filter mr-3 text-gray-400"></i>Filter Laporan
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                            {{-- Filter Tanggal --}}
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                                    Tanggal</label>
                                <input type="date" x-model="filters.start_date" id="start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                                    Tanggal</label>
                                <input type="date" x-model="filters.end_date" id="end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Filter Status --}}
                            <div wire:ignore>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" class="mt-1 block w-full">
                                    <option value="">Semua Status</option>
                                    <option value="unassigned">Belum Diambil</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="pending_review">Review</option>
                                    <option value="completed">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>

                            {{-- Filter Staff (Untuk Leader, Manager, Admin) --}}
                            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']) ||
                            str_ends_with(Auth::user()->role_id, '01'))
                            <div wire:ignore>
                                <label for="staff_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Staff</label>
                                <select id="staff_id" class="mt-1 block w-full">
                                    <option value="">Semua Staff</option>
                                    @foreach($data['staffUsers'] as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Filter Departemen (Hanya untuk Manager & Admin) --}}
                            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                            <div wire:ignore>
                                <label for="department"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departemen</label>
                                <select id="department" class="mt-1 block w-full">
                                    <option value="">Semua Departemen</option>
                                    @foreach($data['departments'] as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            {{-- Filter Pencarian --}}
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                                <input type="text" x-model.debounce.500ms="filters.search" @input="applyFilters"
                                    id="search" placeholder="Cari judul tugas atau nama staff..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            {{-- Tombol Aksi --}}
                            <div class="flex items-end space-x-2">
                                <button @click="applyFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
                                    <i class="fas fa-search mr-2"></i>Terapkan Filter
                                </button>
                                <button @click="resetFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card untuk Tabel Hasil --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Judul Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jenis Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Staff</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tgl. Selesai</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400"><i
                                                class="fas fa-spinner fa-spin fa-2x"></i></td>
                                    </tr>
                                </template>
                                <template x-for="task in tasks.data" :key="task.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="task.title"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.task_type ? task.task_type.name_task : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="task.assignee ? task.assignee.name : 'N/A'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                            x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="statusColor(task.status)"
                                                x-text="statusText(task.status)"></span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a :href="`/tasks/${task.id}`"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Detail</a>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && (!tasks.data || tasks.data.length === 0)">
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">Tidak
                                            ada data yang cocok dengan filter Anda.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    {{-- Kontrol Paginasi --}}
                    <div class="p-4 flex justify-between items-center bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
                        x-show="!isLoading && tasks.total > 0">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Menampilkan <span class="font-medium" x-text="tasks.from || 0"></span> - <span
                                class="font-medium" x-text="tasks.to || 0"></span> dari <span class="font-medium"
                                x-text="tasks.total || 0"></span> hasil
                        </p>
                        <div class="flex space-x-2">
                            <button @click="fetchTasks(tasks.current_page - 1)" :disabled="!tasks.prev_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Sebelumnya</button>
                            <button @click="fetchTasks(tasks.current_page + 1)" :disabled="!tasks.next_page_url"
                                class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function taskHistory(data) {
            return {
                tasks: { data: [] },
                isLoading: true,
                filters: { start_date: '', end_date: '', status: '', staff_id: '', department: '', search: '' },

                init() {
                    const self = this;
                    $('#status').select2({ theme: 'classic', width: '100%', placeholder: 'Semua Status' }).on('change', function() { self.filters.status = $(this).val(); });
                    $('#staff_id').select2({ theme: 'classic', width: '100%', placeholder: 'Semua Staff' }).on('change', function() { self.filters.staff_id = $(this).val(); });
                    $('#department').select2({ theme: 'classic', width: '100%', placeholder: 'Semua Departemen' }).on('change', function() { self.filters.department = $(this).val(); });

                    this.applyFilters();
                },

                applyFilters() {
                    this.fetchTasks(1);
                },

                resetFilters() {
                    this.filters = { start_date: '', end_date: '', status: '', staff_id: '', department: '', search: '' };
                    $('#status, #staff_id, #department').val('').trigger('change');
                    this.fetchTasks(1);
                },

                fetchTasks(page = 1) {
                    this.isLoading = true;
                    const activeFilters = Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v != null && v !== ''));
                    const params = new URLSearchParams({ page, ...activeFilters }).toString();

                    axios.get(`{{ route('api.tasks.history_data') }}?${params}`)
                        .then(response => {
                            this.tasks = response.data;
                        })
                        .catch(error => {
                            console.error('Gagal mengambil data riwayat:', error);
                            window.iziToast.error({ title: 'Gagal!', message: 'Tidak dapat mengambil data dari server.', position: 'topRight' });
                        })
                        .finally(() => this.isLoading = false);
                },

                statusColor(status) {
                    const colors = {
                        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        'unassigned': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                },

                statusText(status) {
                    const texts = {
                        'completed': 'Selesai',
                        'pending_review': 'Review',
                        'in_progress': 'Dikerjakan',
                        'rejected': 'Ditolak',
                        'unassigned': 'Belum Diambil'
                    };
                    return texts[status] || status;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>