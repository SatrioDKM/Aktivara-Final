<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Aset: ') .
            $data['asset']->name_asset }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="assetForm()" x-init="initData(@js($data['asset']))">
                    <form @submit.prevent="save()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="name_asset" class="block text-sm font-medium">Nama Aset</label>
                                    <input type="text" x-model="formData.name_asset" class="mt-1 w-full rounded-md"
                                        placeholder="Nama aset atau barang" required>
                                </div>
                                <div>
                                    <label for="category" class="block text-sm font-medium">Kategori</label>
                                    <input type="text" x-model="formData.category" class="mt-1 w-full rounded-md"
                                        placeholder="e.g. Elektronik" required>
                                </div>
                                <div>
                                    <label for="room_id" class="block text-sm font-medium">Lokasi</label>
                                    <select id="room_id" class="mt-1 block w-full">
                                        <option value="">-- Gudang --</option>
                                        @foreach($data['rooms'] as $room)
                                        <option value="{{ $room->id }}">{{ $room->floor->building->name_building }} / {{
                                            $room->floor->name_floor }} / {{ $room->name_room }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="purchase_date" class="block text-sm font-medium">Tanggal
                                        Pembelian</label>
                                    <input type="date" x-model="formData.purchase_date" class="mt-1 w-full rounded-md">
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="space-y-6">
                                <div x-show="formData.asset_type === 'fixed_asset'">
                                    <label class="block text-sm font-medium">Nomor Seri</label>
                                    <input type="text" x-model="formData.serial_number"
                                        class="mt-1 w-full rounded-md bg-gray-100" placeholder="Dibuat otomatis"
                                        disabled>
                                </div>
                                <div x-show="formData.asset_type === 'fixed_asset'">
                                    <label for="condition" class="block text-sm font-medium">Kondisi</label>
                                    <select id="condition" x-model="formData.condition" class="mt-1 block w-full">
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium">Stok</label>
                                        <input type="number" x-model.number="formData.current_stock" min="0"
                                            class="mt-1 w-full rounded-md" required
                                            :disabled="formData.asset_type === 'fixed_asset'">
                                    </div>
                                    <div x-show="formData.asset_type === 'consumable'">
                                        <label class="block text-sm font-medium">Stok Min.</label>
                                        <input type="number" x-model.number="formData.minimum_stock" min="0"
                                            class="mt-1 w-full rounded-md">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Status</label>
                                    <select id="status" x-model="formData.status" class="mt-1 block w-full">
                                        <option value="available">Tersedia</option>
                                        <option value="in_use">Digunakan</option>
                                        <option value="maintenance">Perbaikan</option>
                                        <option value="disposed">Dibuang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium">Deskripsi</label>
                                <textarea x-model="formData.description" rows="3"
                                    class="mt-1 w-full rounded-md"></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.assets.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
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
        function assetForm() {
                return {
                    isSubmitting: false,
                    formData: {},
                    initData(asset) {
                        this.formData = { ...asset, room_id: asset.room_id || '' };
                        this.$nextTick(() => {
                            $('#room_id').val(this.formData.room_id).trigger('change');
                            $('#condition').val(this.formData.condition).trigger('change');
                            $('#status').val(this.formData.status).trigger('change');
                            $('#room_id, #condition, #status').select2({ theme: "classic", width: '100%' });
                        });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.room_id = $('#room_id').val();
                        this.formData.condition = $('#condition').val();
                        this.formData.status = $('#status').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/assets/${this.formData.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Data aset berhasil diperbarui!');
                            window.location.href = "{{ route('master.assets.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa isian Anda.';
                            if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                            iziToast.error({ title: 'Gagal!', message: msg, position: 'topRight', timeout: 5000 });
                            this.isSubmitting = false;
                        });
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>