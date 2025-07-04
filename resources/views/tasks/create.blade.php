<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Tugas Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div x-data="createTaskForm()">
                <!-- Notifikasi -->
                <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed top-5 right-5 z-50">
                    <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
                        :class="{ 'bg-green-100 text-green-700': notification.type === 'success', 'bg-red-100 text-red-700': notification.type === 'error' }">
                        <span x-text="notification.message"></span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submitForm" x-ref="form" class="p-6 bg-white border-b border-gray-200">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="title" value="Judul Tugas" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" x-model="formData.title"
                                    required autofocus />
                            </div>
                            <div>
                                <x-input-label for="task_type_id" value="Jenis Tugas" />
                                <select x-model="formData.task_type_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Jenis Tugas --</option>
                                    @foreach($taskTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name_task }} ({{ $type->departemen }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="description" value="Deskripsi Tugas" />
                                <textarea x-model="formData.description" rows="4"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button ::disabled="isSubmitting">
                                    <span x-show="!isSubmitting">Buat Tugas</span>
                                    <span x-show="isSubmitting">Menyimpan...</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createTaskForm() {
            return {
                formData: { title: '', task_type_id: '', description: '' },
                isSubmitting: false,
                notification: { show: false, message: '', type: 'success' },

                async submitForm() {
                    this.isSubmitting = true;
                    await fetch('/sanctum/csrf-cookie');
                    fetch('{{ route('api.tasks.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const err = await res.json().catch(() => ({ message: 'Terjadi kesalahan tidak terduga.' }));
                            throw err;
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.showNotification('Tugas berhasil dibuat!', 'success');
                        this.$refs.form.reset(); // Reset form setelah berhasil
                        this.formData = { title: '', task_type_id: '', description: '' };
                    })
                    .catch(err => {
                        let msg = 'Gagal membuat tugas.';
                        if (err.errors) {
                            msg = Object.values(err.errors).flat().join(' ');
                        } else if (err.message) {
                            msg = err.message;
                        }
                        this.showNotification(msg, 'error');
                    })
                    .finally(() => this.isSubmitting = false);
                },

                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                },

                getCsrfToken() {
                    const csrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
                    if (csrfCookie) {
                        return decodeURIComponent(csrfCookie.split('=')[1]);
                    }
                    return '';
                }
            }
        }
    </script>
</x-app-layout>