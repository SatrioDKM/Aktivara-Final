<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center"> {{-- Wadah Utama Logo --}}
                    <div class="ms-1">
                        <div class="shrink-0 flex items-center gap-4">
                            
                            {{-- GRUP 1: INSTITUSI (Yayasan & Kampus) --}}
                            <div class="flex items-center gap-2">
                                
                                {{-- 1. LOGO YAYASAN (Induk) --}}
                                <img src="{{ asset('logo/sasmita.png') }}" 
                                     alt="Logo Yayasan" 
                                     class="block h-10 w-auto object-contain hover:scale-105 transition" 
                                     title="Yayasan" />

                                {{-- 2. LOGO KAMPUS (Institusi) --}}
                                <img src="{{ asset('logo/UNPAM_logo1.png') }}" 
                                     alt="Logo Kampus" 
                                     class="block h-10 w-auto object-contain hover:scale-105 transition" 
                                     title="Kampus" />
                                    
                            </div>

                            {{-- PEMISAH (Divider Vertikal) --}}
                            <div class="h-8 w-[1.5px] bg-gray-300 dark:bg-gray-600 rounded-full"></div>

                            {{-- GRUP 2: IDENTITAS APLIKASI (Logo Kamu) --}}
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                                
                                {{-- 3. LOGO APLIKASI --}}
                                <img src="{{ asset('logo/logoRounded.png') }}" 
                                     alt="Aktivara" 
                                     class="block h-9 w-auto object-contain group-hover:rotate-6 transition" 
                                     title="Aktivara App" />
                                
                                {{-- Teks Nama Aplikasi (Muncul di Laptop) --}}
                                <span class="hidden lg:block font-bold text-lg text-gray-800 dark:text-gray-200 tracking-tight leading-none">
                                    Aktivara
                                </span>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-tachometer-alt w-4 text-center me-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                    {{-- Dropdown Keluhan (Leader+) --}}
                    @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'WH01')
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                            {{ request()->routeIs('complaints.*')
                                                ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <i class="fas fa-exclamation-triangle w-4 text-center me-2"></i>
                                    <div>Keluhan</div>
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
                                <x-dropdown-link :href="route('complaints.create')"
                                    :active="request()->routeIs('complaints.create')">
                                    <i class="fas fa-plus-circle w-4 text-center me-2"></i>
                                    {{ __('Lapor Keluhan Baru') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('complaints.index')"
                                    :active="request()->routeIs('complaints.index', 'complaints.show')">
                                    <i class="fas fa-list-alt w-4 text-center me-2"></i>
                                    {{ __('Daftar Keluhan') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole
                    {{-- Dropdown Keluhan (Leader+) --}}

                    @role('SA00', 'MG00', 'HK01', 'HK02', 'TK01', 'TK02', 'SC01', 'SC02', 'PK01', 'PK02')
                    {{-- Dropdown Alur Kerja Tugas (Semua user internal) --}}
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                        {{ request()->routeIs('tasks.*', 'history.tasks')
                                            ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <i class="fas fa-tasks w-4 text-center me-2"></i>
                                    <div>Alur Kerja Tugas</div>
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
                                {{-- Menu Leader --}}
                                @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'WH01')
                                <x-dropdown-link :href="route('tasks.create')"
                                    :active="request()->routeIs('tasks.create')">
                                    <i class="fas fa-plus-circle w-4 text-center me-2"></i>
                                    {{ __('Buat Tugas Baru') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.review_list')"
                                    :active="request()->routeIs('tasks.review_list')">
                                    <i class="fas fa-check-double w-4 text-center me-2"></i>
                                    {{ __('Review Laporan') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.monitoring')"
                                    :active="request()->routeIs('tasks.monitoring')">
                                    <i class="fas fa-tv w-4 text-center me-2"></i>
                                    {{ __('Monitoring Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('history.tasks')"
                                    :active="request()->routeIs('history.tasks')">
                                    <i class="fas fa-archive w-4 text-center me-2"></i>
                                    {{ __('Riwayat Semua Tugas') }}
                                </x-dropdown-link>
                                @endrole

                                {{-- Menu Staff --}}
                                @role('HK02', 'TK02', 'SC02', 'PK02', 'WH02')
                                {{-- Separator jika user adalah staff DAN leader (cth: SA00) --}}
                                @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'WH01')
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                @endrole
                                <x-dropdown-link :href="route('tasks.available')"
                                    :active="request()->routeIs('tasks.available')">
                                    <i class="fas fa-clipboard-list w-4 text-center me-2"></i>
                                    {{ __('Papan Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.my_tasks')"
                                    :active="request()->routeIs('tasks.my_tasks')">
                                    <i class="fas fa-bolt w-4 text-center me-2"></i>
                                    {{ __('Tugas Aktif Saya') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.my_history')"
                                    :active="request()->routeIs('tasks.my_history')">
                                    <i class="fas fa-history w-4 text-center me-2"></i>
                                    {{ __('Riwayat Tugas Saya') }}
                                </x-dropdown-link>
                                @endrole
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole
                    
                    {{-- Dropdown Gudang (Gudang+) --}}
                    @role('SA00', 'MG00', 'WH01', 'WH02')
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                            {{ request()->routeIs('stock.*', 'packing_lists.*', 'asset_history.*')
                                                ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <i class="fas fa-boxes w-4 text-center me-2"></i>
                                    <div>Gudang & Aset</div>
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
                                <x-dropdown-link :href="route('stock.index')"
                                    :active="request()->routeIs('stock.index')">
                                    <i class="fas fa-box-open w-4 text-center me-2"></i>
                                    {{ __('Stok Gudang') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('packing_lists.index')"
                                    :active="request()->routeIs('packing_lists.index')">
                                    <i class="fas fa-dolly w-4 text-center me-2"></i>
                                    {{ __('Packing List') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('asset_history.index')"
                                    :active="request()->routeIs('asset_history.index')">
                                    <i class="fas fa-history w-4 text-center me-2"></i>
                                    {{ __('Riwayat Aset') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole

                    {{-- Dropdown Administrasi (Admin/SA00) --}}
                    @role('SA00', 'MG00', 'WH01' , 'WH02')
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="w-60">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                            {{ request()->routeIs('master.*', 'users.*', 'export.*')
                                                ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700' }}">
                                    <i class="fas fa-cogs w-4 text-center me-2"></i>
                                    <div>Master Data</div>
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
                                @role('SA00')
                                <x-dropdown-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    <i class="fas fa-users-cog w-4 text-center me-2"></i>
                                    {{ __('Manajemen Pengguna') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                @endrole
                                
                                <x-dropdown-link :href="route('master.assets.index')"
                                    :active="request()->routeIs('master.assets.*')">
                                    <i class="fas fa-cube w-4 text-center me-2"></i>
                                    {{ __('Master Aset') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('master.asset_categories.index')"
                                    :active="request()->routeIs('master.asset_categories.*')">
                                    <i class="fas fa-tags w-4 text-center me-2"></i>
                                    {{ __('Master Kategori Aset') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('master.maintenances.index')"
                                    :active="request()->routeIs('master.maintenances.*')">
                                    <i class="fas fa-wrench w-4 text-center me-2"></i>
                                    {{ __('Master Maintenance') }}
                                </x-dropdown-link>
                                
                                @role('SA00', 'MG00')
                                <x-dropdown-link :href="route('master.buildings.index')"
                                    :active="request()->routeIs('master.buildings.*')">
                                    <i class="fas fa-building w-4 text-center me-2"></i>
                                    {{ __('Master Gedung') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('master.floors.index')"
                                    :active="request()->routeIs('master.floors.*')">
                                    <i class="fas fa-layer-group w-4 text-center me-2"></i>
                                    {{ __('Master Lantai') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('master.rooms.index')"
                                    :active="request()->routeIs('master.rooms.*')">
                                    <i class="fas fa-door-open w-4 text-center me-2"></i>
                                    {{ __('Master Ruangan') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('master.task_types.index')"
                                    :active="request()->routeIs('master.task_types.*')">
                                    <i class="fas fa-list-ul w-4 text-center me-2"></i>
                                    {{ __('Master Jenis Tugas') }}
                                </x-dropdown-link>
                                @endrole

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <x-dropdown-link :href="route('export.index')"
                                    :active="request()->routeIs('export.index')">
                                    <i class="fas fa-file-export w-4 text-center me-2"></i>
                                    {{ __('Ekspor Data') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole

                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Notification Dropdown --}}
                <div x-data="notifications()" x-init="init()" class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button @click="toggle()"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <i class="fas fa-bell w-4 text-center"></i>
                                <span x-show="unreadCount > 0"
                                    class="ms-1 px-2 py-1 text-sm font-bold leading-none text-red-100 bg-red-600 rounded-full"
                                    x-text="unreadCount"></span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-sm text-gray-400">
                                Notifikasi (<span x-text="unreadCount"></span> belum dibaca)
                            </div>

                            <template x-for="notification in unread" :key="notification.id">
                                <a :href="notification.data.url ? notification.data.url : '#'" @click.prevent="markAsRead(notification.id)"
                                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
                                    <p class="font-bold" x-text="notification.data.message"></p>
                                    <p class="text-sm text-gray-500" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                </a>
                            </template>

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <x-dropdown-link :href="route('notifications.index')">
                                {{ __('Lihat Semua Notifikasi') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-600" x-show="unreadCount > 0"></div>

                            <button @click="markAllAsRead()" x-show="unreadCount > 0"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
                                Tandai semua sudah dibaca
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
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
                            <i class="fas fa-user-circle w-4 text-center me-2"></i>
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt w-4 text-center me-2"></i>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-tachometer-alt w-4 text-center me-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        @auth
        {{-- Responsive Menu Keluhan --}}
        @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01', 'WH01')
        <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">Menu Keluhan</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('complaints.create')"
                    :active="request()->routeIs('complaints.create')">
                    <i class="fas fa-plus-circle w-4 text-center me-2"></i>
                    {{ __('Lapor Keluhan Baru') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('complaints.index')"
                    :active="request()->routeIs('complaints.index', 'complaints.show')">
                    <i class="fas fa-list-alt w-4 text-center me-2"></i>
                    {{ __('Daftar Keluhan') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endrole

        {{-- Responsive Menu Alur Kerja Tugas --}}
        <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                @role('SA00', 'MG00', 'HK', 'TK', 'SC', 'PK')
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">Alur Kerja Tugas</div>
                @endrole
            </div>
            <div class="mt-3 space-y-1">
                {{-- Menu Leader --}}
                @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01')
                <x-responsive-nav-link :href="route('tasks.create')" :active="request()->routeIs('tasks.create')">
                    <i class="fas fa-plus-circle w-4 text-center me-2"></i>
                    {{ __('Buat Tugas Baru') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tasks.review_list')"
                    :active="request()->routeIs('tasks.review_list')">
                    <i class="fas fa-check-double w-4 text-center me-2"></i>
                    {{ __('Review Laporan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tasks.monitoring')"
                    :active="request()->routeIs('tasks.monitoring')">
                    <i class="fas fa-tv w-4 text-center me-2"></i>
                    {{ __('Monitoring Tugas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('history.tasks')" :active="request()->routeIs('history.tasks')">
                    <i class="fas fa-archive w-4 text-center me-2"></i>
                    {{ __('Riwayat Semua Tugas') }}
                </x-responsive-nav-link>
                @endrole
                {{-- Menu Staff --}}
                @role('HK02', 'TK02', 'SC02', 'PK02', 'WH02')
                @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01')
                <div class="my-2 border-t border-gray-200 dark:border-gray-600"></div>
                @endrole
                <x-responsive-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">
                    <i class="fas fa-clipboard-list w-4 text-center me-2"></i>
                    {{ __('Papan Tugas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tasks.my_tasks')" :active="request()->routeIs('tasks.my_tasks')">
                    <i class="fas fa-bolt w-4 text-center me-2"></i>
                    {{ __('Tugas Aktif Saya') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tasks.my_history')"
                    :active="request()->routeIs('tasks.my_history')">
                    <i class="fas fa-history w-4 text-center me-2"></i>
                    {{ __('Riwayat Tugas Saya') }}
                </x-responsive-nav-link>
                @endrole
            </div>
        </div>

        {{-- Responsive Menu Gudang --}}
        @role('SA00', 'MG00', 'WH01', 'WH02')
        <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">Menu Gudang</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.index')">
                    <i class="fas fa-box-open w-4 text-center me-2"></i>
                    {{ __('Stok Gudang') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('packing_lists.index')"
                    :active="request()->routeIs('packing_lists.index')">
                    <i class="fas fa-dolly w-4 text-center me-2"></i>
                    {{ __('Packing List') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('asset_history.index')"
                    :active="request()->routeIs('asset_history.index')">
                    <i class="fas fa-history w-4 text-center me-2"></i>
                    {{ __('Riwayat Aset') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endrole

        {{-- Responsive Menu Administrasi --}}
        @role('SA00', 'MG00', 'WH01')
        <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">Master Data</div>
            </div>
            <div class="mt-3 space-y-1">
                @role('SA00')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <i class="fas fa-users-cog w-4 text-center me-2"></i>
                    {{ __('Manajemen Pengguna') }}
                </x-responsive-nav-link>
                @endrole
                <x-responsive-nav-link :href="route('master.assets.index')"
                    :active="request()->routeIs('master.assets.*')">
                    {{ __('Master Aset') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master.asset_categories.index')"
                    :active="request()->routeIs('master.asset_categories.*')">
                    {{ __('Master Kategori Aset') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master.maintenances.index')"
                    :active="request()->routeIs('master.maintenances.*')">
                    {{ __('Master Maintenance') }}
                </x-responsive-nav-link>
                @role('SA00', 'MG00')
                <x-responsive-nav-link :href="route('master.buildings.index')"
                    :active="request()->routeIs('master.buildings.*')">
                    {{ __('Master Gedung') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master.floors.index')"
                    :active="request()->routeIs('master.floors.*')">
                    {{ __('Master Lantai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master.rooms.index')"
                    :active="request()->routeIs('master.rooms.*')">
                    {{ __('Master Ruangan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master.task_types.index')"
                    :active="request()->routeIs('master.task_types.*')">
                    {{ __('Master Jenis Tugas') }}
                </x-responsive-nav-link>
                @endrole

                <div class="my-2 border-t border-gray-200 dark:border-gray-600"></div>
                <x-responsive-nav-link :href="route('export.index')" :active="request()->routeIs('export.index')">
                    <i class="fas fa-file-export w-4 text-center me-2"></i>
                    {{ __('Ekspor Data') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endrole
        @endauth

        {{-- Responsive Notification Link --}}
        <div x-data="notifications()" x-init="init()" class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    Notifikasi
                    <span x-show="unreadCount > 0"
                        class="ms-1 px-2 py-1 text-sm font-bold leading-none text-red-100 bg-red-600 rounded-full"
                        x-text="unreadCount"></span>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <template x-for="notification in unread" :key="notification.id">
                    <a :href="notification.data.url ? notification.data.url : '#'" @click.prevent="markAsRead(notification.id)"
                        class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                        <p class="font-bold" x-text="notification.data.message"></p>
                        <p class="text-sm text-gray-500" x-text="new Date(notification.created_at).toLocaleString()"></p>
                    </a>
                </template>
                <x-responsive-nav-link :href="route('notifications.index')">
                    {{ __('Lihat Semua Notifikasi') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user-circle w-4 text-center me-2"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt w-4 text-center me-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>