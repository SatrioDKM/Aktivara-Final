<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jenis Tugas: ') . $data['taskType']->name_task }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8" x-data="taskTypeForm(@js($data['taskType']))">
                    <form @submit.prevent="save()">
                        {{-- ... Isi form sama seperti create, tapi value diisi dari data taskType ... --}}
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('master.task_types.index') }}" class="...">Batal</a>
                            <x-primary-button type="submit" ::disabled="isSubmitting">Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    {{-- ... CDN JS ... --}}
    <script>
        function taskTypeForm(taskType) {
                return {
                    initData(taskType) {
                        // ... Logika inisialisasi data form ...
                    },
                    async save() {
                        // ... Logika AJAX PUT ...
                    }
                }
            }
    </script>
    @endpush
</x-app-layout>