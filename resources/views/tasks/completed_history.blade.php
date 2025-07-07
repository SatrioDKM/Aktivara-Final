<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Tugas Selesai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="completedHistory()">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Daftar Tugas Selesai</h3>

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
                                            Dibuat Oleh</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Selesai</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="isLoading">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat
                                                riwayat...</td>
                                        </tr>
                                    </template>
                                    <template x-for="(task, index) in tasks" :key="task.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="task.title">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.staff ? task.staff.name : 'N/A'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="task.creator ? task.creator.name : 'N/A'"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"
                                                x-text="new Date(task.updated_at).toLocaleDateString('id-ID')"></td>
                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a :href="`/tasks/${task.id}`"
                                                    class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!isLoading && tasks.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada
                                                riwayat tugas yang selesai.</td>
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
        function completedHistory() {
            return {
                tasks: [],
                isLoading: true,
                init() {
                    this.isLoading = true;
                    fetch('{{ route('api.tasks.completed_history_data') }}', {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.tasks = data;
                        this.isLoading = false;
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memuat riwayat tugas.');
                        this.isLoading = false;
                    });
                }
            }
        }
    </script>
</x-app-layout>