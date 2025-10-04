<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-user-circle mr-2"></i>
                {{ __('Detail Pengguna') }}
            </h2>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
                <a href="{{ route('users.edit', $data['user']->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-8">
                        {{-- Foto Profil --}}
                        <div class="flex-shrink-0 mb-6 md:mb-0">
                            <img class="h-32 w-32 rounded-full object-cover ring-4 ring-gray-200 dark:ring-gray-700"
                                src="{{ $data['user']->profile_picture ? Storage::url($data['user']->profile_picture) : asset('assets/backend/img/avatars/user-default.png') }}"
                                alt="Foto profil {{ $data['user']->name }}">
                        </div>

                        {{-- Detail Utama --}}
                        <div class="w-full text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $data['user']->name }}
                            </h3>
                            <p class="text-md text-indigo-500 dark:text-indigo-400 font-semibold">{{
                                $data['user']->role->role_name ?? 'Tanpa Peran' }}</p>

                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                                    {{-- Alamat Email --}}
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat Email
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-gray-100 flex items-center justify-center md:justify-start">
                                            <i class="fas fa-envelope me-2 text-gray-400"></i>
                                            <a href="mailto:{{ $data['user']->email }}" class="hover:underline">{{
                                                $data['user']->email }}</a>
                                        </dd>
                                    </div>

                                    {{-- Status Akun --}}
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Akun
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            @if($data['user']->status == 'active')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Aktif
                                            </span>
                                            @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Tidak Aktif
                                            </span>
                                            @endif
                                        </dd>
                                    </div>

                                    {{-- ID Chat Telegram --}}
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Chat
                                            Telegram</dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-gray-100 flex items-center justify-center md:justify-start">
                                            <i class="fab fa-telegram-plane me-2 text-gray-400"></i>
                                            <span>{{ $data['user']->telegram_chat_id ?? 'Tidak diatur' }}</span>
                                        </dd>
                                    </div>

                                    {{-- Bergabung Sejak --}}
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bergabung Sejak
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-gray-100 flex items-center justify-center md:justify-start">
                                            <i class="fas fa-calendar-alt me-2 text-gray-400"></i>
                                            @tanggal($data['user']->created_at)
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>