<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>
                {{ __('Detail Laporan') }}
            </h2>
            <a href="{{ route('complaints.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Header Detail Laporan --}}
                    <div
                        class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['complaint']->title
                                }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Dilaporkan oleh: <span class="font-semibold">{{ $data['complaint']->reporter_name
                                    }}</span>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-user-circle mr-2"></i>
                                Dicatat oleh {{ $data['complaint']->creator->name ?? 'Sistem' }} pada
                                @tanggal($data['complaint']->created_at)
                            </p>
                        </div>
                        <div>
                            @php
                            $status = $data['complaint']->status;
                            $statusClass = [
                            'open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                            'converted_to_task' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                            ][$status] ?? 'bg-gray-100';
                            $statusText = [
                            'open' => 'Terbuka',
                            'converted_to_task' => 'Jadi Tugas',
                            'closed' => 'Ditutup'
                            ][$status] ?? $status;
                            @endphp
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    {{-- Konten Detail --}}
                    <div>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- Deskripsi Lengkap --}}
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-align-left fa-fw mr-3"></i>Deskripsi Lengkap
                                </dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100 prose max-w-none">{{
                                    $data['complaint']->description }}</dd>
                            </div>

                            {{-- Deskripsi Lokasi --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-map-marker-alt fa-fw mr-3"></i>Deskripsi Lokasi
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['complaint']->location_text }}</dd>
                            </div>

                            {{-- Ruangan Terkait --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-door-open fa-fw mr-3"></i>Ruangan Terkait
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $data['complaint']->room ?
                                    ($data['complaint']->room->floor->building->name_building . ' / ' .
                                    $data['complaint']->room->floor->name_floor . ' / ' .
                                    $data['complaint']->room->name_room) : 'Tidak spesifik' }}
                                </dd>
                            </div>

                            {{-- Aset Terkait --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-box fa-fw mr-3"></i>Aset Terkait
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $data['complaint']->asset ? ($data['complaint']->asset->name_asset . ' (' .
                                    ($data['complaint']->asset->serial_number ?? 'Non-Serial') . ')') : 'Tidak spesifik'
                                    }}
                                </dd>
                            </div>

                            {{-- Tugas yang Dihasilkan --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-retweet fa-fw mr-3"></i>Tugas yang Dihasilkan
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($data['complaint']->task)
                                    <a href="{{ route('tasks.show', ['taskId' => $data['complaint']->task->id]) }}"
                                        class="text-indigo-500 hover:underline font-semibold">
                                        Lihat Tugas: {{ $data['complaint']->task->title }}
                                    </a>
                                    @else
                                    <span>Belum dikonversi menjadi tugas.</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>