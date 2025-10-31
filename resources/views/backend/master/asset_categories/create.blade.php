<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kategori Aset Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div id="error-message-container" class="mb-4 hidden">
                        <div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                            <strong>Oops! Terjadi kesalahan.</strong>
                            <ul id="error-list" class="mt-2 list-disc list-inside"></ul>
                        </div>
                    </div>

                    {{-- Ganti action ke route API --}}
                    <form id="create-category-form" method="POST" action="{{ url('/api/asset-categories') }}">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nama Kategori')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus />
                            <span id="name-error" class="text-sm text-red-600 hidden"></span>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('master.asset_categories.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <x-primary-button id="save-button">
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('create-category-form');
            const saveButton = document.getElementById('save-button');
            const errorMessageContainer = document.getElementById('error-message-container');
            const errorList = document.getElementById('error-list');
            const nameError = document.getElementById('name-error');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                saveButton.disabled = true;
                saveButton.innerHTML = 'Menyimpan...';
                errorMessageContainer.classList.add('hidden');
                errorList.innerHTML = '';
                nameError.classList.add('hidden');

                const data = {
                    name: document.getElementById('name').value.trim(),
                };

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify(data),
                    });

                    const result = await response.json().catch(() => null);

                    if (!response.ok) {
                        errorMessageContainer.classList.remove('hidden');

                        if (response.status === 422 && result?.errors) {
                            for (const [key, messages] of Object.entries(result.errors)) {
                                const li = document.createElement('li');
                                li.textContent = messages[0];
                                errorList.appendChild(li);

                                if (key === 'name') {
                                    nameError.textContent = messages[0];
                                    nameError.classList.remove('hidden');
                                }
                            }
                        } else {
                            const li = document.createElement('li');
                            li.textContent = result?.message || 'Terjadi kesalahan server.';
                            errorList.appendChild(li);
                        }
                    } else {
                        // Sukses: redirect ke halaman index
                        window.location.href = "{{ route('master.asset_categories.index') }}?status=success&message=" + encodeURIComponent(result.message);
                    }
                } catch (error) {
                    errorMessageContainer.classList.remove('hidden');
                    const li = document.createElement('li');
                    li.textContent = 'Tidak dapat terhubung ke server. ' + error.message;
                    errorList.appendChild(li);
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Simpan';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>