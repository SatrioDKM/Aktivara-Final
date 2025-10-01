<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lapor Keluhan - ManproApp</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="min-h-screen flex flex-col">
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
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
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
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Kami siap membantu. Sampaikan keluhan atau
                            laporan Anda melalui form di bawah ini.</p>
                    </div>

                    <form action="{{ route('guest.complaint.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="reporter_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Anda</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 ps-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="reporter_name" id="reporter_name"
                                        value="{{ old('reporter_name') }}"
                                        class="block w-full ps-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Contoh: John Doe" required>
                                </div>
                            </div>
                            <div>
                                <label for="task_type_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori
                                    Laporan</label>
                                <div class="mt-1">
                                    <select name="task_type_id" id="task_type_id" class="block w-full" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($data['taskTypes'] as $type)
                                        <option value="{{ $type->id }}" {{ old('task_type_id')==$type->id ? 'selected' :
                                            '' }}>{{ $type->name_task }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul
                                Singkat Laporan</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                placeholder="Contoh: AC tidak dingin, Keran kamar mandi bocor"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label for="location_text"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detail Lokasi</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 ps-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" name="location_text" id="location_text"
                                    value="{{ old('location_text') }}"
                                    placeholder="Contoh: Kamar 501, Lobi dekat pintu masuk"
                                    class="block w-full ps-10 sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                Lengkap</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                placeholder="Jelaskan sedetail mungkin masalah yang Anda alami (minimal 10 karakter).">{{ old('description') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#task_type_id').select2({
                theme: "classic",
                width: '100%',
                placeholder: '-- Pilih Kategori --'
            });

            @if(session('success'))
                iziToast.success({
                    title: 'Berhasil!',
                    message: '{{ session('success') }}',
                    position: 'topRight'
                });
            @endif

            @if(session('error') || $errors->any())
                let errorMessage = '';
                @if(session('error'))
                    errorMessage += '{{ session('error') }}<br>';
                @endif
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        errorMessage += '{{ $error }}<br>';
                    @endforeach
                @endif

                iziToast.error({
                    title: 'Oops, ada kesalahan!',
                    message: errorMessage,
                    position: 'topRight'
                });
            @endif
        });
    </script>
</body>

</html>