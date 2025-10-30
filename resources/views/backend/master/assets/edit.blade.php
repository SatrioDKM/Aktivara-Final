<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Aset: ') .
            $data['asset']->name_asset }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="assetForm()" x-init="initData(@js($data['asset']))" x-cloak>
                    <form @submit.prevent="save()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="name_asset"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                        Aset</label>
                                    <input type="text" x-model="formData.name_asset"
                                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Nama aset atau barang" required>
                                </div>
                                <div>
                                    <label for="asset_category_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                    <select id="asset_category_id" class="mt-1 block w-full">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($data['categories'] as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="room_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                                    {{-- Select2 akan di-render di sini oleh Alpine --}}
                                    <select id="room_id" class="mt-1 block w-full">
                                        <option value="">-- Gudang --</option>
                                        @foreach($data['rooms'] as $room)
                                        <option value="{{ $room->id }}">{{ $room->floor->building->name_building }} / {{
                                            $room->floor->name_floor }} / {{ $room->name_room }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="purchase_date"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                        Pembelian</label>
                                    <input type="date" x-model="formData.purchase_date"
                                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="space-y-6">
                                <div x-show="formData.asset_type === 'fixed_asset'">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                        Seri</label>
                                    <input type="text" x-model="formData.serial_number"
                                        class="mt-1 w-full rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-700 dark:text-gray-400"
                                        placeholder="Dibuat otomatis" disabled>
                                </div>
                                <div x-show="formData.asset_type === 'fixed_asset'">
                                    <label for="condition"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi</label>
                                    <select id="condition" x-model="formData.condition" class="mt-1 block w-full">
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
                                        <input type="number" x-model.number="formData.current_stock" min="0"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                            required :disabled="formData.asset_type === 'fixed_asset'"
                                            :class="{'bg-gray-100 dark:bg-gray-700': formData.asset_type === 'fixed_asset'}">
                                    </div>
                                    <div x-show="formData.asset_type === 'consumable'" style="display: none;">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok
                                            Min.</label>
                                        <input type="number" x-model.number="formData.minimum_stock" min="0"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select id="status" x-model="formData.status" class="mt-1 block w-full">
                                        <option value="available">Tersedia</option>
                                        <option value="in_use">Digunakan</option>
                                        <option value="maintenance">Perbaikan</option>
                                        <option value="disposed">Dibuang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <textarea x-model="formData.description" rows="3"
                                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('master.assets.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <span x-show="!isSubmitting">Simpan Perubahan</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- PERBAIKAN: Hapus semua @push CDN (styles dan scripts) --}}
    {{-- Semua library (iziToast, Select2, jQuery) sudah di-load dari app.js --}}

    @push('scripts')
    {{-- Script ini aman karena bergantung pada library global (window.$/jQuery, window.iziToast) dari app.js --}}
    <script>
        function assetForm() {
                return {
                    isSubmitting: false,
                    formData: {},
                    initData(asset) {
                        // Ganti 'category' menjadi 'asset_category_id' saat menyimpan
                        this.formData = { ...asset, room_id: asset.room_id || '' };
                        // Pastikan 'category' yang lama tidak terkirim
                        delete this.formData.category; 
                        // Pastikan asset_category_id ada di formData
                        this.formData.asset_category_id = asset.asset_category_id || '';

                        this.$nextTick(() => {
                            // Inisialisasi Select2 menggunakan jQuery ($) dari app.js
                            $('#room_id').val(this.formData.room_id).trigger('change');
                            $('#condition').val(this.formData.condition).trigger('change');
                            $('#status').val(this.formData.status).trigger('change');
                            $('#asset_category_id').val(this.formData.asset_category_id).trigger('change'); // <-- TAMBAH INI

                            // Terapkan Select2
                            $('#room_id, #condition, #status, #asset_category_id').select2({ // <-- TAMBAH INI
                                theme: "classic",
                                width: '100%'
                            });

                            // Tambahkan listener untuk update model Alpine saat Select2 berubah
                            $('#room_id').on('change', (e) => this.formData.room_id = e.target.value);
                            $('#condition').on('change', (e) => this.formData.condition = e.target.value);
                            $('#status').on('change', (e) => this.formData.status = e.target.value);
                            $('#asset_category_id').on('change', (e) => this.formData.asset_category_id = e.target.value); // <-- TAMBAH INI
                        });
                    },

                    async save() {
                        this.isSubmitting = true;

                        // PERBAIKAN: Gunakan axios.post (bukan PUT)
                        // Ini untuk mencocokkan route `api.php` Anda: Route::post('/{id}', ...)
                        // Axios (dari app.js) akan menangani header CSRF secara otomatis
                        axios.post(`/api/assets/${this.formData.id}`, this.formData)
                        .then(response => {
                            // Gunakan sessionStorage untuk pesan sukses setelah redirect
                            sessionStorage.setItem('toastMessage', 'Data aset berhasil diperbarui!');
                            window.location.href = "{{ route('master.assets.index') }}";
                        })
                        .catch(error => {
                            let msg = 'Gagal menyimpan. Periksa isian Anda.';
                            if (error.response && error.response.status === 422 && error.response.data.errors) {
                                // Tangani error validasi
                                msg = Object.values(error.response.data.errors).flat().join('<br>');
                            } else if (error.response && error.response.data.message) {
                                // Tangani error server lainnya
                                msg = error.response.data.message;
                            }

                            // Gunakan iziToast dari app.js
                            window.iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                            this.isSubmitting = false;
                        });
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>