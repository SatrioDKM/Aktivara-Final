<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <i class="fas fa-envelope-open-text fa-lg text-green-600"></i>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-gray-800 dark:text-gray-200">Verifikasi Alamat Email Anda</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan
            mengklik link yang baru saja kami kirimkan? Jika Anda tidak menerima email, kami akan dengan senang hati
            mengirimkan yang lain.') }}
        </p>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div>
                <x-primary-button>
                    <i class="fas fa-paper-plane me-2"></i>
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    {{-- HAPUS BAGIAN INI --}}
    {{-- @push('styles') ... @endpush --}}

    @push('scripts')
    {{-- HAPUS LINK CDN INI --}}
    <script>
        // Script ini akan tetap berfungsi karena window.iziToast sudah diset di app.js
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status') == 'verification-link-sent')
                iziToast.success({
                    title: 'Terkirim!',
                    message: 'Link verifikasi baru telah dikirim ke alamat email Anda.',
                    position: 'topRight'
                });
            @endif
        });
    </script>
    @endpush
</x-guest-layout>