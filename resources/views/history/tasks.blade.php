<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Laporan Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="taskHistory()">
                <!-- Panel Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Filter Laporan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Filter Tanggal Mulai -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Dari
                                    Tanggal</label>
                                <input type="date" x-model="filters.start_date" id="start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <!-- Filter Tanggal Selesai -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai
                                    Tanggal</label>
                                <input type="date" x-model="filters.end_date" id="end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <!-- Filter Jenis Tugas -->
                            <div>
                                <label for="task_type_id" class="block text-sm font-medium text-gray-700">Jenis
                                    Tugas</label>
                                <select x-model="filters.task_type_id" id="task_type_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua Jenis</option>
                                    @foreach($taskTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name_task }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filter Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select x-model="filters.status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="pending_review">Review</option>
                                    <option value="completed">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <!-- Tombol Aksi -->
                            <div class="flex items-end space-x-2">
                                <button @click="applyFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
                                <button @click="resetFilters"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Hasil -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <!-- ... (thead sama seperti sebelumnya) ... -->
                                <tbody>
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="6" class="py-4 text-center">Memuat data...</td>
                                        </tr>
                                    </template>
                                    <template x-for="(task, index) in tasks" :key="task.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="task.title">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.task_type.name_task"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="task.staff.name"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="new Date(task.created_at).toLocaleDateString('id-ID')"></td>
                                            <td class="px-6 py-4"><span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="statusColor(task.status)"
                                                    x-text="task.status.replace('_', ' ')"></span></td>
                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a :href="`/tasks/${task.id}`"
                                                    class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && tasks.length === 0">
                                        <tr>
                                            <td colspan="6" class="py-4 text-center">Tidak ada data yang cocok dengan
                                                filter Anda.</td>
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
        function taskHistory() {
            return {
                tasks: [],
                isLoading: true,
                filters: {
                    start_date: '',
                    end_date: '',
                    task_type_id: '',
                    status: '',
                    staff_id: ''
                },
                init() {
                    this.applyFilters();
                },
                applyFilters() {
                    this.isLoading = true;
                    const params = new URLSearchParams(this.filters).toString();
                    fetch(`{{ route('api.tasks.history_data') }}?${params}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => { this.tasks = data; })
                    .finally(() => this.isLoading = false);
                },
                resetFilters() {
                    this.filters = { start_date: '', end_date: '', task_type_id: '', status: '', staff_id: '' };
                    this.applyFilters();
                },
                statusColor(status) {
                    switch (status) {
                        case 'completed':
                            return 'bg-green-100 text-green-800';
                        case 'pending_review':
                            return 'bg-yellow-100 text-yellow-800';
                        case 'in_progress':
                            return 'bg-blue-100 text-blue-800';
                        case 'rejected':
                            return 'bg-red-100 text-red-800';
                        default:
                            return 'bg-gray-100 text-gray-800';
                    }
                }
            }
        }
    </script>
</x-app-layout>