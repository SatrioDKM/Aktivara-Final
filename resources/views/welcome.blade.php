<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ManproApp - Manajemen Properti & Operasional</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 text-gray-700">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white/80 backdrop-blur-md shadow-sm fixed top-0 w-full z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('welcome') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                        <span class="font-semibold text-lg text-gray-800">ManproApp</span>
                    </div>
                    <div>
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-600 hover:text-indigo-600 transition duration-150">
                            Log in
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <div class="relative isolate px-6 pt-14 lg:px-8 bg-white">
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                    aria-hidden="true">
                    <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                    </div>
                </div>
                <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Manajemen Properti &
                            Operasional Terintegrasi</h1>
                        <p class="mt-6 text-lg leading-8 text-gray-600">Laporkan keluhan dengan mudah atau masuk ke
                            sistem untuk mengelola tugas, aset, dan laporan secara efisien.</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a href="{{ route('guest.complaint.create') }}"
                                class="rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-transform transform hover:scale-105">
                                Lapor Keluhan Sekarang
                            </a>
                            <a href="{{ route('login') }}"
                                class="group text-sm font-semibold leading-6 text-gray-900 flex items-center">
                                Masuk ke Dashboard <span
                                    class="transition-transform transform group-hover:translate-x-1"
                                    aria-hidden="true"><i class="fas fa-arrow-right ms-2"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} ManproApp. All Rights Reserved.
            </div>
        </footer>
    </div>
</body>

</html>