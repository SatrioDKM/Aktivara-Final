<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ekspor Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Pilih Data untuk Diekspor</h3>
                    <p class="text-sm text-gray-600 mb-6">Silakan pilih jenis data yang ingin Anda unduh dalam format
                        Excel (.xlsx).</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Opsi Ekspor Laporan Harian -->
                        <div class="border p-4 rounded-lg flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-md bg-teal-100 text-teal-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-base font-medium text-gray-900">Laporan Harian</h4>
                                <p class="mt-1 text-sm text-gray-500">Unduh semua riwayat laporan harian yang telah
                                    dikirim oleh staff.</p>
                                <div class="mt-4">
                                    <a href="{{ route('export.daily_reports') }}"
                                        class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500">
                                        Unduh Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Opsi Ekspor Aset -->
                        <div class="border p-4 rounded-lg flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-base font-medium text-gray-900">Data Aset</h4>
                                <p class="mt-1 text-sm text-gray-500">Unduh daftar lengkap semua aset yang terdaftar di
                                    dalam sistem.</p>
                                <div class="mt-4">
                                    <a href="{{ route('export.assets') }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                        Unduh Aset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>