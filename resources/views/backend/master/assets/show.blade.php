<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Detail Aset') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $data['asset']->name_asset }}</h3>
                            <p class="mt-1 text-md text-gray-500 dark:text-gray-400">
                                S/N: {{ $data['asset']->serial_number ?? 'N/A' }} | 
                                Kategori: {{ $data['asset']->AssetCategory->name ?? '-' }}
                            </p>

                        </div>
                        <span
                            class="mt-2 md:mt-0 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full capitalize"
                            :class="{
                                  'bg-green-100 text-green-800': '{{ $data['asset']->status }}' === 'available',
                                  'bg-blue-100 text-blue-800': '{{ $data['asset']->status }}' === 'in_use',
                                  'bg-yellow-100 text-yellow-800': '{{ $data['asset']->status }}' === 'maintenance',
                                  'bg-gray-100 text-gray-800': '{{ $data['asset']->status }}' === 'disposed'
                              }">
                            {{ str_replace('_', ' ', $data['asset']->status) }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $data['asset']->room ?
                                    $data['asset']->room->name_room . ', ' . $data['asset']->room->floor->name_floor :
                                    'Gudang' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lokasi Spesifik</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $data['asset']->location_detail ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipe Aset</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $data['asset']->asset_type
                                    == 'fixed_asset' ? 'Aset Tetap' : 'Barang Habis Pakai' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kondisi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $data['asset']->condition
                                    ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stok Saat Ini</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['asset']->current_stock }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stok Minimum</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['asset']->minimum_stock }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tgl. Pembelian</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                                    $data['asset']->purchase_date ?
                                    \Carbon\Carbon::parse($data['asset']->purchase_date)->translatedFormat('d F Y') :
                                    'N/A' }}</dd>
                            </div>
                            <div class="lg:col-span-3">
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $data['asset']->description
                                    ?? 'Tidak ada deskripsi.' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div
                        class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Riwayat Perbaikan</h4>
                            <ul class="space-y-3 max-h-60 overflow-y-auto">
                                @forelse ($data['asset']->maintenances as $maintenance)
                                <li class="border dark:border-gray-700 p-3 rounded-md text-sm">
                                    <p><strong>@tanggal($maintenance->created_at):</strong> {{ $maintenance->description
                                        }}</p>
                                    <p class="text-xs text-gray-500">Teknisi: {{ $maintenance->technician->name ?? 'N/A'
                                        }}</p>
                                </li>
                                @empty
                                <li class="text-gray-500 text-sm">Tidak ada riwayat perbaikan.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Riwayat Pemakaian/Tugas</h4>
                            <ul class="space-y-3 max-h-60 overflow-y-auto">
                                @forelse ($data['asset']->tasks as $task)
                                <li class="border dark:border-gray-700 p-3 rounded-md text-sm">
                                    <p><strong>@tanggal($task->created_at):</strong> {{ $task->title }}</p>
                                    {{-- PERBAIKAN DI SINI --}}
                                    <p class="text-xs text-gray-500">Staff: {{ $task->assignee->name ?? 'N/A' }}</p>
                                </li>
                                @empty
                                <li class="text-gray-500 text-sm">Aset ini belum pernah terkait tugas.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Riwayat Pergerakan Aset</h4>
                            <ul class="space-y-3 max-h-60 overflow-y-auto">
                                @forelse ($data['asset']->movements as $movement)
                                <li class="border dark:border-gray-700 p-3 rounded-md text-sm">
                                    <p><strong>@tanggal($movement->movement_time):</strong> Dari {{ $movement->fromRoom->name_room ?? 'N/A' }} ke {{ $movement->toRoom->name_room ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Oleh: {{ $movement->movedBy->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Keterangan: {{ $movement->description ?? 'N/A' }}</p>
                                </li>
                                @empty
                                <li class="text-gray-500 text-sm">Tidak ada riwayat pergerakan aset.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 flex justify-end space-x-3">
                        <a href="{{ route('master.assets.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Kembali</a>
                        <a href="{{ route('master.assets.edit', $data['asset']->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Edit
                            Aset</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>