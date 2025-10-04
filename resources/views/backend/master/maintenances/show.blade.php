<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-tools mr-2"></i>
                {{ __('Detail Maintenance Aset') }}
            </h2>
            <a href="{{ route('master.maintenances.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8">

                    {{-- Header Detail Maintenance --}}
                    <div
                        class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['maintenance']->asset->name_asset ?? 'Aset Telah Dihapus' }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Laporan untuk tipe: <span class="font-semibold capitalize">{{
                                    $data['maintenance']->maintenance_type }}</span>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Dilaporkan pada @tanggal($data['maintenance']->created_at)
                            </p>
                        </div>
                        <div>
                            @php
                            $status = $data['maintenance']->status;
                            $statusClass = [
                            'scheduled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                            ][$status] ?? 'bg-gray-100';
                            @endphp
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full capitalize {{ $statusClass }}">
                                {{ str_replace('_', ' ', $status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Konten Detail --}}
                    <div>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- Deskripsi Kerusakan --}}
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-comment-dots fa-fw mr-3"></i>Deskripsi Laporan Kerusakan
                                </dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->description }}</dd>
                            </div>

                            {{-- Teknisi --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-user-cog fa-fw mr-3"></i>Teknisi yang Mengerjakan
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->technician->name ?? 'Belum Ditugaskan' }}</dd>
                            </div>

                            {{-- Tugas Terkait --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-tasks fa-fw mr-3"></i>Tugas Terkait
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($data['maintenance']->task)
                                    <a href="{{ route('tasks.show', ['taskId' => $data['maintenance']->task->id]) }}"
                                        class="text-indigo-500 hover:underline font-semibold">
                                        Lihat Tugas #{{ $data['maintenance']->task->id }}
                                    </a>
                                    @else
                                    <span>Tugas belum dibuat atau telah dihapus.</span>
                                    @endif
                                </dd>
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-calendar-check fa-fw mr-3"></i>Tanggal Selesai
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($data['maintenance']->end_date)
                                    @tanggal($data['maintenance']->end_date)
                                    @else
                                    -
                                    @endif
                                </dd>
                            </div>

                            {{-- Catatan Perbaikan --}}
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-sticky-note fa-fw mr-3"></i>Catatan Perbaikan
                                </dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->notes ?? 'Tidak ada catatan.' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end">
                        <a href="{{ route('master.maintenances.edit', $data['maintenance']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900">
                            <i class="fas fa-edit me-2"></i>
                            Update Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>