<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['complaint']->title
                                }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Dilaporkan oleh: <span class="font-semibold">{{ $data['complaint']->reporter_name
                                    }}</span>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-user-circle me-2"></i>
                                Dicatat oleh {{ $data['complaint']->creator->name ?? 'N/A' }} pada
                                @tanggal($data['complaint']->created_at)
                            </p>
                        </div>
                        <div>
                            @php
                            $statusClass = [
                            'open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                            'converted_to_task' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                            ][$data['complaint']->status] ?? 'bg-gray-100';
                            $statusText = [
                            'open' => 'Terbuka',
                            'converted_to_task' => 'Jadi Tugas',
                            'closed' => 'Ditutup'
                            ][$data['complaint']->status] ?? $data['complaint']->status;
                            @endphp
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Lengkap</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{
                                    $data['complaint']->description }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Lokasi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['complaint']->location_text }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ruangan Terkait</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['complaint']->room->name_room ?? 'Tidak spesifik' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aset Terkait</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['complaint']->asset->name_asset ?? 'Tidak spesifik' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tugas yang Dihasilkan
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($data['complaint']->generatedTask)
                                    <a href="#" class="text-indigo-500 hover:underline">Lihat Tugas #{{
                                        $data['complaint']->generatedTask->id }}</a>
                                    @else
                                    <span>Belum dikonversi menjadi tugas.</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('complaints.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>