<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-door-open mr-2"></i>
                {{ __('Detail Ruangan') }}
            </h2>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('master.rooms.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
                <a href="{{ route('master.rooms.edit', $data['room']->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Header Detail --}}
                    <div
                        class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['room']->name_room
                                }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Lokasi:
                                <a href="{{ route('master.buildings.show', $data['room']->floor->building->id) }}"
                                    class="text-indigo-500 hover:underline font-semibold">{{
                                    $data['room']->floor->building->name_building }}</a>
                                /
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
                            @if($data['room']->status == 'active')
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Aktif
                            </span>
                            @else
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Tidak Aktif
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Konten Detail --}}
                    <div>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-info-circle fa-fw mr-3"></i>Informasi Tambahan
                                </dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    Informasi spesifik mengenai ruangan ini (seperti kapasitas, fasilitas, dll.) belum
                                    ditambahkan.
                                </dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>