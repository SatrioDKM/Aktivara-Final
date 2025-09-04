<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Barang Keluar & Packing List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Packing List Baru</h3>
                    <form action="{{ route('packing_lists.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama
                                    Penerima</label>
                                <input type="text" name="recipient_name" id="recipient_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="asset_ids" class="block text-sm font-medium text-gray-700">Pilih
                                    Aset</label>
                                <select name="asset_ids[]" id="asset_ids" multiple required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm h-48">
                                    @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->name_asset }} (S/N: {{
                                        $asset->serial_number ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih
                                    dari satu.</p>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan
                                    (Opsional)</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center py-2 px-4 border shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Buat & Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Packing List</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No.
                                        Dokumen</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penerima
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total
                                        Item</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($packingLists as $list)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm font-medium">{{ $list->document_number }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $list->recipient_name }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $list->assets->count() }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ route('packing_lists.pdf', $list) }}" target="_blank"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm">Cetak PDF</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">Belum ada riwayat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $packingLists->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>