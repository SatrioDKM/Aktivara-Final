<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Gedung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['building']->name_building }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-user-circle me-2"></i>
                                Dibuat oleh {{ $data['building']->creator->name ?? 'N/A' }} pada
                                @tanggal($data['building']->created_at)
                            </p>
                        </div>
                        <div>
                            @if($data['building']->status == 'active')
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak
                                Aktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100 flex items-start">
                                    <i class="fas fa-map-marker-alt me-3 mt-1 text-gray-400"></i>
                                    <span>{{ $data['building']->address ?? 'Tidak ada alamat.' }}</span>
                                </dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Daftar Lantai & Ruangan
                                </dt>
                                <dd class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                    <ul class="list-disc list-inside space-y-2">
                                        @forelse ($data['building']->floors as $floor)
                                        <li class="font-semibold">{{ $floor->name_floor }}
                                            <ul class="list-disc list-inside ps-6 font-normal">
                                                @forelse($floor->rooms as $room)
                                                <li>{{ $room->name_room }}</li>
                                                @empty
                                                <li class="text-gray-500">Belum ada ruangan di lantai ini.</li>
                                                @endforelse
                                            </ul>
                                        </li>
                                        @empty
                                        <p class="text-gray-500">Belum ada lantai di gedung ini.</p>
                                        @endforelse
                                    </ul>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.buildings.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <a href="{{ route('master.buildings.edit', $data['building']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase">
                            <i class="fas fa-edit me-2"></i> Edit Gedung
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>