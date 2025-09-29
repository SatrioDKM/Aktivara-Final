<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Form Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="assetForm(@js($data['rooms']))">
                    <form @submit.prevent="saveAssets()">
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto p-2 border dark:border-gray-700 rounded-lg">
                            <template x-for="(asset, index) in formData.assets" :key="index">
                                <div
                                    class="grid grid-cols-12 gap-x-4 gap-y-2 items-start bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg relative">
                                    <div class="col-span-12 md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Nama
                                            Aset</label>
                                        <input type="text" x-model="asset.name_asset"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                            placeholder="Nama Aset/Barang" required>
                                    </div>
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Jenis</label>
                                        <select x-model="asset.asset_type"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                                            <option value="fixed_asset">Aset Tetap</option>
                                            <option value="consumable">Habis Pakai</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Kategori</label>
                                        <input type="text" x-model="asset.category"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                            placeholder="e.g. Elektronik" required>
                                    </div>
                                    <div class="col-span-6 md:col-span-1">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Stok</label>
                                        <input type="number" x-model.number="asset.current_stock" min="1"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                            placeholder="Jml" required>
                                    </div>
                                    <div class="col-span-6 md:col-span-2" x-show="asset.asset_type === 'fixed_asset'">
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">Kondisi</label>
                                        <select x-model="asset.condition"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                    <div class="col-span-6 md:col-span-2" x-show="asset.asset_type === 'consumable'">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Stok
                                            Min.</label>
                                        <input type="number" x-model.number="asset.minimum_stock" min="0"
                                            class="mt-1 w-full rounded-md text-sm border-gray-300 dark:bg-gray-900 dark:border-gray-700"
                                            placeholder="Stok Min.">
                                    </div>
                                    <div class="col-span-12 md:col-span-2 flex items-center md:pt-5">
                                        <button type="button" @click="removeAssetRow(index)"
                                            x-show="formData.assets.length > 1"
                                            class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition"
                                            title="Hapus Baris">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addAssetRow()"
                            class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center">
                            <i class="fas fa-plus me-1"></i>Tambah Baris
                        </button>

                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.assets.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Simpan Semua</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        function assetForm(rooms) {
                return {
                    isSubmitting: false,
                    formData: { assets: [] },
                    rooms: rooms,
                    init() {
                        this.addAssetRow();
                    },
                    addAssetRow() {
                        this.formData.assets.push({
                            name_asset: '',
                            asset_type: 'fixed_asset',
                            category: '',
                            condition: 'Baik',
                            current_stock: 1,
                            minimum_stock: 0
                        });
                    },
                    removeAssetRow(index) {
                        this.formData.assets.splice(index, 1);
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async saveAssets() {
                        this.isSubmitting = true;
                        await fetch('/sanctum/csrf-cookie');
                        fetch('/api/assets', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', data.message || 'Aset berhasil ditambahkan!');
                            window.location.href = "{{ route('master.assets.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa kembali isian Anda.';
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('<br>');
                            } else if (err.message) {
                                msg = err.message;
                            }
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                        })
                        .finally(() => this.isSubmitting = false);
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>