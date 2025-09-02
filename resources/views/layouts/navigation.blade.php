<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                    {{-- Menu untuk Staff --}}
                    @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02', 'PK02']))
                    <x-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">{{
                        __('Papan Tugas') }}</x-nav-link>
                    <x-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">{{
                        __('Riwayat Tugas Saya') }}</x-nav-link>
                    @endif

                    {{-- Menu Laporan Masuk untuk Leader ke Atas --}}
                    @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index')">{{
                        __('Laporan Masuk') }}</x-nav-link>
                    @endif

                    {{-- Menu Manajemen Tugas (TERMASUK SUPERADMIN) --}}
                    @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition {{ request()->routeIs(['tasks.create', 'tasks.monitoring', 'tasks.review_list']) ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <div>Manajemen Tugas</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('tasks.create')">{{ __('Buat Tugas Baru') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.monitoring')">{{ __('Monitoring Tugas Aktif') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.review_list')">{{ __('Review Laporan Staff') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    {{-- Menu untuk Admin & Manager (Data Master & Laporan) --}}
                    @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition {{ request()->routeIs('master.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <div>Data Master</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click.prevent.stop="open = !open"
                                        class="w-full text-left flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <span>Bangunan</span>
                                        <svg class="h-4 w-4 transform transition-transform"
                                            :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition class="pl-4 border-l ml-4">
                                        <x-dropdown-link :href="route('master.buildings.index')">{{ __('Gedung') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('master.floors.index')">{{ __('Lantai') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('master.rooms.index')">{{ __('Ruangan') }}
                                        </x-dropdown-link>
                                    </div>
                                </div>
                                <x-dropdown-link :href="route('master.task_types.index')">{{ __('Jenis Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.assets.index')">{{ __('Manajemen Aset') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.maintenances.index')">{{ __('Maintenance Aset') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition {{ request()->routeIs('history.tasks', 'export.index') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <div>Laporan</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('history.tasks')">{{ __('Riwayat & Laporan Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('export.index')">{{ __('Halaman Ekspor') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    {{-- Menu Superadmin --}}
                    @if(Auth::user()->role_id == 'SA00')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">{{
                        __('Manajemen Pengguna') }}</x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            {{-- ... (Bagian Kanan Navigasi: Notifikasi & Profil tidak berubah) ... --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Konten Notifikasi --}}
                <div x-data="notifications()" class="relative me-3">
                    <button @click="toggle"
                        class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="h-6 w-6"
                            stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg><span x-show="unreadCount > 0"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                            x-text="unreadCount"></span></button>
                    <div x-show="isOpen" @click.away="isOpen = false" x-transition
                        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-700 font-semibold border-b">Notifikasi</div>
                            <div class="max-h-80 overflow-y-auto"><template
                                    x-if="unread.length === 0 && read.length === 0">
                                    <p class="text-center text-gray-500 py-4 text-sm">Tidak ada notifikasi.</p>
                                </template><template x-for="notification in unread" :key="notification.id"><a
                                        :href="notification.data.url"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 bg-indigo-50 border-l-4 border-indigo-400">
                                        <p class="font-bold" x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-500"
                                            x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                    </a></template><template x-for="notification in read" :key="notification.id"><a
                                        :href="notification.data.url"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        <p x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-500"
                                            x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                    </a></template></div>
                            <div class="px-4 py-2 border-t" x-show="unreadCount > 0"><button @click="markAllAsRead"
                                    class="text-sm text-indigo-600 hover:underline w-full text-center">Tandai semua
                                    sudah dibaca</button></div>
                        </div>
                    </div>
                </div>
                {{-- Konten Dropdown Profil --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger"><button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg></div>
                        </button></x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"><svg
                        class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{
                __('Dashboard') }}</x-responsive-nav-link>

            @auth
            @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02', 'PK02']))
            <x-responsive-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">{{
                __('Papan Tugas') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">{{
                __('Riwayat Tugas Saya') }}</x-responsive-nav-link>
            @endif

            @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index')">{{
                __('Laporan Masuk') }}</x-responsive-nav-link>
            @endif

            @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
            <div class="pt-2 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Manajemen Tugas</div>
                </div>
                <div class="mt-1 space-y-1">
                    <x-responsive-nav-link :href="route('tasks.create')">{{ __('Buat Tugas Baru') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.monitoring')">{{ __('Monitoring Tugas Aktif') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.review_list')">{{ __('Review Laporan Staff') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endif

            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Data Master</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('master.buildings.index')">{{ __('Gedung') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master.floors.index')">{{ __('Lantai') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master.rooms.index')">{{ __('Ruangan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master.task_types.index')">{{ __('Jenis Tugas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master.assets.index')">{{ __('Manajemen Aset') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('master.maintenances.index')">{{ __('Maintenance Aset') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Laporan</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('history.tasks')">{{ __('Riwayat & Laporan Tugas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('export.index')">{{ __('Halaman Ekspor') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endif

            @if(Auth::user()->role_id == 'SA00')
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">{{
                __('Manajemen Pengguna') }}</x-responsive-nav-link>
            @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">@csrf<x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    function notifications() {
        return {
            isOpen: false, unread: [], read: [], unreadCount: 0,
            toggle() { this.isOpen = !this.isOpen; if (this.isOpen) { this.fetchNotifications(); } },
            fetchNotifications() {
                fetch('{{ route('notifications.index') }}', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => { this.unread = data.unread; this.read = data.read; this.unreadCount = data.unread.length; });
            },
            async markAllAsRead() {
                await fetch('/sanctum/csrf-cookie');
                fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': this.getCsrfToken() }
                }).then(() => { this.fetchNotifications(); });
            },
            getCsrfToken() { const c = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN=')); return c ? decodeURIComponent(c.split('=')[1]) : ''; },
            init() { this.fetchNotifications(); setInterval(() => this.fetchNotifications(), 60000); }
        }
    }
</script>