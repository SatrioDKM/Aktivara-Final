<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lapor Keluhan - ManproApp</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('welcome') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                        <span class="font-semibold text-lg">ManproApp</span>
                    </div>
                    <div>
                        <a href="{{ route('welcome') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center py-12 px-4">
            <div class="w-full max-w-2xl">
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Form Laporan Keluhan</h1>
                        <p class="text-gray-600 mt-2">Kami siap membantu. Sampaikan keluhan atau laporan Anda melalui
                            form di bawah ini.</p>
                    </div>

                    @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6"
                        role="alert">
                        <p class="font-bold">Berhasil!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                    @endif
                    @if(session('error') || $errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                        <p class="font-bold">Oops, ada kesalahan!</p>
                        @if(session('error'))
                        <p>{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endif

                    <form action="{{ route('guest.complaint.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama
                                    Anda</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="reporter_name" id="reporter_name"
                                        value="{{ old('reporter_name') }}"
                                        class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                            </div>

                            <div>
                                <label for="task_type_id" class="block text-sm font-medium text-gray-700">Kategori
                                    Laporan</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                        </svg>
                                    </div>
                                    <select name="task_type_id" id="task_type_id"
                                        class="block w-full pl-10 pr-10 sm:text-sm border-gray-300 rounded-md" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($taskTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('task_type_id')==$type->id ? 'selected' :
                                            '' }}>{{ $type->name_task }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Singkat
                                Laporan</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                placeholder="Contoh: AC tidak dingin, Keran kamar mandi bocor"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div>
                            <label for="location_text" class="block text-sm font-medium text-gray-700">Detail
                                Lokasi</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="location_text" id="location_text"
                                    value="{{ old('location_text') }}"
                                    placeholder="Contoh: Kamar 501, Lobi dekat pintu masuk"
                                    class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi
                                Lengkap</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required
                                placeholder="Jelaskan sedetail mungkin masalah yang Anda alami...">{{ old('description') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <footer class="text-center py-6 px-4 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} ManproApp. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>