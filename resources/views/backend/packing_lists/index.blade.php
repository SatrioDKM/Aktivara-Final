<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-truck-loading mr-2"></i>
            {{ __('Barang Keluar & Packing List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="packingListPage()" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Kolom Kiri: Form Input --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg sm:rounded-lg sticky top-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Buat Packing List
                                Baru</h3>
                            <form @submit.prevent="save()">
                                <div class="space-y-4">
                                    {{-- Nama Penerima --}}
                                    <div>
                                        <label for="recipient_name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                            Penerima <span class="text-red-500">*</span></label>
                                        <div class="relative mt-1">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <i class="fas fa-user-tag text-gray-400"></i>
                                            </div>
                                            <input type="text" x-model="formData.recipient_name" id="recipient_name"
                                                required
                                                class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="e.g. Departemen Teknisi">
                                        </div>
                                    </div>

                                    {{-- Pilih Aset/Barang --}}
                                    <div wire:ignore>
                                        <label for="asset_ids"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                                            Aset/Barang <span class="text-red-500">*</span></label>
                                        <select id="asset_ids" multiple required class="mt-1 block w-full"></select>
                                    </div>

                                    {{-- Catatan --}}
                                    <div>
                                        <label for="notes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                            (Opsional)</label>
                                        <textarea x-model="formData.notes" id="notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Tujuan atau keterangan tambahan..."></textarea>
                                    </div>

                                    {{-- Tombol Aksi --}}
                                    <div>
                                        <x-primary-button type="submit" class="w-full justify-center"
                                            ::disabled="isSubmitting">
                                            <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                                style="display: none;"></i>
                                            <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Buat & Simpan'"></span>
                                        </x-primary-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Riwayat Packing List --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg sm:rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Riwayat Packing List
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                No. Dokumen</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Penerima</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Total Item</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-if="isLoading">
                                            <tr>
                                                <td colspan="4" class="py-10 text-center"><i
                                                        class="fas fa-spinner fa-spin text-gray-400"></i> Memuat...</td>
                                            </tr>
                                        </template>
                                        <template x-if="!isLoading && lists.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-10 text-center text-gray-500">Belum ada
                                                    riwayat.</td>
                                            </tr>
                                        </template>
                                        <template x-for="list in lists" :key="list.id">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200"
                                                    x-text="list.document_number"></td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"
                                                    x-text="list.recipient_name"></td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"
                                                    x-text="list.assets_count"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <a :href="`/packing-lists/${list.id}/pdf`" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-semibold inline-flex items-center">
                                                        <i class="fas fa-print me-1"></i> Cetak
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginasi --}}
                            <div class="mt-4 flex justify-between items-center"
                                x-show="!isLoading && pagination.total > 0">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Menampilkan <span x-text="pagination.from || 0"></span> - <span
                                        x-text="pagination.to || 0"></span> dari <span
                                        x-text="pagination.total || 0"></span>
                                </p>
                                <div class="flex space-x-2">
                                    <button @click="changePage(pagination.prev_page_url)"
                                        :disabled="!pagination.prev_page_url"
                                        class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50">Sebelumnya</button>
                                    <button @click="changePage(pagination.next_page_url)"
                                        :disabled="!pagination.next_page_url"
                                        class="px-3 py-1 text-sm rounded-md bg-gray-200 dark:bg-gray-700 disabled:opacity-50">Berikutnya</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                    axios.get(`/api/packing-lists?page=${page}`)
                    .then(response => {
                        this.lists = response.data.data;
                        this.pagination = response.data;
                    })
                    .catch(error => {
                        console.error('Gagal mengambil data packing list:', error);
                        window.iziToast.error({ title: 'Gagal', message: 'Tidak dapat mengambil riwayat data.', position: 'topRight' });
                    })
                    .finally(() => this.isLoading = false);
                },

                changePage(url) {
                    if (!url) return;
                    const pageNumber = new URL(url).searchParams.get('page');
                    this.fetchLists(pageNumber);
                },

                save() {
                    this.isSubmitting = true;
                    this.formData.asset_ids = $('#asset_ids').val();

                    axios.post("{{ route('api.packing_lists.store') }}", this.formData)
                    .then(response => {
                        window.iziToast.success({ title: 'Berhasil!', message: response.data.message, position: 'topRight' });
                        this.fetchLists(); // Muat ulang data riwayat
                        // Reset form
                        this.formData = { recipient_name: '', notes: '', asset_ids: [] };
                        $('#asset_ids').val(null).trigger('change');
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan. Periksa kembali isian Anda.';
                        if (error.response?.status === 422) {
                            msg = Object.values(error.response.data.errors).flat().join('<br>');
                        } else if (error.response?.data?.message) {
                            msg = error.response.data.message;
                        }
                        window.iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                    })
                    .finally(() => this.isSubmitting = false);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>