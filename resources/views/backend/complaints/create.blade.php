<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catat Laporan / Keluhan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="complaintForm()">
                    <form @submit.prevent="save()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul
                                    Laporan</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-heading text-gray-400"></i></div>
                                    <input type="text" x-model="formData.title" id="title"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: AC di Ruang Rapat Tidak Dingin" required>
                                </div>
                            </div>
                            <div>
                                <label for="reporter_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                    Pelapor</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i></div>
                                    <input type="text" x-model="formData.reporter_name" id="reporter_name"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700"
                                        placeholder="Contoh: John Doe (Tamu)" required>
                                </div>
                            </div>
                            <div>
                                <label for="location_text"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                    Lokasi</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i></div>
                                    <input type="text" x-model="formData.location_text" id="location_text"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700"
                                        placeholder="Contoh: Toilet Pria dekat Lobi Utama" required>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Lengkap
                                    Laporan</label>
                                <textarea x-model="formData.description" id="description" rows="5"
                                    class="mt-1 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700"
                                    placeholder="Jelaskan laporan atau keluhan secara detail (minimal 10 karakter)."
                                    required></textarea>
                            </div>
                            <div>
                                <label for="room_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ruangan Terkait
                                    (Opsional)</label>
                                <select id="room_id" class="mt-1 block w-full">
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($data['rooms'] as $room)
                                    <option value="{{ $room->id }}">{{ $room->floor->building->name_building }} / {{
                                        $room->floor->name_floor }} / {{ $room->name_room }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="asset_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset Terkait
                                    (Opsional)</label>
                                <select id="asset_id" class="mt-1 block w-full">
                                    <option value="">-- Pilih Aset --</option>
                                    @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name_asset }} ({{ $asset->serial_number
                                        ?? 'Non-Serial' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('complaints.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Simpan Laporan</span>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function complaintForm() {
                return {
                    isSubmitting: false,
                    formData: { title: '', description: '', reporter_name: '', location_text: '', room_id: '', asset_id: '' },
                    init() {
                        $('#room_id, #asset_id').select2({ theme: "classic", width: '100%' });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.room_id = $('#room_id').val();
                        this.formData.asset_id = $('#asset_id').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch("{{ route('api.complaints.store') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Laporan baru berhasil dicatat!');
                            window.location.href = "{{ route('complaints.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Periksa kembali isian Anda.';
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