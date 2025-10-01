<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Tugas #') . $data['task']->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="taskShowPage()">
            {{-- Kolom Kiri: Detail Tugas --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['task']->title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Jenis: <span class="font-semibold">{{ $data['task']->taskType->name_task }}</span> |
                                Prioritas: <span class="font-semibold capitalize">{{ $data['task']->priority }}</span>
                            </p>
                        </div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full capitalize">
                            {{ str_replace('_', ' ', $data['task']->status) }}
                        </span>
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Dibuat oleh</dt>
                            <dd>{{ $data['task']->creator->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Dikerjakan oleh</dt>
                            <dd>{{ $data['task']->staff->name ?? 'Belum Ditugaskan' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Lokasi</dt>
                            <dd>{{ $data['task']->room ? $data['task']->room->floor->building->name_building . ' / ' .
                                $data['task']->room->floor->name_floor . ' / ' . $data['task']->room->name_room : 'Tidak
                                spesifik' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Aset Terkait</dt>
                            <dd>{{ $data['task']->asset->name_asset ?? 'Tidak ada' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="font-medium text-gray-500">Deskripsi</dt>
                            <dd>{{ $data['task']->description ?? 'Tidak ada deskripsi.' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Laporan Pengerjaan</h3>
                    @if($data['task']->status === 'pending_review' || $data['task']->status === 'completed')
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="md:col-span-2">
                            <dt class="font-medium text-gray-500">Catatan Laporan</dt>
                            <dd>{{ $data['task']->report_text }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 mb-2">Foto Sebelum</dt>
                            <img src="{{ $data['task']->image_before ? Storage::url($data['task']->image_before) : asset('assets/backend/img/image-default.png') }}"
                                alt="Foto Sebelum" class="rounded-lg w-full h-auto object-cover">
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 mb-2">Foto Sesudah</dt>
                            <img src="{{ $data['task']->image_after ? Storage::url($data['task']->image_after) : asset('assets/backend/img/image-default.png') }}"
                                alt="Foto Sesudah" class="rounded-lg w-full h-auto object-cover">
                        </div>
                    </dl>
                    @else
                    <p class="text-sm text-gray-500">Belum ada laporan yang dikirim untuk tugas ini.</p>
                    @endif
                </div>
            </div>

            {{-- Kolom Kanan: Form Laporan --}}
            <div class="lg:col-span-1">
                @if($data['task']->status === 'in_progress' && Auth::id() === $data['task']->user_id)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sticky top-24">
                    <h3 class="text-lg font-semibold mb-4">Kirim Laporan Pengerjaan</h3>
                    <form @submit.prevent="submitReport({{ $data['task']->id }})">
                        <div class="space-y-4">
                            <div>
                                <label for="report_text" class="block text-sm font-medium">Catatan Laporan</label>
                                <textarea x-model="formData.report_text" id="report_text" rows="5"
                                    class="mt-1 block w-full rounded-md"
                                    placeholder="Jelaskan apa yang sudah dikerjakan..." required></textarea>
                            </div>
                            <div>
                                <label for="asset_id" class="block text-sm font-medium">Aset yang Digunakan
                                    (Opsional)</label>
                                <select id="asset_id" class="mt-1 block w-full">
                                    <option value="">-- Pilih Aset --</option>
                                    @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name_asset }} ({{ $asset->serial_number
                                        ?? 'Non-Serial' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Foto Sebelum</label>
                                <input type="file" @change="handleFileChange($event, 'before')"
                                    class="mt-1 block w-full text-sm" required>
                                <img x-show="imageBeforePreview" :src="imageBeforePreview"
                                    class="mt-2 rounded-lg object-cover w-full h-auto">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Foto Sesudah</label>
                                <input type="file" @change="handleFileChange($event, 'after')"
                                    class="mt-1 block w-full text-sm" required>
                                <img x-show="imageAfterPreview" :src="imageAfterPreview"
                                    class="mt-2 rounded-lg object-cover w-full h-auto">
                            </div>
                            <x-primary-button type="submit" class="w-full justify-center" ::disabled="isSubmitting">
                                <span x-show="!isSubmitting">Kirim Laporan</span>
                                <span x-show="isSubmitting">Mengirim...</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                @endif
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
        function taskShowPage() {
                return {
                    imageBeforePreview: null,
                    imageAfterPreview: null,
                    imageBeforeFile: null,
                    imageAfterFile: null,
                    isSubmitting: false,
                    formData: { report_text: '', asset_id: '{{ $data['task']->asset_id ?? '' }}' },
                    init() {
                        $('#asset_id').select2({ theme: 'classic', width: '100%' });
                    },
                    handleFileChange(event, type) {
                        const file = event.target.files[0];
                        if (!file) return;

                        if (type === 'before') {
                            this.imageBeforeFile = file;
                            this.imageBeforePreview = URL.createObjectURL(file);
                        } else {
                            this.imageAfterFile = file;
                            this.imageAfterPreview = URL.createObjectURL(file);
                        }
                    },
                    getCsrfToken() {
                        const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                        return csrfCookie ? decodeURIComponent(csrfCookie.split('=')[1]) : '';
                    },
                    async submitReport(taskId) {
                        this.isSubmitting = true;

                        const formData = new FormData();
                        formData.append('report_text', this.formData.report_text);
                        formData.append('asset_id', $('#asset_id').val());
                        if (this.imageBeforeFile) formData.append('image_before', this.imageBeforeFile);
                        if (this.imageAfterFile) formData.append('image_after', this.imageAfterFile);

                        // Karena kita mengirim file (multipart/form-data), kita harus menggunakan 'PUT'/'POST' palsu
                        formData.append('_method', 'POST');

                        await fetch('/sanctum/csrf-cookie');
                        fetch(`/api/tasks/${taskId}/report`, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                            body: formData
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res.json()))
                        .then(data => {
                            iziToast.success({ title: 'Berhasil!', message: data.message, position: 'topRight' });
                            setTimeout(() => window.location.reload(), 1500);
                        })
                        .catch(err => {
                            let msg = 'Gagal mengirim laporan. Periksa kembali isian Anda.';
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