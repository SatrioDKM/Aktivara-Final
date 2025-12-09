<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Aktivara') }} - Sistem Manajemen Aset</title>
    <link rel="icon" href="{{ asset('logo/logoRounded.ico') }}" type="image/x-icon">

    <linkpreconnect href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-900 text-white font-sans selection:bg-indigo-500 selection:text-white">

    <div class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden">
        
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('background_gedung.jpeg') }}"
                 alt="Background" 
                 class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/80 via-indigo-900/50 to-gray-900"></div>
        </div>

        @if (Route::has('login'))
            <div class="absolute top-0 right-0 p-6 z-50">
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="font-semibold text-white hover:text-indigo-400 transition flex items-center gap-2 border border-white/20 px-6 py-2 rounded-full hover:bg-white/10 backdrop-blur-sm">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="font-semibold text-white hover:text-indigo-300 transition flex items-center gap-2 border border-white/20 px-6 py-2 rounded-full hover:bg-white/10 backdrop-blur-sm group">
                        <i class="fas fa-sign-in-alt"></i> Masuk Sistem <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                @endauth
            </div>
        @endif

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <div class="inline-flex items-center justify-center gap-4 sm:gap-8 p-6 sm:p-8 bg-white/5 border border-white/10 backdrop-blur-md rounded-3xl shadow-2xl mb-10 animate-fade-in-down">
                
                {{-- Logo Yayasan --}}
                <div class="group relative">
                    <div class="absolute -inset-2 bg-white/20 rounded-full blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <img src="{{ asset('logo/sasmita.png') }}" alt="Yayasan" 
                         class="relative block h-16 sm:h-24 w-auto object-contain bg-white rounded-full p-2 shadow-lg" 
                         title="Yayasan Sasmita Jaya">
                </div>

                {{-- Divider --}}
                <div class="h-12 sm:h-20 w-[1px] bg-gradient-to-b from-transparent via-white/50 to-transparent"></div>

                {{-- Logo Kampus --}}
                <div class="group relative">
                    <div class="absolute -inset-2 bg-white/20 rounded-full blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <img src="{{ asset('logo/UNPAM_logo1.png') }}" alt="Kampus" 
                         class="relative block h-16 sm:h-24 w-auto object-contain bg-white rounded-full p-2 shadow-lg" 
                         title="Universitas Pamulang">
                </div>

                {{-- Divider --}}
                <div class="h-12 sm:h-20 w-[1px] bg-gradient-to-b from-transparent via-white/50 to-transparent"></div>

                {{-- Logo Aplikasi --}}
                <div class="group relative">
                    <div class="absolute -inset-2 bg-indigo-500/40 rounded-full blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <img src="{{ asset('logo/logoRounded.png') }}" alt="Aktivara" 
                         class="relative block h-16 sm:h-24 w-auto object-contain bg-white rounded-full p-2 shadow-lg hover:scale-110 transition duration-300" 
                         title="Aktivara App">
                </div>
            </div>

            <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight text-white mb-6 drop-shadow-lg">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-white">AKTIVARA</span>
            </h1>
            
            <p class="mt-4 text-lg sm:text-2xl text-indigo-100 max-w-3xl mx-auto font-light leading-relaxed">
                Sistem Informasi Manajemen Aset & Inventaris <br class="hidden sm:block">
                <span class="font-semibold text-white">Universitas Pamulang</span>
            </p>

            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-1">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-8 py-4 bg-white text-indigo-900 hover:bg-gray-100 rounded-xl font-bold text-lg shadow-lg hover:shadow-white/20 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fas fa-key"></i> Login Pegawai
                    </a>
                    
                    {{-- Tombol Lapor Tamu (Opsional, jika fitur ini aktif) --}}
                    <a href="{{ route('guest.complaint.create') }}" 
                       class="px-8 py-4 bg-transparent border border-white/30 hover:bg-white/10 text-white rounded-xl font-semibold text-lg backdrop-blur-sm transition flex items-center justify-center gap-2">
                        <i class="fas fa-bullhorn"></i> Lapor Kerusakan
                    </a>
                @endauth
            </div>

        </div>

        <div class="absolute bottom-0 w-full bg-black/20 backdrop-blur-md border-t border-white/5 py-6 hidden md:block">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-3 gap-8 text-center text-sm text-indigo-200">
                <div class="flex flex-col items-center gap-2">
                    <div class="h-10 w-10 bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-300">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <span class="font-medium">Manajemen Aset & Stok</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="h-10 w-10 bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-300">
                        <i class="fas fa-tools"></i>
                    </div>
                    <span class="font-medium">Monitoring & Maintenance</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="h-10 w-10 bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-300">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span class="font-medium">Pelaporan Real-time</span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-2 text-xs text-white/30 md:hidden">
            &copy; {{ date('Y') }} Aktivara. UNPAM.
        </div>
    </div>
</body>
</html>