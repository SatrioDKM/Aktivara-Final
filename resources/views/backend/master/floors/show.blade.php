<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Lantai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
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
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $data['floor']->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $data['floor']->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">Daftar Ruangan di Lantai Ini
                        </h4>
                        <div class="mt-4">
                            @forelse ($data['floor']->rooms as $room)
                            <div
                                class="p-3 rounded-md border dark:border-gray-700 flex justify-between items-center mb-2">
                                <span class="text-gray-700 dark:text-gray-300">{{ $room->name_room }}</span>
                                <span
                                    class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $room->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center py-4 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                <p class="text-gray-500 dark:text-gray-400">Belum ada ruangan yang ditambahkan di lantai
                                    ini.</p>
                            </div>
                            @endforelse
                            </ul>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                            <a href="{{ route('master.floors.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-arrow-left me-2"></i>
                                Kembali
                            </a>
                            <a href="{{ route('master.floors.edit', $data['floor']->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <i class="fas fa-edit me-2"></i>
                                Edit Lantai
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>