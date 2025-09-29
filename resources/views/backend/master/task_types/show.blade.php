<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Jenis Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['taskType']->name_task }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Departemen: {{ $data['taskType']->departemen }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full capitalize
                            @switch($data['taskType']->priority_level)
                                @case('low') bg-blue-100 text-blue-800 @break
                                @case('medium') bg-yellow-100 text-yellow-800 @break
                                @case('high') bg-orange-100 text-orange-800 @break
                                @case('critical') bg-red-100 text-red-800 @break
                            @endswitch">
                            Prioritas {{ $data['taskType']->priority_level }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl>
                            <div
                                class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 rounded-t-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{
                                    $data['taskType']->description ?? 'Tidak ada deskripsi.' }}</dd>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Pada</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    @tanggal($data['taskType']->created_at)</dd>
                            </div>
                            <div
                                class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 rounded-b-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Diperbarui Pada</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                    @tanggal($data['taskType']->updated_at)</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.task_types.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('master.task_types.edit', $data['taskType']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-edit me-2"></i>
                            Edit Jenis Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>