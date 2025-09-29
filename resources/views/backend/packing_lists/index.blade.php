<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Barang Keluar & Packing List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="packingListPage()">

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Buat Packing List Baru</h3>
                    <form @submit.prevent="save()">
                        <div class="space-y-4">
                            <div>
                                <label for="recipient_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Penerima</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user-tag text-gray-400"></i></div>
                                    <input type="text" x-model="formData.recipient_name" id="recipient_name" required
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700"
                                        placeholder="e.g. Departemen Teknisi">
                                </div>
                            </div>
                            <div>
                                <label for="asset_ids"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                                    Aset/Barang</label>
                                <select id="asset_ids" multiple required class="mt-1 block w-full"></select>
                            </div>
                            <div>
                                <label for="notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                    (Opsional)</label>
                                <textarea x-model="formData.notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                    placeholder="Tujuan atau keterangan tambahan..."></textarea>
                            </div>
                            <div>
                                <x-primary-button type="submit" class="w-full justify-center" ::disabled="isSubmitting">
                                    <span x-show="!isSubmitting">Buat & Simpan</span>
                                    <span x-show="isSubmitting">Menyimpan...</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Riwayat Packing List</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                        Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                        Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="isLoading">
                                    <tr>
                                        <td colspan="4" class="py-10 text-center"><i class="fas fa-spinner fa-spin"></i>
                                            Memuat...</td>
                                    </tr>
                                </template>
                                <template x-if="!isLoading && lists.length === 0">
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-gray-500">Belum ada riwayat.</td>
                                    </tr>
                                </template>
                                <template x-for="list in lists" :key="list.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 text-sm font-medium" x-text="list.document_number"></td>
                                        <td class="px-4 py-3 text-sm" x-text="list.recipient_name"></td>
                                        <td class="px-4 py-3 text-sm" x-text="list.assets_count"></td>
                                        <td class="px-4 py-3 text-center">
                                            <a :href="`/packing-lists/${list.id}/pdf`" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold inline-flex items-center">
                                                <i class="fas fa-print me-1"></i> Cetak
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{-- Paginasi akan di-handle oleh Alpine.js --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function packingListPage() {
                return {
                    isLoading: true,
                    isSubmitting: false,
                    lists: [],
                    pagination: {},
                    formData: { recipient_name: '', notes: '', asset_ids: [] },
                    init() {
                        this.fetchLists();

                        $('#asset_ids').select2({
                            theme: "classic",
                            width: '100%',
                            placeholder: 'Cari nama atau S/N aset...',
                            ajax: {
                                url: "{{ route('api.packing_lists.get_assets') }}",
                                dataType: 'json',
                                delay: 250,
                                processResults: function (data) {
                                    return { results: data.results };
                                },
                                cache: true
                            }
                        });
                    },
                    fetchLists(page = 1) {
                        this.isLoading = true;
                        fetch(`/api/packing-lists?page=${page}`, { headers: {'Accept': 'application/json'} })
                        .then(res => res.json())
                        .then(data => {
                            this.lists = data.data;
                            this.pagination = data;
                            this.isLoading = false;
                        });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.asset_ids = $('#asset_ids').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch("{{ route('api.packing_lists.store') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            iziToast.success({ title: 'Berhasil!', message: data.message, position: 'topRight' });
                            this.fetchLists();
                            this.formData = { recipient_name: '', notes: '', asset_ids: [] };
                            $('#asset_ids').val(null).trigger('change');
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa isian Anda.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                        })
                        .finally(() => this.isSubmitting = false);
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>