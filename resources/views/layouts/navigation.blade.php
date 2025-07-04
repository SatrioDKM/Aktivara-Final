<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Menu untuk Semua Role -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                    <!-- Menu untuk Staff (xx02) -->
                    @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02']))
                    {{-- <x-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">
                        {{ __('Papan Tugas') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tasks.my')" :active="request()->routeIs('tasks.my')">
                        {{ __('Tugas Saya') }}
                    </x-nav-link> --}}
                    @endif

                    <!-- Menu untuk Leader (xx01) -->
                    @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01']))
                    {{-- <x-nav-link :href="route('tasks.create')" :active="request()->routeIs('tasks.create')">
                        {{ __('Buat Tugas') }}
                    </x-nav-link> --}}
                    @endif

                    <!-- Menu Dropdown untuk Admin & Manager -->
                    @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>Data Master</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('buildings.index')">
                                    {{ __('Manajemen Gedung') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('floors.index')">
                                    {{ __('Manajemen Lantai') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('rooms.index')">
                                    {{ __('Manajemen Ruangan') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    <!-- Menu khusus Superadmin -->
                    @if(Auth::user()->role_id == 'SA00')
                    {{-- <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('Manajemen Pengguna') }}
                    </x-nav-link> --}}
                    @endif

                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown (Profil & Logout) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @auth
            <!-- Menu Responsive untuk Staff -->
            @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02']))
            {{-- <x-responsive-nav-link :href="route('tasks.available')"
                :active="request()->routeIs('tasks.available')">
                {{ __('Papan Tugas') }}
            </x-responsive-nav-link> --}}
            @endif

            <!-- Menu Responsive untuk Admin & Manager -->
            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Data Master</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('buildings.index')">
                        {{ __('Manajemen Gedung') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('floors.index')">
                        {{ __('Manajemen Lantai') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('rooms.index')">
                        {{ __('Manajemen Ruangan') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endif

            <!-- Menu Responsive khusus Superadmin -->
            @if(Auth::user()->role_id == 'SA00')
            {{-- <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                {{ __('Manajemen Pengguna') }}
            </x-responsive-nav-link> --}}
            @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>