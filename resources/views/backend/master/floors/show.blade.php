<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-layer-group mr-2"></i>
                {{ __('Detail Lantai') }}
            </h2>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('master.floors.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
                <a href="{{ route('master.floors.edit', $data['floor']->id) }}"
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
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['floor']->name_floor }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Berada di <a href="{{ route('master.buildings.show', $data['floor']->building->id) }}"
                                    class="text-indigo-500 hover:underline font-semibold">{{
                                    $data['floor']->building->name_building }}</a>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-user-circle me-2"></i>
                                Dibuat oleh {{ $data['floor']->creator->name ?? 'N/A' }} pada
                                @tanggal($data['floor']->created_at)
                            </p>
                        </div>
                        <div>
                            @if($data['floor']->status == 'active')
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
                        <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">Daftar Ruangan di Lantai Ini
                        </h4>
                        <div class="mt-4">
                            <div class="border dark:border-gray-700 rounded-md max-h-96 overflow-y-auto">
                                <ul class="divide-y dark:divide-gray-700">
                                    @forelse ($data['floor']->rooms as $room)
                                    <li class="p-4 flex justify-between items-center">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $room->name_room }}</span>
                                        <span
                                            class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $room->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ $room->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </li>
                                    @empty
                                    <li class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada ruangan yang ditambahkan di lantai ini.
                                    </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>