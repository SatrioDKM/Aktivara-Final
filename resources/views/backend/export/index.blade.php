<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-file-export mr-2"></i>
            {{ __('Ekspor Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pusat Unduhan Laporan</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Pilih jenis data yang ingin Anda unduh dalam format
                        Excel (.xlsx). File akan digenerate secara otomatis berdasarkan data terbaru.</p>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div
                            class="group border dark:border-gray-700 p-6 rounded-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                                <i class="fas fa-boxes fa-2x"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">Data Aset</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 h-16">Unduh daftar lengkap semua
                                aset (tetap & habis pakai) yang terdaftar di dalam sistem.</p>
                            <div class="mt-4">
                                <a href="{{ route('export.assets') }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800">
                                    <i class="fas fa-download mr-2"></i>
                                    Unduh Aset
                                </a>
                            </div>
                        </div>

                        <div
                            class="group border dark:border-gray-700 p-6 rounded-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                                <i class="fas fa-history fa-2x"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Tugas</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 h-16">Unduh semua riwayat pekerjaan
                                dari semua departemen, termasuk detail status, staff, dan tanggal.</p>
                            <div class="mt-4">
                                {{-- Arahkan ke rute export.task_history (akan kita buat di langkah 3) --}}
                                <a href="{{ route('export.task_history') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800">
                                    <i class="fas fa-download mr-2"></i>
                                    Unduh Riwayat
                                </a>
                            </div>
                        </div>

                        <div class="group border border-dashed dark:border-gray-700 p-6 rounded-lg opacity-60">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 mb-4">
                                <i class="fas fa-file-excel fa-2x"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">Laporan Lainnya</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 h-16">Fitur ekspor untuk data
                                lainnya akan tersedia di kemudian hari.</p>
                            <div class="mt-4">
                                <span
                                    class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                                    Segera Hadir
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>