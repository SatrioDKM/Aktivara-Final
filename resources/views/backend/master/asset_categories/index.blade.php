<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Kategori Aset') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '', openModal: false, categoryId: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Header: Tambah + Pencarian -->
                    <div class="mb-4 flex items-center space-x-4">
                        <!-- Input Pencarian -->
                        <input type="text" placeholder="Cari kategori..." x-model="search"
                            class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-indigo-300 text-sm">

                        <!-- Tombol Tambah -->
                        <a href="{{ route('master.asset_categories.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Tambah Kategori Aset
                        </a>
                    </div>


                    <!-- Notifikasi -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div id="api-success-message" class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg" style="display: none;"></div>

                    <!-- Tabel -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($categories as $category)
                                    <tr x-show=" '{{ strtolower($category->name) }}'.includes(search.toLowerCase()) ">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-2">
                                            <!-- Button Edit -->
                                            <button @click="window.location.href='{{ route('master.asset_categories.edit', $category) }}'"
                                                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                                                Edit
                                            </button>

                                            <!-- Button Hapus -->
                                            <button @click="openModal = true; categoryId = {{ $category->id }}"
                                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada data kategori aset.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-4">Apakah Anda yakin ingin menghapus kategori ini?</p>
                <div class="flex justify-end space-x-2">
                    <button @click="openModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>

                    <form :action="`/master/asset_categories/${categoryId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tampilkan pesan sukses jika ada di URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');

            if (status === 'success' && message) {
                const successContainer = document.getElementById('api-success-message');
                successContainer.textContent = decodeURIComponent(message);
                successContainer.style.display = 'block';
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
    @endpush
</x-app-layout>
