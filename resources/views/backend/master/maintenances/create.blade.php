<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lapor Kerusakan Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="maintenanceForm()">
                    <form @submit.prevent="save()">
                        <div class="space-y-6">
                            <div>
                                <label for="asset_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aset yang
                                    Rusak</label>
                                <select id="asset_id" class="mt-1 block w-full" required>
                                    <option value="">-- Pilih Aset Tetap --</option>
                                    @foreach ($data['assets'] as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name_asset }} (S/N: {{
                                        $asset->serial_number ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                    Kerusakan</label>
                                <div class="relative mt-1">
                                    <div
                                        class="absolute inset-y-0 start-0 pt-3 flex items-start ps-3 pointer-events-none">
                                        <i class="fas fa-comment-dots text-gray-400"></i></div>
                                    <textarea x-model="formData.description" id="description" rows="4"
                                        class="block w-full ps-10 border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Jelaskan kerusakan secara detail..." required></textarea>
                                </div>
                                {{-- ================== TAMBAHKAN BARIS INI ================== --}}
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jelaskan kerusakan secara
                                    detail (minimal 10 karakter).</p>
                                {{-- ======================================================= --}}
                            </div>
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat
                                    Prioritas</label>
                                <select id="priority" class="mt-1 block w-full" required>
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                    <option value="critical">Kritis</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.maintenances.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Buat Laporan & Tugas</span>
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
        function maintenanceForm() {
                return {
                    isSubmitting: false,
                    formData: { asset_id: '', description: '', priority: 'high', maintenance_type: 'repair' },
                    init() {
                        $('#asset_id, #priority').select2({ theme: "classic", width: '100%' });
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async save() {
                        this.isSubmitting = true;
                        this.formData.asset_id = $('#asset_id').val();
                        this.formData.priority = $('#priority').val();

                        await fetch('/sanctum/csrf-cookie');
                        fetch('/api/maintenances', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: JSON.stringify(this.formData)
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            sessionStorage.setItem('toastMessage', 'Laporan kerusakan berhasil dibuat!');
                            window.location.href = "{{ route('master.maintenances.index') }}";
                        })
                        .catch(err => {
                            let msg = 'Gagal menyimpan. Pastikan semua field terisi.';
                            // Kode ini sudah otomatis menampilkan error validasi dari server
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('<br>');
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