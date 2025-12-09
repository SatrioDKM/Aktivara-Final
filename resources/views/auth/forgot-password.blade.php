<x-guest-layout>
    <div class="flex min-h-screen">
        {{-- BAGIAN KIRI: BRANDING (Sama seperti Login) --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-indigo-900 text-white items-center justify-center">
            <img src="{{ asset('background_gedung.jpeg') }}"
                 alt="Background Kampus"
                 class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">
            
            <div class="relative z-10 p-12 text-center">
                 <div class="flex items-center justify-center gap-6 mb-8 p-6 bg-white/10 rounded-2xl backdrop-blur-sm">
                    <img src="{{ asset('logo/sasmita.png') }}" class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg"/>
                    <div class="h-16 w-[2px] bg-white/50 rounded-full"></div>
                    <img src="{{ asset('logo/UNPAM_logo1.png') }}" class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg"/>
                    <div class="h-16 w-[2px] bg-white/50 rounded-full"></div>
                     <img src="{{ asset('logo/logoRounded.png') }}" class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg hover:rotate-12 transition duration-300"/>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight mb-4">
                    Pemulihan Akun
                </h2>
                <p class="text-lg text-indigo-100 max-w-md mx-auto">
                    Jangan khawatir, kami akan membantu Anda mendapatkan kembali akses ke Aktivara.
                </p>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM LUPA PASSWORD --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-md space-y-8">
                 {{-- LOGO UNTUK MOBILE --}}
                <div class="lg:hidden flex justify-center mb-6">
                     <div class="inline-flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <img src="{{ asset('logo/sasmita.png') }}" class="h-8 w-auto">
                        <div class="h-6 w-px bg-gray-300"></div>
                        <img src="{{ asset('logo/UNPAM_logo1.png') }}" class="h-8 w-auto">
                         <div class="h-6 w-px bg-gray-300"></div>
                        <img src="{{ asset('logo/logoRounded.png') }}" class="h-8 w-auto">
                    </div>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900 mb-4">
                        <i class="fas fa-key text-2xl text-indigo-600 dark:text-indigo-300"></i>
                    </div>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        Lupa Password?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.') }}
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        <x-text-input id="email" class="block w-full pl-10 py-3" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@unpam.ac.id"/>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex flex-col space-y-4">
                        <x-primary-button class="w-full justify-center py-3 text-base font-bold tracking-wider">
                            {{ __('Kirim Tautan Reset') }} <i class="fas fa-paper-plane ml-2"></i>
                        </x-primary-button>

                        <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>