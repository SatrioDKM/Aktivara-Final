<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="taskDetail({{ $task->id }}, {{ Js::from(Auth::user()) }}, {{ Js::from($task) }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Notifikasi Global -->
            <div x-show="notification.show" x-transition class="fixed top-20 right-5 z-50">
                <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                    :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                    <span x-text="notification.message"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Detail Tugas & Riwayat Laporan -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Alert Alasan Penolakan (Tetap di atas untuk visibilitas) -->
                    <template x-if="task.status === 'rejected' && task.rejection_notes">
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                            <p class="font-bold">Tugas Ditolak, Perlu Revisi</p>
                            <p class="mt-1"><strong>Alasan:</strong> <span x-text="task.rejection_notes"></span></p>
                        </div>
                    </template>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800" x-text="task.title"></h3>
                                <p class="text-sm text-gray-500">Dibuat oleh <strong
                                        x-text="task.creator.name"></strong> pada <span
                                        x-text="new Date(task.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                                </p>
                            </div>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="{
                                    'bg-gray-100 text-gray-800': task.status === 'unassigned',
                                    'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                    'bg-yellow-100 text-yellow-800': task.status === 'pending_review',
                                    'bg-green-100 text-green-800': task.status === 'completed',
                                    'bg-red-100 text-red-800': task.status === 'rejected',
                                  }" x-text="task.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())">
                            </span>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <p class="text-gray-700" x-text="task.description || 'Tidak ada deskripsi.'"></p>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Riwayat Laporan</h3>
                        <div class="space-y-4">
                            <template x-if="task.daily_reports.length === 0">
                                <p class="text-gray-500">Belum ada laporan untuk tugas ini.</p>
                            </template>
                            <template x-for="(report, index) in task.daily_reports" :key="report.id">
                                <div class="border rounded-lg p-4">
                                    <p class="font-semibold text-gray-800" x-text="report.title"></p>
                                    <p class="text-sm text-gray-600">Dilaporkan oleh <strong
                                            x-text="report.user.name"></strong> pada <span
                                            x-text="new Date(report.created_at).toLocaleString('id-ID')"></span></p>
                                    <p class="mt-2 text-gray-700" x-text="report.description"></p>
                                    <div class="mt-2" x-show="report.attachments.length > 0">
                                        <p class="text-sm font-semibold">Lampiran:</p>
                                        <template x-for="attachment in report.attachments" :key="attachment.id">
                                            <a :href="`/storage/${attachment.file_path}`" target="_blank"
                                                class="text-indigo-600 hover:underline text-sm block"
                                                x-text="attachment.file_path.split('/').pop()"></a>
                                        </template>
                                    </div>

                                    <!-- PENAMBAHAN: Indikator Hasil Review pada Laporan Terakhir -->
                                    <template
                                        x-if="index === task.daily_reports.length - 1 && ['completed', 'rejected'].includes(task.status)">
                                        <div class="mt-3 pt-3 border-t border-dashed">
                                            <p class="text-xs font-semibold text-gray-500">HASIL REVIEW:</p>
                                            <p class="text-sm font-medium"
                                                :class="task.status === 'completed' ? 'text-green-600' : 'text-red-600'"
                                                x-text="task.status === 'completed' ? 'Laporan ini disetujui.' : 'Laporan ini ditolak.'">
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Info Tambahan & Form Aksi -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Informasi Tambahan</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jenis Tugas:</span>
                                <span class="font-semibold text-gray-800" x-text="task.task_type.name_task"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Departemen:</span>
                                <span class="font-semibold text-gray-800" x-text="task.task_type.departemen"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Prioritas:</span>
                                <span class="font-semibold text-gray-800" x-text="task.task_type.priority_level"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Lokasi:</span>
                                <span class="font-semibold text-gray-800 text-right"
                                    x-text="task.room && task.room.floor && task.room.floor.building ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor} / ${task.room.name_room}` : 'Tidak spesifik'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Aset Terkait:</span>
                                <span class="font-semibold text-gray-800"
                                    x-text="task.asset ? task.asset.name_asset : '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dikerjakan oleh:</span>
                                <span class="font-semibold text-gray-800"
                                    x-text="task.staff ? task.staff.name : 'Belum Diambil'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- FORM UNTUK STAFF (KIRIM LAPORAN) -->
                    <template
                        x-if="(task.status === 'in_progress' || task.status === 'rejected') && currentUser.id === task.user_id">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold border-b pb-2 mb-4">Buat Laporan Baru</h3>
                            <form @submit.prevent="submitReport" x-ref="reportForm">
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="title" value="Judul Laporan" />
                                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                            required />
                                    </div>
                                    <div>
                                        <x-input-label for="description" value="Deskripsi Laporan" />
                                        <textarea id="description" name="description" rows="5"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                            required></textarea>
                                    </div>
                                    <div>
                                        <x-input-label for="attachments"
                                            value="Lampiran (Opsional, bisa lebih dari satu)" />
                                        <input type="file" name="attachments[]" id="attachments" multiple
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1" />
                                    </div>
                                    <x-primary-button class="w-full justify-center" ::disabled="isSubmitting">
                                        <span x-show="!isSubmitting">Kirim Laporan</span>
                                        <span x-show="isSubmitting">Mengirim...</span>
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </template>

                    <!-- FORM UNTUK LEADER (REVIEW) -->
                    <template x-if="task.status === 'pending_review' && currentUser.id === task.created_by">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold border-b pb-2 mb-4">Review Tugas</h3>
                            <div class="space-y-2">
                                <button @click="submitApproval()"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">Setujui</button>
                                <button @click="openRejectionModal()"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">Tolak
                                    (Revisi)</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal untuk Alasan Penolakan -->
            <div x-show="showRejectionModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen">
                    <div @click="showRejectionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                        <h3 class="text-lg font-medium text-gray-900">Alasan Penolakan</h3>
                        <p class="mt-1 text-sm text-gray-600">Tulis alasan mengapa tugas ini ditolak. Pesan ini akan
                            dikirimkan ke staff.</p>
                        <div class="mt-4">
                            <textarea x-model="rejectionNotes" rows="4"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Contoh: Lampiran foto kurang jelas, mohon ulangi."></textarea>
                        </div>
                        <div class="mt-4 flex justify-end space-x-3">
                            <x-secondary-button @click="showRejectionModal = false">Batal</x-secondary-button>
                            <x-danger-button @click="submitRejection()">Kirim Penolakan</x-danger-button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function taskDetail(taskId, currentUser, initialTask) {
            return {
                taskId: taskId,
                currentUser: currentUser,
                task: initialTask,
                isLoading: false,
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },
                showRejectionModal: false,
                rejectionNotes: '',

                async init() {
                    await fetch('/sanctum/csrf-cookie');
                    console.log('Task detail loaded for task ID:', this.taskId);
                },

                getTaskDetails() {
                    this.isLoading = true;
                    fetch(`/api/tasks/${this.taskId}`, { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            this.task = data;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.showNotification('Gagal memuat detail tugas.', 'error');
                            this.isLoading = false;
                        });
                },

                async submitReport() {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    const formData = new FormData(this.$refs.reportForm);
                    fetch(`/api/tasks/${this.taskId}/report`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                        body: formData
                    })
                    .then(async res => { if (!res.ok) { const err = await res.json().catch(() => ({})); throw err; } return res.json(); })
                    .then(data => {
                        this.showNotification('Laporan berhasil dikirim!', 'success');
                        this.getTaskDetails();
                    })
                    .catch(err => {
                        let msg = 'Gagal mengirim laporan.';
                        if (err.errors) msg = Object.values(err.errors).flat().join(' ');
                        else if (err.message) msg = err.message;
                        this.showNotification(msg, 'error');
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                        this.$refs.reportForm.reset();
                    });
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
                        alert('Alasan penolakan tidak boleh kosong.');
                        return;
                    }
                    this.submitReview('rejected', this.rejectionNotes);
                    this.showRejectionModal = false;
                },

                async submitReview(decision, notes = null) {
                    await fetch('/sanctum/csrf-cookie');
                    fetch(`/api/tasks/${this.taskId}/review`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify({
                            decision: decision,
                            rejection_notes: notes
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.showNotification('Review berhasil dikirim.', 'success');
                        this.getTaskDetails();
                    })
                    .catch(err => this.showNotification('Gagal mengirim review.', 'error'));
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) return decodeURIComponent(csrfCookie.split('=')[1]);
                    return '';
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                }
            }
        }
    </script>
</x-app-layout>