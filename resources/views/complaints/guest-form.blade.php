<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Keluhan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4 sm:p-8 max-w-2xl">
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md">

            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Form Laporan Keluhan</h1>
                <p class="text-gray-500">Kami siap membantu. Silakan sampaikan keluhan atau laporan Anda melalui form di
                    bawah ini.</p>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif


            <form action="{{ route('guest.complaint.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Anda</label>
                        <input type="text" name="reporter_name" id="reporter_name" value="{{ old('reporter_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div>
                        <label for="task_type_id" class="block text-sm font-medium text-gray-700">Kategori
                            Laporan</label>
                        <select name="task_type_id" id="task_type_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($taskTypes as $type)
                            <option value="{{ $type->id }}" {{ old('task_type_id')==$type->id ? 'selected' : '' }}>
                                {{ $type->name_task }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Judul Singkat Laporan</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            placeholder="Contoh: AC tidak dingin, Keran bocor"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div>
                        <label for="location_text" class="block text-sm font-medium text-gray-700">Detail Lokasi</label>
                        <input type="text" name="location_text" id="location_text" value="{{ old('location_text') }}"
                            placeholder="Contoh: Kamar 501, Lobi dekat pintu masuk"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi
                            Lengkap</label>
                        <textarea name="description" id="description" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required>{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
        <footer class="text-center mt-6 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} ManproApp. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>