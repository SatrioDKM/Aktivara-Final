<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-user-circle mr-2"></i>
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Bagian Informasi Profil --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Informasi Profil') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
                            </p>
                        </header>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            {{-- Upload Foto Profil dengan Preview --}}
                            <div x-data="{ photoName: null, photoPreview: null }">
                                <label for="profile_picture"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto
                                    Profil</label>
                                <div class="mt-2 flex items-center gap-x-4">
                                    <img x-show="!photoPreview"
                                        class="h-24 w-24 rounded-full object-cover ring-2 ring-gray-300 dark:ring-gray-600"
                                        src="{{ $data['user']->profile_picture ? Storage::url($data['user']->profile_picture) : asset('assets/backend/img/avatars/user-default.png') }}"
                                        alt="Foto profil saat ini">
                                    <div x-show="photoPreview" style="display: none;"
                                        class="h-24 w-24 rounded-full bg-cover bg-center ring-2 ring-indigo-400"
                                        :style="'background-image: url(\'' + photoPreview + '\');'">
                                    </div>
                                    <div>
                                        <input id="profile_picture" name="profile_picture" type="file" class="hidden"
                                            x-ref="photo" x-on:change="
                                            photoName = $refs.photo.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL($refs.photo.files[0]);
                                        " />
                                        <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                                            <i class="fas fa-upload mr-2"></i>
                                            {{ __('Pilih Foto Baru') }}
                                        </x-secondary-button>
                                        <p x-show="photoName" style="display: none;" class="text-sm text-gray-500 mt-2"
                                            x-text="photoName"></p>
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>

                            {{-- Input Nama --}}
                            <div>
                                <x-input-label for="name" :value="__('Nama')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <x-text-input id="name" name="name" type="text" class="block w-full ps-10"
                                        :value="old('name', $data['user']->name)" required autofocus autocomplete="name"
                                        placeholder="Nama lengkap Anda" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            {{-- Input Email --}}
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <x-text-input id="email" name="email" type="email" class="block w-full ps-10"
                                        :value="old('email', $data['user']->email)" required autocomplete="username"
                                        placeholder="alamat@email.com" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if ($data['user'] instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !
                                $data['user']->hasVerifiedEmail())
                                <div>
                                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                        {{ __('Alamat email Anda belum terverifikasi.') }}
                                        <button form="send-verification"
                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                        </button>
                                    </p>
                                </div>
                                @endif
                            </div>

                            {{-- Input Telegram --}}
                            <div>
                                <x-input-label for="telegram_chat_id" :value="__('ID Chat Telegram (Opsional)')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fab fa-telegram-plane text-gray-400"></i>
                                    </div>
                                    <x-text-input id="telegram_chat_id" name="telegram_chat_id" type="text"
                                        class="block w-full ps-10"
                                        :value="old('telegram_chat_id', $data['user']->telegram_chat_id)"
                                        autocomplete="off" placeholder="Contoh: 123456789" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('telegram_chat_id')" />
                            </div>

                            {{-- Info Role & Status (Read-only) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label :value="__('Peran (Role)')" />
                                    <p
                                        class="mt-1 block w-full px-3 py-2 bg-gray-100 dark:bg-gray-900 rounded-md border border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $data['user']->role->role_name ?? 'Tidak ada peran' }}
                                    </p>
                                </div>
                                <div>
                                    <x-input-label :value="__('Status Akun')" />
                                    <p
                                        class="mt-1 block w-full px-3 py-2 bg-gray-100 dark:bg-gray-900 rounded-md border border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ Str::title($data['user']->status) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('Simpan Perubahan') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            {{-- Bagian Update Password --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Ubah Password') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.')
                                }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div>
                                <x-input-label for="update_password_current_password"
                                    :value="__('Password Saat Ini')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-key text-gray-400"></i></div>
                                    <x-text-input id="update_password_current_password" name="current_password"
                                        type="password" class="block w-full ps-10" autocomplete="current-password"
                                        placeholder="Password Anda saat ini" />
                                </div>
                                <x-input-error :messages="$errors->updatePassword->get('current_password')"
                                    class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="update_password_password" :value="__('Password Baru')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i></div>
                                    <x-text-input id="update_password_password" name="password" type="password"
                                        class="block w-full ps-10" autocomplete="new-password"
                                        placeholder="Password baru yang kuat" />
                                </div>
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="update_password_password_confirmation"
                                    :value="__('Konfirmasi Password Baru')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i></div>
                                    <x-text-input id="update_password_password_confirmation"
                                        name="password_confirmation" type="password" class="block w-full ps-10"
                                        autocomplete="new-password" placeholder="Ketik ulang password baru" />
                                </div>
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                                    class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>
                                    <i class="fas fa-save mr-2"></i>
                                    {{ __('Simpan Password') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            {{-- Bagian Hapus Akun --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-red-600 dark:text-red-400">
                                {{ __('Hapus Akun') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara
                                permanen. Sebelum menghapus, harap unduh data apa pun yang ingin Anda simpan.') }}
                            </p>
                        </header>

                        <x-danger-button x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
                            <i class="fas fa-trash-alt me-2"></i>
                            {{ __('Hapus Akun') }}
                        </x-danger-button>

                        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara
                                    permanen. Silakan masukkan password Anda untuk mengonfirmasi penghapusan permanen
                                    ini.') }}
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="password_delete" value="{{ __('Password') }}" class="sr-only" />
                                    <div class="relative mt-1">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="fas fa-key text-gray-400"></i></div>
                                        <x-text-input id="password_delete" name="password" type="password"
                                            class="block w-full ps-10" placeholder="{{ __('Password') }}" />
                                    </div>
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        {{ __('Batal') }}
                                    </x-secondary-button>
                                    <x-danger-button class="ms-3">
                                        {{ __('Hapus Akun') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Menampilkan notifikasi iziToast berdasarkan status sesi dari controller
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('status') === 'profile-updated')
                window.iziToast.success({
                    title: 'Berhasil!',
                    message: 'Informasi profil Anda telah diperbarui.',
                    position: 'topRight'
                });
            @endif
            @if(session('status') === 'password-updated')
                window.iziToast.success({
                    title: 'Berhasil!',
                    message: 'Password Anda telah diperbarui.',
                    position: 'topRight'
                });
            @endif
            @if(session('status') === 'verification-link-sent')
                window.iziToast.info({
                    title: 'Info',
                    message: 'Link verifikasi baru telah dikirim ke email Anda.',
                    position: 'topRight'
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>