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
                    {{-- Menu Staff (Tidak Berubah) --}}
                    @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02', 'PK02']))
                    <x-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">
                        {{ __('Papan Tugas') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">
                        {{ __('Riwayat Tugas Saya') }}
                    </x-nav-link>
                    @endif

                    {{-- ====================================================== --}}
                    {{-- PENAMBAHAN MENU LAPORAN MASUK (DESKTOP) --}}
                    {{-- ====================================================== --}}
                    @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index')">
                        {{ __('Laporan Masuk') }}
                    </x-nav-link>
                    @endif
                    {{-- ====================================================== --}}

                    {{-- Dropdown Manajemen Tugas (Tidak Berubah) --}}
                    @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                                {{ request()->routeIs(['tasks.create', 'tasks.monitoring', 'tasks.review_list']) ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
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

                    {{-- Dropdown Data Master & Laporan (Tidak Berubah) --}}
                    @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition {{ request()->routeIs(['buildings.*', 'floors.*', 'rooms.*', 'task_types.*', 'assets.*', 'maintenances.*']) ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
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
                                        <x-dropdown-link :href="route('buildings.index')">{{ __('Gedung') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('floors.index')">{{ __('Lantai') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('rooms.index')">{{ __('Ruangan') }}
                                        </x-dropdown-link>
                                    </div>
                                </div>
                                <x-dropdown-link :href="route('task_types.index')">{{ __('Jenis Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('assets.index')">{{ __('Manajemen Aset') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('maintenances.index')">{{ __('Maintenance Aset') }}
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

                    {{-- Menu Superadmin (Tidak Berubah) --}}
                    @if(Auth::user()->role_id == 'SA00')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('Manajemen Pengguna') }}
                    </x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- ... Kode untuk Notifikasi dan Dropdown Profil ... --}}
            </div>

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

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{
                __('Dashboard') }}</x-responsive-nav-link>

            @auth
            {{-- Menu Responsive Staff (Tidak Berubah) --}}
            @if(in_array(Auth::user()->role_id, ['HK02', 'TK02', 'SC02', 'PK02']))
            <x-responsive-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">{{
                __('Papan Tugas') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">{{
                __('Riwayat Tugas Saya') }}</x-responsive-nav-link>
            @endif

            {{-- ====================================================== --}}
            {{-- PENAMBAHAN MENU LAPORAN MASUK (RESPONSIVE) --}}
            {{-- ====================================================== --}}
            @if(in_array(Auth::user()->role_id, ['HK01', 'TK01', 'SC01', 'PK01', 'MG00', 'SA00']))
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index')">{{
                __('Laporan Masuk') }}</x-responsive-nav-link>
            @endif
            {{-- ====================================================== --}}

            {{-- Menu Responsive Leader (Tidak Berubah) --}}
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

            {{-- Menu Responsive Admin & Manager (Tidak Berubah) --}}
            @if(in_array(Auth::user()->role_id, ['SA00', 'MG00']))
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Data Master</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('buildings.index')">{{ __('Gedung') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('floors.index')">{{ __('Lantai') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('rooms.index')">{{ __('Ruangan') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('task_types.index')">{{ __('Jenis Tugas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('assets.index')">{{ __('Manajemen Aset') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('maintenances.index')">{{ __('Maintenance Aset') }}
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

            {{-- Menu Responsive Superadmin (Tidak Berubah) --}}
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