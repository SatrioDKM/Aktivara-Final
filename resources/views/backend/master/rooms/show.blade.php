<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['room']->name_room
                                }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Lokasi:
                                <a href="{{ route('master.buildings.show', $data['room']->floor->building->id) }}"
                                    class="text-indigo-500 hover:underline font-semibold">{{
                                    $data['room']->floor->building->name_building }}</a>
                                -
                                <a href="{{ route('master.floors.show', $data['room']->floor->id) }}"
                                    class="text-indigo-500 hover:underline font-semibold">{{
                                    $data['room']->floor->name_floor }}</a>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-user-circle me-2"></i>
                                Dibuat oleh {{ $data['room']->creator->name ?? 'N/A' }} pada
                                @tanggal($data['room']->created_at)
                            </p>
                        </div>
                        <div>
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $data['room']->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ $data['room']->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tambahkan detail lain yang relevan di sini jika ada --}}
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Informasi Tambahan</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    Informasi spesifik mengenai ruangan ini belum ditambahkan.
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.rooms.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('master.rooms.edit', $data['room']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-edit me-2"></i>
                            Edit Ruangan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>