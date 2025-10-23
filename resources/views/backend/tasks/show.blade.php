<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-tasks mr-2"></i>
                {{ __('Detail Tugas') }}
            </h2>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                @php
                $atasanRoles = ['SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'WH01'];
                $staffRoles = ['HK02', 'TK02', 'SC02', 'PK02', 'WH02'];
                @endphp

                @if(in_array(Auth::user()->role_id, $atasanRoles))
                <a href="{{ route('tasks.monitoring') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Monitoring
                </a>
                @elseif(in_array(Auth::user()->role_id, $staffRoles))
                <a href="{{ route('tasks.my_tasks') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Tugas Aktif
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="taskDetail({
                initialTaskData: {{ Js::from($data['task']) }},
                assets: {{ Js::from($data['assets']) }},
                currentUser: {{ Js::from(Auth::user()) }}
            })" x-cloak>

                {{-- Komponen Notifikasi Global --}}
                <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="fixed top-20 right-5 z-50 rounded-lg shadow-lg"
                    :class="{ 'bg-green-500 text-white': notification.type === 'success', 'bg-red-500 text-white': notification.type === 'error' }">
                    <div class="flex items-center p-4">
                        <i class="fas"
                            :class="{ 'fa-check-circle': notification.type === 'success', 'fa-times-circle': notification.type === 'error' }"></i>
                        <div class="ml-3">
                            <p class="font-bold" x-text="notification.type === 'success' ? 'Berhasil!' : 'Oops!'"></p>
                            <p class="text-sm" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>

                {{-- State Loading Awal --}}
                <template x-if="isLoading">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-10 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-circle-notch fa-spin text-3xl"></i>
                        <p class="mt-3">Memuat Detail Tugas...</p>
                    </div>
                </template>

                {{-- Tampilan Utama (muncul setelah loading selesai) --}}
                <template x-if="!isLoading">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <div class="lg:col-span-2 space-y-6">
                            {{-- Notifikasi Jika Tugas Ditolak --}}
                            <template x-if="task.status === 'rejected' && task.rejection_notes">
                                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded-md shadow-sm"
                                    role="alert">
                                    <div class="flex">
                                        <div class="py-1"><i class="fas fa-exclamation-triangle mr-3"></i></div>
                                        <div>
                                            <p class="font-bold">Tugas Ditolak, Perlu Revisi</p>
                                            <p class="mt-1 text-sm"><strong>Alasan:</strong> <span
                                                    x-text="task.rejection_notes"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Card Detail Tugas Utama --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                                x-text="task.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Dibuat oleh <strong class="text-gray-700 dark:text-gray-200"
                                                    x-text="task.creator ? task.creator.name : 'Sistem'"></strong>
                                                pada <span
                                                    x-text="task.created_at ? new Date(task.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Tanggal tidak valid'"></span>
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="statusColor(task.status)" x-text="statusText(task.status)"></span>
                                    </div>
                                    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                        <p class="text-gray-700 dark:text-gray-300 prose max-w-none"
                                            x-html="task.description || 'Tidak ada deskripsi.'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Laporan Pengerjaan --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 flex items-center">
                                        <i class="fas fa-file-alt mr-3 text-gray-400"></i> Laporan Pengerjaan
                                    </h3>
                                    <template x-if="task.status === 'pending_review' || task.status === 'completed'">
                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">Deskripsi
                                                    Laporan:</h4>
                                                <p class="text-gray-700 dark:text-gray-300 mt-1"
                                                    x-text="task.report_text"></p>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">Foto
                                                        Sebelum:</h4>
                                                    <a :href="task.image_before ? `/storage/${task.image_before}` : '#'"
                                                        target="_blank" :class="{'cursor-pointer': task.image_before}">
                                                        <img :src="task.image_before ? `/storage/${task.image_before}` : `{{ asset('assets/backend/img/image-default.png') }}`"
                                                            alt="Foto Sebelum"
                                                            class="mt-2 rounded-lg w-full h-48 object-cover hover:opacity-80 transition shadow-md">
                                                    </a>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">Foto
                                                        Sesudah:</h4>
                                                    <a :href="task.image_after ? `/storage/${task.image_after}` : '#'"
                                                        target="_blank" :class="{'cursor-pointer': task.image_after}">
                                                        <img :src="task.image_after ? `/storage/${task.image_after}` : `{{ asset('assets/backend/img/image-default.png') }}`"
                                                            alt="Foto Sesudah"
                                                            class="mt-2 rounded-lg w-full h-48 object-cover hover:opacity-80 transition shadow-md">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="task.status !== 'pending_review' && task.status !== 'completed'">
                                        <div class="text-center py-8">
                                            <i class="fas fa-info-circle text-4xl text-gray-400"></i>
                                            <p class="mt-4 text-gray-500 dark:text-gray-400">Belum ada laporan yang
                                                dikirim untuk tugas ini.</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-6">

                            {{-- Card Informasi Tambahan --}}
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 flex items-center">
                                        <i class="fas fa-info-circle mr-3 text-gray-400"></i> Informasi
                                    </h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between"><span
                                                class="text-gray-500 dark:text-gray-400">Jenis Tugas:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200"
                                                x-text="task.task_type ? task.task_type.name_task : 'N/A'"></span></div>
                                        <div class="flex justify-between"><span
                                                class="text-gray-500 dark:text-gray-400">Departemen:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200"
                                                x-text="task.task_type ? task.task_type.departemen : 'N/A'"></span>
                                        </div>
                                        <div class="flex justify-between"><span
                                                class="text-gray-500 dark:text-gray-400">Prioritas:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200 capitalize"
                                                x-text="task.priority || 'Low'"></span></div>
                                        <div class="flex justify-between items-start"><span
                                                class="text-gray-500 dark:text-gray-400 flex-shrink-0 mr-2">Lokasi:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200 text-right"
                                                x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                                        </div>
                                        <div class="flex justify-between"><span
                                                class="text-gray-500 dark:text-gray-400">Aset Terkait:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200"
                                                x-text="task.asset ? task.asset.name_asset : '-'"></span></div>
                                        <div class="flex justify-between"><span
                                                class="text-gray-500 dark:text-gray-400">Dikerjakan oleh:</span><span
                                                class="font-semibold text-gray-800 dark:text-gray-200"
                                                x-text="task.assignee ? task.assignee.name : 'Belum Diambil'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Laporan (Hanya untuk Staff yang ditugaskan) --}}
                            <template
                                x-if="(task.status === 'in_progress' || task.status === 'rejected') && currentUser.id === task.user_id">
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                                    <form @submit.prevent="submitReport" class="p-6">
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 flex items-center">
                                            <i class="fas fa-paper-plane mr-3 text-gray-400"></i> Submit Laporan
                                        </h3>
                                        <div class="space-y-4">
                                            <div>
                                                <label for="report_text"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                                    Laporan <span class="text-red-500">*</span></label>
                                                <textarea x-model="formData.report_text" id="report_text" rows="4"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
                                                    placeholder="Jelaskan pekerjaan yang telah dilakukan..."
                                                    required></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto
                                                    Sebelum <span class="text-red-500">*</span></label>
                                                <input x-ref="fileInputBefore" type="file"
                                                    @change="previewImage($event, 'before')" accept="image/*"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    required>
                                                <template x-if="imageBeforePreview"><img :src="imageBeforePreview"
                                                        class="mt-2 rounded-md h-32 w-auto object-cover border dark:border-gray-700"></template>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto
                                                    Sesudah <span class="text-red-500">*</span></label>
                                                <input x-ref="fileInputAfter" type="file"
                                                    @change="previewImage($event, 'after')" accept="image/*"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    required>
                                                <template x-if="imageAfterPreview"><img :src="imageAfterPreview"
                                                        class="mt-2 rounded-md h-32 w-auto object-cover border dark:border-gray-700"></template>
                                            </div>
                                            <button type="submit" :disabled="isSubmitting"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 disabled:opacity-50">
                                                <i class="fas fa-circle-notch fa-spin mr-2" x-show="isSubmitting"
                                                    style="display: none;"></i>
                                                <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Laporan'"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>

                            {{-- Tombol Aksi Review (Hanya untuk Leader/pembuat tugas) --}}
                            <template x-if="task.status === 'pending_review' && currentUser.id === task.created_by">
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                                    <div class="p-6">
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 flex items-center">
                                            <i class="fas fa-check-double mr-3 text-gray-400"></i> Aksi Review
                                        </h3>
                                        <div class="space-y-2">
                                            <button @click="submitApproval()" :disabled="isSubmitting"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700 disabled:opacity-50">Setujui
                                                & Selesaikan</button>
                                            <button @click="openRejectionModal()"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-red-700">Tolak
                                                (Revisi)</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Modal untuk Alasan Penolakan --}}
                <div x-show="showRejectionModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
                    style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div @click="showRejectionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full"
                            @click.away="showRejectionModal = false">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Alasan Penolakan</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tulis alasan mengapa tugas ini
                                    ditolak. Pesan ini akan dikirimkan ke staff.</p>
                                <div class="mt-4">
                                    <textarea x-model="rejectionNotes" rows="4"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Contoh: Lampiran foto kurang jelas, mohon ulangi."
                                        required></textarea>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 flex justify-end space-x-3">
                                <x-secondary-button @click="showRejectionModal = false">Batal</x-secondary-button>
                                <x-danger-button @click="submitRejection()">Kirim Penolakan</x-danger-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('taskDetail', (data) => ({
                task: {},
                initialTaskData: data.initialTaskData,
                assets: data.assets,
                currentUser: data.currentUser,
                isLoading: true,
                isSubmitting: false,
                formData: { report_text: '', image_before: null, image_after: null },
                imageBeforePreview: null,
                imageAfterPreview: null,
                showRejectionModal: false,
                rejectionNotes: '',
                notification: { show: false, message: '', type: 'success' },

                init() {
                    console.log("[DEBUG] Alpine init started. Initial data from server:", this.initialTaskData);
                    if (this.initialTaskData && this.initialTaskData.id) {
                        this.task = this.initialTaskData;
                        this.isLoading = false;
                        console.log("[DEBUG] Task data loaded from server.", this.task);
                    } else {
                        console.warn("[DEBUG] Initial data is invalid. Fetching from API as a fallback.");
                        const taskId = window.location.pathname.split('/').pop();
                        this.getTaskDetails(taskId);
                    }
                },

                getTaskDetails(taskId) {
                    this.isLoading = true;
                    const idToFetch = taskId || this.task.id;
                    console.log(`[DEBUG] Fetching data for task ID: ${idToFetch}...`);
                    axios.get(`/api/tasks/${idToFetch}`)
                        .then(response => {
                            console.log('[DEBUG] API Response Success:', response.data);
                            this.task = response.data;
                        })
                        .catch(error => {
                            console.error('[DEBUG] API Response Error:', error.response || error);
                            this.showNotification('Gagal memuat detail tugas. Coba refresh halaman.', 'error');
                        })
                        .finally(() => { this.isLoading = false; });
                },

                previewImage(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (type === 'before') this.imageBeforePreview = e.target.result;
                        else this.imageAfterPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    if (type === 'before') this.formData.image_before = file;
                    else this.formData.image_after = file;
                },

                submitReport() {
                    this.isSubmitting = true;
                    const fd = new FormData();
                    fd.append('report_text', this.formData.report_text);
                    if (this.formData.image_before) fd.append('image_before', this.formData.image_before);
                    if (this.formData.image_after) fd.append('image_after', this.formData.image_after);

                    axios.post(`/api/tasks/${this.task.id}/report`, fd)
                        .then(response => {
                            this.showNotification(response.data.message, 'success');
                            this.getTaskDetails();
                            this.formData = { report_text: '', image_before: null, image_after: null };
                            this.imageBeforePreview = null;
                            this.imageAfterPreview = null;

                            // === PERBAIKAN DI SINI: Gunakan x-ref ===
                            if (this.$refs.fileInputBefore) {
                                this.$refs.fileInputBefore.value = null;
                            }
                            if (this.$refs.fileInputAfter) {
                                this.$refs.fileInputAfter.value = null;
                            }
                        })
                        .catch(error => {
                            let msg = 'Terjadi kesalahan saat mengirim laporan.'; // Pesan error default
                            if (error.response?.status === 422) {
                                msg = Object.values(error.response.data.errors).flat().join('<br>');
                            } else if (error.response?.data?.message) {
                                msg = error.response.data.message;
                            }
                            this.showNotification(msg, 'error');
                        })
                        .finally(() => this.isSubmitting = false);
                },

                openRejectionModal() {
                    this.rejectionNotes = '';
                    this.showRejectionModal = true;
                },

                submitApproval() {
                    this.submitReview('completed');
                },

                submitRejection() {
                    if (!this.rejectionNotes.trim()) {
                        this.showNotification('Alasan penolakan tidak boleh kosong.', 'error');
                        return;
                    }
                    this.submitReview('rejected', this.rejectionNotes);
                    this.showRejectionModal = false;
                },

                submitReview(decision, notes = null) {
                    this.isSubmitting = true;
                    axios.post(`/api/tasks/${this.task.id}/review`, {
                        decision: decision,
                        rejection_notes: notes
                    })
                    .then(response => {
                        this.showNotification(response.data.message, 'success');
                        this.getTaskDetails();
                    })
                    .catch(error => {
                        let msg = 'Gagal mengirim review.';
                        if (error.response?.data?.message) {
                            msg = error.response.data.message;
                        }
                        this.showNotification(msg, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },

                showNotification(message, type) {
                    window.iziToast[type.toLowerCase()]({
                        title: type === 'success' ? 'Berhasil' : 'Error',
                        message: message,
                        position: 'topRight'
                    });
                },

                statusColor(status) {
                    const colors = {
                        'unassigned': 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
                        'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    };
                    return colors[status] || 'bg-gray-100';
                },

                statusText(status) {
                    if (!status) return 'Memuat...';
                    const texts = {
                        'unassigned': 'Belum Diambil',
                        'in_progress': 'Dikerjakan',
                        'pending_review': 'Review',
                        'completed': 'Selesai',
                        'rejected': 'Ditolak'
                    };
                    return texts[status] || status.replace(/_/g, ' ');
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>