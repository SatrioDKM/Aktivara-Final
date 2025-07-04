<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- PENTING: Tambahkan enctype untuk upload file --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Foto Profil -->
        <div>
            <x-input-label for="profile_picture" :value="__('Foto Profil')" />
            <div class="mt-2 flex items-center gap-x-3">
                <!-- Tampilkan foto profil saat ini -->
                <img class="h-20 w-20 rounded-full object-cover"
                    src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://placehold.co/80x80/e2e8f0/64748b?text=Foto' }}"
                    alt="Foto profil saat ini">
                <input id="profile_picture" name="profile_picture" type="file" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
        </div>

        <!-- Nama -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Alamat email Anda belum terverifikasi.') }}

                    <button form="send-verification"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- ID Chat Telegram -->
        <div>
            <x-input-label for="telegram_chat_id" :value="__('ID Chat Telegram (Opsional)')" />
            <x-text-input id="telegram_chat_id" name="telegram_chat_id" type="text" class="mt-1 block w-full"
                :value="old('telegram_chat_id', $user->telegram_chat_id)" autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('telegram_chat_id')" />
        </div>

        <!-- Info Peran & Status (Read-only) -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label :value="__('Peran (Role)')" />
                <p
                    class="mt-1 block w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-500 text-sm">
                    {{ $user->role->role_name ?? 'Tidak ada peran' }}
                </p>
            </div>
            <div>
                <x-input-label :value="__('Status Akun')" />
                <p
                    class="mt-1 block w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-500 text-sm">
                    {{ Str::title($user->status) }}
                </p>
            </div>
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>