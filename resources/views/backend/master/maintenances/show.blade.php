<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Maintenance Aset') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['maintenance']->asset->name_asset }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                Laporan untuk tipe: <span class="font-semibold capitalize">{{
                                    $data['maintenance']->maintenance_type }}</span>
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Dilaporkan pada @tanggal($data['maintenance']->created_at)
                            </p>
                        </div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full capitalize {{ [
                            'scheduled' => 'bg-gray-100 text-gray-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ][$data['maintenance']->status] ?? 'bg-gray-100' }}">
                            {{ str_replace('_', ' ', $data['maintenance']->status) }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi Laporan
                                    Kerusakan</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->description }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teknisi yang
                                    Mengerjakan</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->technician->name ?? 'Belum Ditugaskan' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tugas Terkait</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{-- ================== PERBAIKAN DI SINI ================== --}}
                                    @if($data['maintenance']->generatedTask)
                                    <a href="#" class="text-indigo-500 hover:underline">Lihat Tugas #{{
                                        $data['maintenance']->generatedTask->id }}</a>
                                    @else
                                    <span>Tugas belum dibuat atau dihapus.</span>
                                    @endif
                                    {{-- ======================================================= --}}
                                </dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Perbaikan</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{
                                    $data['maintenance']->notes ?? 'Tidak ada catatan.' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.maintenances.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('master.maintenances.edit', $data['maintenance']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="fas fa-edit me-2"></i>
                            Update Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>