<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat & Laporan Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="historyPage(@js($data))">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                            {{-- ... Filter Lengkap: Department, Staff, Date, Status, Search ... --}}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            {{-- ... Tabel Riwayat Tugas Lengkap ... --}}
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        {{-- ... Paginasi ... --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function historyPage(data) {
                return {
                     // ... Logika Alpine.js lengkap untuk filter kompleks dan tabel (API: getTaskHistory)
                }
            }
    </script>
    @endpush
</x-app-layout>