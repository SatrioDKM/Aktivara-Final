<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="taskDetail({ taskId: {{ $task->id }}, taskData: {{ Js::from($task) }}, assets: {{ Js::from($assets) }}, currentUser: {{ Js::from(Auth::user()) }} })">

                <div x-show="notification.show" x-transition class="fixed top-20 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="md:col-span-2 space-y-6">
                        <div x-show="task.status === 'rejected' && task.rejection_notes"
                            class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                            <p class="font-bold">Tugas Ditolak, Perlu Revisi</p>
                            <p class="mt-1"><strong>Alasan:</strong> <span x-text="task.rejection_notes"></span></p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800" x-text="task.title"></h3>
                                    <p class="text-sm text-gray-500">Dibuat oleh <strong
                                            x-text="task.creator.name"></strong> pada <span
                                            x-text="new Date(task.created_at).toLocaleDateString('id-ID')"></span></p>
                                </div>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusColor(task.status)" x-text="statusText(task.status)"></span>
                            </div>
                            <div class="mt-4 border-t pt-4">
                                <p class="text-gray-700" x-text="task.description || 'Tidak ada deskripsi.'"></p>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold border-b pb-2 mb-4">Laporan Pengerjaan</h3>
                            <template x-if="task.report_text">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold">Deskripsi Laporan:</h4>
                                        <p class="text-gray-700" x-text="task.report_text"></p>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-semibold">Foto Sebelum:</h4>
                                            <template x-if="task.image_before">
                                                <a :href="`/storage/${task.image_before}`" target="_blank">
                                                    <img :src="`/storage/${task.image_before}`"
                                                        class="mt-2 rounded-lg w-full h-auto object-cover cursor-pointer hover:opacity-80 transition">
                                                </a>
                                            </template>
                                            <template x-if="!task.image_before">
                                                <p class="text-gray-500 text-sm">Tidak ada foto.</p>
                                            </template>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold">Foto Sesudah:</h4>
                                            <template x-if="task.image_after">
                                                <a :href="`/storage/${task.image_after}`" target="_blank">
                                                    <img :src="`/storage/${task.image_after}`"
                                                        class="mt-2 rounded-lg w-full h-auto object-cover cursor-pointer hover:opacity-80 transition">
                                                </a>
                                            </template>
                                            <template x-if="!task.image_after">
                                                <p class="text-gray-500 text-sm">Tidak ada foto.</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!task.report_text">
                                <p class="text-gray-500">Belum ada laporan yang dikirim untuk tugas ini.</p>
                            </template>
                        </div>
                    </div>

                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold border-b pb-2 mb-4">Informasi Tambahan</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-500">Jenis Tugas:</span><span
                                        class="font-semibold text-gray-800" x-text="task.task_type.name_task"></span>
                                </div>
                                <div class="flex justify-between"><span class="text-gray-500">Departemen:</span><span
                                        class="font-semibold text-gray-800" x-text="task.task_type.departemen"></span>
                                </div>
                                <div class="flex justify-between"><span class="text-gray-500">Prioritas:</span><span
                                        class="font-semibold text-gray-800" x-text="task.priority || 'Low'"></span>
                                </div>
                                <div class="flex justify-between"><span class="text-gray-500">Lokasi:</span><span
                                        class="font-semibold text-gray-800 text-right"
                                        x-text="task.room ? `${task.room.floor.building.name_building} / ${task.room.floor.name_floor}` : 'Tidak spesifik'"></span>
                                </div>
                                <div class="flex justify-between"><span class="text-gray-500">Aset Terkait:</span><span
                                        class="font-semibold text-gray-800"
                                        x-text="task.asset ? task.asset.name_asset : '-'"></span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Dikerjakan
                                        oleh:</span><span class="font-semibold text-gray-800"
                                        x-text="task.staff ? task.staff.name : 'Belum Diambil'"></span></div>
                            </div>
                        </div>

                        <template
                            x-if="(task.status === 'in_progress' || task.status === 'rejected') && currentUser.id === task.user_id">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-bold border-b pb-2 mb-4">Submit Laporan</h3>
                                <div x-show="errorMessage"
                                    class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4"
                                    role="alert">
                                    <p class="font-bold">Oops!</p>
                                    <p x-text="errorMessage"></p>
                                </div>
                                <form @submit.prevent="submitReport">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="report_text" value="Deskripsi Laporan" /><textarea
                                                x-model="formData.report_text" id="report_text" rows="4"
                                                class="mt-1 block w-full rounded-md" required></textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="asset_id" value="Aset Diperbaiki (S/N)" /><select
                                                x-model="formData.asset_id" id="asset_id"
                                                class="mt-1 block w-full rounded-md">
                                                <option value="">-- Tidak ada --</option><template
                                                    x-for="asset in assets" :key="asset.id">
                                                    <option :value="asset.id"
                                                        x-text="`${asset.name_asset} (${asset.serial_number || 'No S/N'})`">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                        <div><label class="block text-sm font-medium">Foto Sebelum</label><input
                                                type="file" @change="previewImage($event, 'before')" accept="image/*"
                                                class="mt-1 block w-full text-sm" required><template
                                                x-if="imageBeforePreview"><img :src="imageBeforePreview"
                                                    class="mt-2 rounded-md h-32 w-auto object-cover"></template></div>
                                        <div><label class="block text-sm font-medium">Foto Sesudah</label><input
                                                type="file" @change="previewImage($event, 'after')" accept="image/*"
                                                class="mt-1 block w-full text-sm" required><template
                                                x-if="imageAfterPreview"><img :src="imageAfterPreview"
                                                    class="mt-2 rounded-md h-32 w-auto object-cover"></template></div>
                                        <x-primary-button class="w-full justify-center" ::disabled="isSubmitting"><span
                                                x-show="!isSubmitting">Kirim Laporan</span><span
                                                x-show="isSubmitting">Mengirim...</span></x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </template>

                        <template x-if="task.status === 'pending_review' && currentUser.id === task.created_by">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-bold border-b pb-2 mb-4">Review Laporan</h3>
                                <div class="space-y-2">
                                    <button @click="submitApproval()"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-green-500">Setujui</button>
                                    <button @click="openRejectionModal()"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border rounded-md font-semibold text-xs text-white uppercase hover:bg-red-500">Tolak
                                        (Revisi)</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="showRejectionModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen">
                        <div @click="showRejectionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                            <h3 class="text-lg font-medium text-gray-900">Alasan Penolakan</h3>
                            <p class="mt-1 text-sm text-gray-600">Tulis alasan mengapa tugas ini ditolak. Pesan ini akan
                                dikirimkan ke staff.</p>
                            <div class="mt-4"><textarea x-model="rejectionNotes" rows="4"
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Contoh: Lampiran foto kurang jelas, mohon ulangi."></textarea></div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <x-secondary-button @click="showRejectionModal = false">Batal</x-secondary-button>
                                <x-danger-button @click="submitRejection()">Kirim Penolakan</x-danger-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function taskDetail(data) {
        return {
            taskId: data.taskId, currentUser: data.currentUser, task: data.taskData, assets: data.assets,
            isSubmitting: false, errorMessage: '',
            formData: { report_text: '', asset_id: '', image_before: null, image_after: null },
            imageBeforePreview: null, imageAfterPreview: null,
            showRejectionModal: false, rejectionNotes: '',
            notification: { show: false, message: '', type: 'success' },

            getTaskDetails() {
                fetch(`/api/tasks/${this.taskId}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json()).then(data => this.task = data);
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

            async submitReport() {
                this.isSubmitting = true; this.errorMessage = '';
                const fd = new FormData();
                fd.append('report_text', this.formData.report_text);
                fd.append('asset_id', this.formData.asset_id);
                if (this.formData.image_before) fd.append('image_before', this.formData.image_before);
                if (this.formData.image_after) fd.append('image_after', this.formData.image_after);
                fd.append('_method', 'POST');

                await fetch('/sanctum/csrf-cookie');
                fetch(`/api/tasks/${this.taskId}/report`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                    body: fd
                })
                .then(async res => res.ok ? res.json() : Promise.reject(await res.json()))
                .then(data => {
                    this.showNotification(data.message, 'success');
                    setTimeout(() => window.location.href = '{{ route('tasks.my_history') }}', 1500);
                })
                .catch(err => {
                    let msg = err.message || (err.errors ? Object.values(err.errors).flat().join('\\n') : 'Terjadi kesalahan.');
                    this.errorMessage = msg;
                })
                .finally(() => this.isSubmitting = false);
            },

            openRejectionModal() { this.rejectionNotes = ''; this.showRejectionModal = true; },
            submitApproval() { this.submitReview('completed'); },
            submitRejection() {
                if (!this.rejectionNotes.trim()) { alert('Alasan penolakan tidak boleh kosong.'); return; }
                this.submitReview('rejected', this.rejectionNotes);
                this.showRejectionModal = false;
            },
            async submitReview(decision, notes = null) {
                this.isSubmitting = true;
                await fetch('/sanctum/csrf-cookie');
                fetch(`/api/tasks/${this.taskId}/review`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() },
                    body: JSON.stringify({ decision: decision, rejection_notes: notes })
                })
                .then(res => res.json())
                .then(() => {
                    this.showNotification('Review berhasil dikirim.', 'success');
                    this.getTaskDetails(); // Refresh data tugas setelah review
                })
                .catch(() => this.showNotification('Gagal mengirim review.', 'error'))
                .finally(() => this.isSubmitting = false);
            },
            getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
            showNotification(message, type) { this.notification.message = message; this.notification.type = type; this.notification.show = true; setTimeout(() => this.notification.show = false, 3000); },
            statusColor(status) { return { in_progress: 'bg-blue-100 text-blue-800', pending_review: 'bg-yellow-100 text-yellow-800', completed: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100 text-gray-800'; },
            statusText(status) { return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); }
        }
    }
    </script>
</x-app-layout>