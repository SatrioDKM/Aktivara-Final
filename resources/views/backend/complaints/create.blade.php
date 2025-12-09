<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ __('Catat Laporan / Keluhan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="complaintForm()" x-cloak>
                    <form @submit.prevent="save()">
                        <div class="space-y-6">

                            {{-- Judul Laporan --}}
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Laporan
                                    <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-heading text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.title" id="title"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: AC di Ruang Rapat Tidak Dingin" required>
                                </div>
                                <template x-if="errors.title">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.title[0]"></p>
                                </template>
                            </div>

                            {{-- Nama Pelapor & Deskripsi Lokasi --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="reporter_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pelapor
                                        <span class="text-red-500">*</span></label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" x-model="formData.reporter_name" id="reporter_name"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Contoh: John Doe (Tamu)" required>
                                    </div>
                                    <template x-if="errors.reporter_name">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.reporter_name[0]"></p>
                                    </template>
                                </div>
                                <div>
                                    <label for="location_text"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                        Lokasi <span class="text-red-500">*</span></label>
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                        <input type="text" x-model="formData.location_text" id="location_text"
                                            class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Contoh: Toilet Pria dekat Lobi Utama" required>
                                    </div>
                                    <template x-if="errors.location_text">
                                        <p class="mt-1 text-xs text-red-500" x-text="errors.location_text[0]"></p>
                                    </template>
                                </div>
                            </div>

                            {{-- Deskripsi Lengkap --}}
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Lengkap
                                    Laporan <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute top-3 left-0 ps-3 flex items-center pointer-events-none">
                                        <i class="fas fa-align-left text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.description" id="description" rows="5"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Jelaskan laporan atau keluhan secara detail (minimal 10 karakter)."
                                        required></textarea>
                                </div>
                                <template x-if="errors.description">
                                    <p class="mt-1 text-xs text-red-500" x-text="errors.description[0]"></p>
                                </template>
                            </div>

                            {{-- Ruangan & Aset Terkait --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Informasi Tambahan (Opsional)
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div wire:ignore>
                                        <label for="room_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan
                                            Terkait</label>
                                        <select id="room_id" class="mt-1 block w-full">
                                            <option value="">-- Pilih Ruangan --</option>
                                            @foreach($data['rooms'] as $room)
                                            <option value="{{ $room->id }}">{{ $room->floor->building->name_building }}
                                                / {{ $room->floor->name_floor }} / {{ $room->name_room }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div wire:ignore>
                                        <label for="asset_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset
                                            Terkait <span class="text-gray-400 text-xs font-normal italic ml-1">(Opsional)</span></label>
                                        <select id="asset_id" class="mt-1 block w-full">
                                            <option value="">-- Pilih Aset --</option>
                                            @foreach($data['assets'] as $asset)
                                            <option value="{{ $asset->id }}">{{ $asset->name_asset }} ({{
                                                $asset->serial_number ?? 'Non-Serial' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('complaints.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                    style="display: none;"></i>
                                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Laporan'"></span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function complaintForm() {
            return {
                isSubmitting: false,
                formData: {
                    title: '',
                    description: '',
                    reporter_name: '',
                    location_text: '',
                    room_id: '',
                    asset_id: ''
                },
                errors: {},

                init() {
                    const self = this; // Simpan konteks 'this' dari Alpine

                    // Inisialisasi Select2
                    $('#room_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Ruangan Terkait --'
                    }).on('change', function () {
                        // Update formData Alpine saat nilai Select2 berubah
                        self.formData.room_id = $(this).val();
                    });

                    $('#asset_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Aset Terkait --'
                    }).on('change', function () {
                         // Update formData Alpine saat nilai Select2 berubah
                        self.formData.asset_id = $(this).val();
                    });
                },

                save() {
                    this.isSubmitting = true;
                    this.errors = {}; // Bersihkan error sebelumnya

                    axios.post("{{ route('api.complaints.store') }}", this.formData)
                    .then(response => {
                        // Simpan pesan sukses di sessionStorage untuk ditampilkan setelah redirect
                        sessionStorage.setItem('toastMessage', 'Laporan baru berhasil dicatat!');
                        // Arahkan ke halaman index
                        window.location.href = "{{ route('complaints.index') }}";
                    })
                    .catch(error => {
                        let errorMessage = 'Gagal menyimpan. Silakan periksa kembali isian Anda.';
                        if (error.response && error.response.status === 422) {
                            // Tangani error validasi dari Laravel
                            this.errors = error.response.data.errors;
                            errorMessage = 'Terdapat kesalahan pada input Anda.';
                        } else if (error.response && error.response.data.message) {
                             errorMessage = error.response.data.message;
                        }

                        window.iziToast.error({
                            title: 'Gagal!',
                            message: errorMessage,
                            position: 'topRight'
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