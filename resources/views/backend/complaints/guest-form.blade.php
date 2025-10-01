<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapor Keluhan - ManproApp</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="min-h-screen flex flex-col" x-data="guestComplaintForm">
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('welcome') }}">
                            <x-application-logo
                                class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                        <span class="font-semibold text-lg">ManproApp</span>
                    </div>
                    <div>
                        <a href="{{ route('welcome') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center py-12 px-4">
            <div class="w-full max-w-2xl">
                <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-lg">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Form Laporan Keluhan</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Sampaikan keluhan atau laporan Anda melalui
                            form di bawah ini.</p>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="reporter_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama
                                    Anda</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 ps-3 flex items-center pointer-events-none"><i
                                            class="fas fa-user text-gray-400"></i></div>
                                    <input type="text" x-model="formData.reporter_name" id="reporter_name"
                                        class="block w-full ps-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: John Doe" required>
                                </div>
                                <template x-if="errors.reporter_name">
                                    <p x-text="errors.reporter_name[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>
                            <div wire:ignore>
                                <label for="task_type_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori
                                    Laporan</label>
                                <select id="task_type_id" class="block w-full" required>
                                    <option></option>
                                    @foreach($data['taskTypes'] as $type)
                                    <option value="{{ $type->id }}">{{ $type->name_task }}</option>
                                    @endforeach
                                </select>
                                <template x-if="errors.task_type_id">
                                    <p x-text="errors.task_type_id[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Singkat
                                Laporan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 ps-3 flex items-center pointer-events-none"><i
                                        class="fas fa-heading text-gray-400"></i></div>
                                <input type="text" x-model="formData.title" id="title"
                                    placeholder="Contoh: AC tidak dingin, Keran kamar mandi bocor"
                                    class="block w-full ps-10 rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <template x-if="errors.title">
                                <p x-text="errors.title[0]" class="text-xs text-red-500 mt-1"></p>
                            </template>
                        </div>

                        <div>
                            <label for="location_text"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Detail
                                Lokasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 ps-3 flex items-center pointer-events-none"><i
                                        class="fas fa-map-marker-alt text-gray-400"></i></div>
                                <input type="text" x-model="formData.location_text" id="location_text"
                                    placeholder="Contoh: Kamar 501, Lobi dekat pintu masuk"
                                    class="block w-full ps-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <template x-if="errors.location_text">
                                <p x-text="errors.location_text[0]" class="text-xs text-red-500 mt-1"></p>
                            </template>
                        </div>

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                                Lengkap</label>
                            <textarea x-model="formData.description" id="description" rows="4"
                                class="block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                placeholder="Jelaskan sedetail mungkin masalah yang Anda alami (minimal 10 karakter)."></textarea>
                            <template x-if="errors.description">
                                <p x-text="errors.description[0]" class="text-xs text-red-500 mt-1"></p>
                            </template>
                        </div>

                        <div class="pt-2">
                            <button type="submit" :disabled="loading"
                                class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors disabled:bg-indigo-400 disabled:cursor-not-allowed">
                                <i class="fas fa-circle-notch fa-spin me-2" x-show="loading" style="display: none;"></i>
                                <i class="fas fa-paper-plane me-2" x-show="!loading"></i>
                                <span x-text="loading ? 'Mengirim...' : 'Kirim Laporan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <footer class="text-center py-6 px-4 text-sm text-gray-500 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} ManproApp. All rights reserved.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('guestComplaintForm', () => ({
                formData: {
                    reporter_name: '',
                    task_type_id: '',
                    title: '',
                    location_text: '',
                    description: '',
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                loading: false,
                errors: {},

                init() {
                    const self = this;
                    $('#task_type_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Kategori --'
                    }).on('change', function() {
                        self.formData.task_type_id = $(this).val();
                    });
                },

                submitForm() {
                    this.loading = true;
                    this.errors = {};

                    axios.post('{{ route("api.guest.complaints.store") }}', this.formData)
                        .then(response => {
                            iziToast.success({
                                title: 'Berhasil!',
                                message: response.data.message,
                                position: 'topRight'
                            });
                            // Reset form
                            this.formData.reporter_name = '';
                            this.formData.title = '';
                            this.formData.location_text = '';
                            this.formData.description = '';
                            $('#task_type_id').val(null).trigger('change');
                        })
                        .catch(error => {
                            let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (error.response && error.response.status === 422) {
                                this.errors = error.response.data.errors;
                                errorMessage = 'Harap periksa kembali isian form Anda.';
                            } else if (error.response && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                            iziToast.error({
                                title: 'Oops!',
                                message: errorMessage,
                                position: 'topRight'
                            });
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }))
        });
    </script>
</body>

</html>