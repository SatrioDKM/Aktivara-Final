<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Jenis Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $data['taskType']->name_task }}</h3>
                            <p class="text-md text-gray-500">
                                Departemen: {{ $data['taskType']->departemen }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full capitalize" :class="{
                                  'bg-blue-100 text-blue-800': '{{ $data['taskType']->priority_level }}' === 'low',
                                  'bg-yellow-100 text-yellow-800': '{{ $data['taskType']->priority_level }}' === 'medium',
                                  'bg-orange-100 text-orange-800': '{{ $data['taskType']->priority_level }}' === 'high',
                                  'bg-red-100 text-red-800': '{{ $data['taskType']->priority_level }}' === 'critical'
                              }">
                            Prioritas {{ $data['taskType']->priority_level }}
                        </span>
                    </div>

                    <div class="border-t pt-6">
                        <dl>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-base">{{ $data['taskType']->description ?? 'Tidak ada deskripsi.'
                                    }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.task_types.index') }}" class="...">Kembali</a>
                        <a href="{{ route('master.task_types.edit', $data['taskType']->id) }}" class="...">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>