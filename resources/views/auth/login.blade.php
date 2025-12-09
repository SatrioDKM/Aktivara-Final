<x-guest-layout>
    <div class="flex min-h-screen">
        {{-- BAGIAN KIRI: BRANDING IMAGE & LOGOS (Hidden di HP) --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-indigo-900 text-white items-center justify-center">
            {{-- Gambar Latar Belakang (Ganti URL ini dengan foto kampusmu) --}}
            <img src="{{ asset('background_gedung.jpeg') }}"
                 alt="Background Kampus"
                 class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">
            
            <div class="relative z-10 p-12 text-center">
                {{-- AREA 3 LOGO (Versi Besar untuk Desktop) --}}
                <div class="flex items-center justify-center gap-6 mb-8 p-6 bg-white/10 rounded-2xl backdrop-blur-sm">
                    {{-- LOGO YAYASAN --}}
                    <img src="{{ asset('logo/sasmita.png') }}" alt="Yayasan"
                         class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg" title="Yayasan Sasmita Jaya" />
                    
                    {{-- DIVIDER --}}
                    <div class="h-16 w-[2px] bg-white/50 rounded-full"></div>

                    {{-- LOGO KAMPUS --}}
                    <img src="{{ asset('logo/UNPAM_logo1.png') }}" alt="Kampus"
                         class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg" title="Universitas Pamulang" />
                    
                    {{-- DIVIDER --}}
                    <div class="h-16 w-[2px] bg-white/50 rounded-full"></div>

                    {{-- LOGO APLIKASI --}}
                     <img src="{{ asset('logo/logoRounded.png') }}" alt="Aktivara"
                         class="block h-20 w-auto object-contain bg-white rounded-full p-1 shadow-lg hover:rotate-12 transition duration-300" title="Aktivara" />
                </div>
                
                <h2 class="text-4xl font-extrabold tracking-tight mb-4">
                    Selamat Datang di {{ config('app.name') }}
                </h2>
                <p class="text-lg text-indigo-100 max-w-md mx-auto">
                    Sistem Informasi Manajemen Aset & Inventaris Sarana Prasarana Universitas Pamulang.
                </p>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-md space-y-8">
                
                {{-- LOGO UNTUK MOBILE (Muncul hanya di HP) --}}
                <div class="lg:hidden flex justify-center mb-6">
                     <div class="inline-flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                        <img src="{{ asset('logo/sasmita.png') }}" class="h-8 w-auto" alt="Yayasan">
                        <div class="h-6 w-px bg-gray-300"></div>
                        <img src="{{ asset('logo/UNPAM_logo1.png') }}" class="h-8 w-auto" alt="Kampus">
                         <div class="h-6 w-px bg-gray-300"></div>
                        <img src="{{ asset('logo/logoRounded.png') }}" class="h-8 w-auto" alt="Aktivara">
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Silakan masukkan kredensial Anda untuk melanjutkan.
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                    @csrf

                    {{-- Email Address --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <x-text-input id="email" class="block w-full pl-10 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@unpam.ac.id" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <x-text-input id="password" class="block w-full pl-10 py-3" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat Saya') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400" href="{{ route('password.request') }}">
                                {{ __('Lupa password?') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <x-primary-button class="w-full justify-center py-3 text-base font-bold tracking-wider">
                            {{ __('Masuk Sekarang') }} <i class="fas fa-arrow-right ml-2"></i>
                        </x-primary-button>
                    </div>
                </form>

                 <p class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Dikelola oleh Sarana Prasarana UNPAM.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>