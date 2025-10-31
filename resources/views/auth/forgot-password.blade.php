<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Lupa Password?</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Jangan khawatir. Cukup masukkan alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang
            password Anda.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email Anda')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input id="email" class="block w-full ps-10" type="email" name="email" :value="old('email')"
                    required autofocus placeholder="Masukkan email terdaftar" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Kirim Link Reset Password') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                href="{{ route('login') }}">
                {{ __('Kembali ke Login') }}
            </a>
        </div>
    </form>

    {{-- HAPUS BAGIAN INI --}}
    {{-- @push('styles') ... @endpush --}}

    @push('scripts')
    {{-- HAPUS LINK CDN INI --}}
    <script>
        // Script ini akan tetap berfungsi karena window.iziToast sudah diset di app.js
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                iziToast.success({
                    title: 'Berhasil!',
                    message: '{{ session('status') }}',
                    position: 'topRight'
                });
            @endif
        });
    </script>
    @endpush
</x-guest-layout>