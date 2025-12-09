<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ __('Form Barang Masuk / Aset Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="assetForm(@js($rooms))" x-cloak>
                    <form @submit.prevent="saveAssets()">
                        {{-- Kontainer untuk baris-baris form yang dinamis --}}
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto p-2 border dark:border-gray-700 rounded-lg">
                            <template x-for="(asset, index) in formData.assets" :key="index">
                                <div
                                    class="grid grid-cols-12 gap-x-4 gap-y-3 items-start bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg relative">
                                    {{-- Nama Aset --}}
                                    <div class="col-span-12 md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Nama
                                            Aset</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <i class="fas fa-tag text-gray-400"></i>
                                            </div>
                                            <input type="text" x-model="asset.name_asset"
                                                class="block w-full ps-10 rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Nama Aset/Barang" required>
                                        </div>
                                    </div>
                                    {{-- Jenis Aset --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Jenis</label>
                                        <select x-model="asset.asset_type"
                                            @change="asset.current_stock = (asset.asset_type === 'fixed_asset' ? 1 : asset.current_stock)"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="fixed_asset">Aset Tetap</option>
                                            <option value="consumable">Habis Pakai</option>
                                        </select>
                                    </div>
                                    {{-- Kategori --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Kategori</label>
                                        <select x-model="asset.asset_category_id"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Stok --}}
                                    <div class="col-span-4 md:col-span-1">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Stok</label>
                                        <input type="number" x-model.number="asset.current_stock" min="1"
                                            :disabled="asset.asset_type === 'fixed_asset'"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800"
                                            placeholder="Jml" required>
                                    </div>

                                    {{-- PERBAIKAN: Input Tanggal Beli --}}
                                    <div class="col-span-8 md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Tgl.
                                            Beli <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <input type="date" x-model="asset.purchase_date"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    {{-- Tombol Hapus Baris --}}
                                    <div class="col-span-12 md:col-span-2 flex items-center md:pt-5">
                                        <button type="button" @click="removeAssetRow(index)"
                                            x-show="formData.assets.length > 1"
                                            class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 dark:hover:bg-gray-600 transition"
                                            title="Hapus Baris">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- PERBAIKAN: Input Lokasi (Select2) --}}
                                    <div class="col-span-12 md:col-span-4">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Lokasi <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <select :id="'room_id_' + index" x-init="initSelect2($el, index)"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Gudang --</option>
                                            <template x-for="room in rooms" :key="room.id">
                                                <option :value="room.id"
                                                    x-text="`${room.floor.building.name_building} / ${room.floor.name_floor} / ${room.name_room}`">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- Kondisi (Hanya untuk Aset Tetap) --}}
                                    <div class="col-span-6 md:col-span-3" x-show="asset.asset_type === 'fixed_asset'">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Kondisi</label>
                                        <select x-model="asset.condition"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                    {{-- Stok Minimum (Hanya untuk Habis Pakai) --}}
                                    <div class="col-span-6 md:col-span-3" x-show="asset.asset_type === 'consumable'"
                                        style="display: none;">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Stok
                                            Min. <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <input type="number" x-model.number="asset.minimum_stock" min="0"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="e.g. 5">
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Tombol Tambah Baris --}}
                        <button type="button" @click="addAssetRow()"
                            class="mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold inline-flex items-center">
                            <i class="fas fa-plus me-2"></i>Tambah Baris Aset
                        </button>

                        {{-- Tombol Aksi Form --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('master.assets.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Semua Aset'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function assetForm(roomsData) { // PERBAIKAN: Menerima data rooms
            return {
                isSubmitting: false,
                rooms: roomsData || [], // PERBAIKAN: Menyimpan data rooms
                formData: {
                    assets: []
                },

                init() {
                    this.addAssetRow();
                },

                initSelect2(el, index) {
                    // Inisialisasi Select2
                    $(el).select2({
                        theme: "classic",
                        width: '100%'
                    }).on('change', (e) => {
                        // Update model Alpine saat Select2 berubah
                        this.formData.assets[index].room_id = e.target.value;
                    });
                },

                addAssetRow() {
                    this.formData.assets.push({
                        name_asset: '',
                        asset_type: 'fixed_asset',
                        asset_category_id: '', // <-- UBAH INI
                        condition: 'Baik',
                        current_stock: 1,
                        minimum_stock: 0,
                        room_id: '',
                        purchase_date: ''
                    });
                },

                removeAssetRow(index) {
                    // Hapus elemen Select2 sebelum menghapus baris
                    $(`#room_id_${index}`).select2('destroy');
                    this.formData.assets.splice(index, 1);
                },

                saveAssets() {
                    this.isSubmitting = true;

                    axios.post('{{ route("api.assets.store") }}', this.formData)
                    .then(response => {
                        sessionStorage.setItem('toastMessage', response.data.message || 'Aset berhasil ditambahkan!');
                        window.location.href = "{{ route('master.assets.index') }}";
                    })
                    .catch(error => {
                        let msg = 'Gagal menyimpan. Periksa kembali semua isian Anda.';
                        if (error.response && error.response.status === 422 && error.response.data.errors) {
                            const errorMessages = Object.values(error.response.data.errors).flat();
                            msg = errorMessages.join('<br>');
                        } else if (error.response && error.response.data.message) {
                            msg = error.response.data.message;
                        }

                        window.iziToast.error({
                            title: 'Gagal!',
                            message: msg,
                            position: 'topRight',
                            timeout: 5000
                        });
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>