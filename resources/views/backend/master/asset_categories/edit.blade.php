<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kategori Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Error Message --}}
                    @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                        <strong>Oops! Terjadi kesalahan.</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Form --}}
                    <form id="edit-category-form" method="POST"
                        action="{{ route('master.asset_categories.update', $assetCategory) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nama Kategori')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                value="{{ old('name', $assetCategory->name) }}" required autofocus />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('master.asset_categories.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <x-primary-button id="save-button">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Optional: AJAX submit --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('edit-category-form');
            const saveButton = document.getElementById('save-button');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                saveButton.disabled = true;
                saveButton.textContent = 'Menyimpan...';

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const result = await response.json();
                        alert(result.message || 'Terjadi kesalahan server.');
                        saveButton.disabled = false;
                        saveButton.textContent = 'Update';
                        return;
                    }

                    // Berhasil, redirect ke index
                    window.location.href = "{{ route('master.asset_categories.index') }}?status=success&message=Kategori berhasil diperbarui.";

                } catch (error) {
                    alert('Tidak dapat terhubung ke server. ' + error.message);
                    saveButton.disabled = false;
                    saveButton.textContent = 'Update';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>