<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tugas Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="myTasks()">
                <div class="space-y-4">
                    <template x-if="isLoading">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                            Memuat tugas...
                        </div>
                    </template>

                    <template x-for="task in tasks" :key="task.id">
                        <a :href="`/tasks/${task.id}`"
                            class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg text-gray-800" x-text="task.title"></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Lokasi: <span
                                            x-text="task.room ? `${task.room.name_room}, ${task.room.floor.name_floor}` : 'Tidak spesifik'"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                            'bg-red-100 text-red-800': task.status === 'rejected',
                                            'bg-yellow-100 text-yellow-800': task.status === 'pending_review'
                                          }"
                                        x-text="task.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())">
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>

                    <template x-if="!isLoading && tasks.length === 0">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <p class="text-gray-500">Anda tidak memiliki tugas aktif saat ini.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function myTasks() {
            return {
                tasks: [],
                isLoading: true,
                async init() {
                    this.isLoading = true;
                    fetch('{{ route('api.tasks.my_tasks_data') }}', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            this.tasks = data;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.isLoading = false;
                        });
                }
            }
        }
    </script>
</x-app-layout>