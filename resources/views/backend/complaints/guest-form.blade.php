<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapor Keluhan - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Select2 CSS (Jika belum ada di app.css) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom Select2 Styling agar Perfect Match dengan Tailwind Input */
        .select2-container--classic .select2-selection--single {
            height: 42px !important; /* Samakan tinggi dengan input text lain */
            border: 1px solid #d1d5db !important; /* gray-300 */
            border-radius: 0.5rem !important; /* rounded-lg (biar sama lengkungnya) */
            display: flex !important;
            align-items: center !important; /* KUNCI: Biar teks pas di tengah vertikal */
            padding-left: 0.75rem !important; /* Padding kiri standar */
            background-color: #fff !important;
        }

        /* Teks di dalam Select2 */
        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding: 0 !important;
            color: #374151 !important; /* text-gray-700 */
            width: 100%;
        }

        /* Panah Dropdown */
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            border-left: none !important;
            background: transparent !important;
            top: 1px !important;
            right: 5px !important;
        }

        /* Support Dark Mode (Opsional, jaga-jaga) */
        @media (prefers-color-scheme: dark) {
            .select2-container--classic .select2-selection--single {
                background-color: #111827 !important; /* gray-900 */
                border-color: #374151 !important; /* gray-700 */
            }
            .select2-container--classic .select2-selection--single .select2-selection__rendered {
                color: #9a0000ff !important; /* text-gray-300 */
            }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans">
    
    <div class="min-h-screen flex flex-col" x-data="guestComplaintForm">
        
        {{-- HEADER / NAVBAR --}}
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    
                    {{-- AREA 3 LOGO --}}
                    <div class="flex items-center gap-4">
                        {{-- Grup Institusi --}}
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('logo/sasmita.png') }}" alt="Yayasan" 
                                 class="h-10 w-auto md:h-12 hover:scale-105 transition duration-300" title="Yayasan Sasmita Jaya">
                            <img src="{{ asset('logo/UNPAM_logo1.png') }}" alt="Kampus" 
                                 class="h-10 w-auto md:h-12 hover:scale-105 transition duration-300" title="Universitas Pamulang">
                        </div>

                        {{-- Divider --}}
                        <div class="h-10 w-[1.5px] bg-gray-300 dark:bg-gray-600 rounded-full hidden sm:block"></div>

                        {{-- Logo Aplikasi --}}
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('logo/logoRounded.png') }}" alt="Aktivara" 
                                 class="h-9 w-auto md:h-10 hover:rotate-12 transition duration-300">
                            <span class="hidden md:block font-bold text-xl text-gray-800 dark:text-gray-100 tracking-tight">
                                Aktivara
                            </span>
                        </div>
                    </div>

                    {{-- Tombol Kembali --}}
                    <div>
                        <a href="{{ route('welcome') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-home me-2 text-indigo-500"></i> Beranda
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-grow">
            
            {{-- Hero Section Mini --}}
            <div class="bg-indigo-900 text-white py-12 px-4 relative overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                    {{-- Pattern Background --}}
                    <svg class="h-full w-full" width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                    </svg>
                </div>
                <div class="max-w-3xl mx-auto text-center relative z-10">
                    <h1 class="text-3xl sm:text-4xl font-extrabold mb-2">Layanan Pengaduan & Keluhan</h1>
                    <p class="text-indigo-200 text-lg">Sampaikan laporan kerusakan sarana & prasarana kampus secara cepat dan mudah.</p>
                </div>
            </div>

            {{-- Form Container --}}
            <div class="max-w-3xl mx-auto px-4 -mt-8 pb-12 relative z-20">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    {{-- Form Header Line --}}
                    <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                    <div class="p-6 sm:p-10">
                        <form @submit.prevent="submitForm" class="space-y-6" novalidate>
                            @csrf
                            
                            {{-- Baris 1: Nama & Kategori --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama Pelapor --}}
                                <div>
                                    <label for="reporter_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" x-model="formData.reporter_name" id="reporter_name"
                                            class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:border-gray-600 sm:text-sm py-2.5"
                                            placeholder="Nama Anda / Identitas" required>
                                    </div>
                                    <template x-if="errors.reporter_name">
                                        <p x-text="errors.reporter_name[0]" class="text-xs text-red-500 mt-1"></p>
                                    </template>
                                </div>

                                {{-- Kategori --}}
                                <div wire:ignore>
                                    <label for="task_type_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                        Kategori Masalah <span class="text-red-500">*</span>
                                    </label>
                                    <select id="task_type_id" class="block w-full" required>
                                        <option value=""></option>
                                        @foreach($data['taskTypes'] as $type)
                                            <option value="{{ $type->id }}">{{ $type->name_task }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="errors.task_type_id">
                                        <p x-text="errors.task_type_id[0]" class="text-xs text-red-500 mt-1"></p>
                                    </template>
                                </div>
                            </div>

                            {{-- Judul Laporan --}}
                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Judul Laporan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-heading text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.title" id="title"
                                        placeholder="Cth: AC Ruang V.301 Bocor / Lampu Koridor Mati"
                                        class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:border-gray-600 sm:text-sm py-2.5"
                                        required>
                                </div>
                                <template x-if="errors.title">
                                    <p x-text="errors.title[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>

                            {{-- Lokasi --}}
                            <div>
                                <label for="location_text" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Lokasi Kejadian <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" x-model="formData.location_text" id="location_text"
                                        placeholder="Cth: Gedung A, Lantai 3, Depan Lift"
                                        class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:border-gray-600 sm:text-sm py-2.5"
                                        required>
                                </div>
                                <template x-if="errors.location_text">
                                    <p x-text="errors.location_text[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Rincian Masalah <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                        <i class="fas fa-align-left text-gray-400"></i>
                                    </div>
                                    <textarea x-model="formData.description" id="description" rows="4"
                                        class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:border-gray-600 sm:text-sm"
                                        required
                                        placeholder="Jelaskan detail kerusakan atau keluhan yang Anda temukan..."></textarea>
                                </div>
                                <template x-if="errors.description">
                                    <p x-text="errors.description[0]" class="text-xs text-red-500 mt-1"></p>
                                </template>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="pt-4">
                                <button type="submit" :disabled="loading"
                                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                                    
                                    <span x-show="!loading" class="flex items-center">
                                        <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                                    </span>
                                    
                                    <span x-show="loading" class="flex items-center" style="display: none;">
                                        <i class="fas fa-circle-notch fa-spin me-2"></i> Sedang Mengirim...
                                    </span>
                                </button>
                                <p class="text-center text-xs text-gray-500 mt-4">
                                    Laporan Anda akan diteruskan ke tim Sarana Prasarana UNPAM untuk ditindaklanjuti.
                                </p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }} - Universitas Pamulang. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    {{-- SCRIPT ALPINE JS & SELECT2 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- iziToast JS (Wajib jika menggunakan notifikasi toast) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" type="text/javascript"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('guestComplaintForm', () => ({
                formData: {
                    reporter_name: '',
                    task_type_id: '',
                    title: '',
                    location_text: '',
                    description: ''
                },
                loading: false,
                errors: {},

                init() {
                    const self = this;
                    
                    // Inisialisasi Select2
                    $('#task_type_id').select2({
                        theme: "classic",
                        width: '100%',
                        placeholder: '-- Pilih Kategori Masalah --',
                        allowClear: true
                    }).on('change', function() {
                        self.formData.task_type_id = $(this).val();
                    });
                },

                submitForm() {
                    this.loading = true;
                    this.errors = {};

                    axios.post('{{ route("api.guest.complaint.store") }}', this.formData)
                        .then(response => {
                            // Sukses
                            iziToast.success({
                                title: 'Berhasil!',
                                message: response.data.message,
                                position: 'topCenter',
                                timeout: 5000,
                                icon: 'fas fa-check-circle'
                            });

                            // Reset Form
                            this.formData = {
                                reporter_name: '',
                                task_type_id: '',
                                title: '',
                                location_text: '',
                                description: ''
                            };
                            $('#task_type_id').val(null).trigger('change');
                        })
                        .catch(error => {
                            // Error Handling
                            let errorMessage = 'Terjadi kesalahan pada server.';
                            
                            if (error.response) {
                                if (error.response.status === 422) {
                                    this.errors = error.response.data.errors;
                                    errorMessage = 'Mohon lengkapi formulir dengan benar.';
                                } else if (error.response.data && error.response.data.message) {
                                    errorMessage = error.response.data.message;
                                }
                            }

                            iziToast.error({
                                title: 'Gagal!',
                                message: errorMessage,
                                position: 'topCenter',
                                icon: 'fas fa-exclamation-triangle'
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