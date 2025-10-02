<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                    {{-- Menu Staff --}}
                    @role('HK02', 'TK02', 'SC02', 'PK02', 'WH02')
                    <x-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">{{
                        __('Papan Tugas') }}</x-nav-link>
                    <x-nav-link :href="route('tasks.my_tasks')" :active="request()->routeIs('tasks.my_tasks')">
                        {{ __('Tugas Aktif Saya') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">{{
                        __('Riwayat Tugas') }}</x-nav-link>
                    @endrole

                    {{-- Menu Leader & Atasan --}}
                    @role('HK01', 'TK01', 'SC01', 'PK01', 'WH01', 'MG00', 'SA00')
                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">{{
                        __('Laporan Masuk') }}</x-nav-link>
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('tasks.create', 'tasks.monitoring', 'tasks.review_list') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                    <div>Manajemen Tugas</div>
                                    <div class="ms-1"><i class="fas fa-chevron-down h-4 w-4"></i></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('tasks.create')">{{ __('Buat Tugas Baru') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.monitoring')">{{ __('Monitoring Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.review_list')">{{ __('Review Laporan') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole

                    {{-- Menu Manajemen Data --}}
                    @role('SA00', 'MG00', 'WH01', 'WH02')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('master.*', 'stock.*', 'packing_lists.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                    <div>Manajemen Data</div>
                                    <div class="ms-1"><i class="fas fa-chevron-down h-4 w-4"></i></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @role('SA00', 'MG00')
                                <div class="px-4 py-2 text-xs text-gray-400">Master Lokasi & Aset</div>
                                <x-dropdown-link :href="route('master.buildings.index')" class="ps-6">{{ __('Gedung') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.floors.index')" class="ps-6">{{ __('Lantai') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.rooms.index')" class="ps-6">{{ __('Ruangan') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.assets.index')" class="ps-6">{{ __('Aset') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <x-dropdown-link :href="route('master.task_types.index')">{{ __('Jenis Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('master.maintenances.index')">{{ __('Jadwal Maintenance')
                                    }}</x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                @endrole
                                <div class="px-4 py-2 text-xs text-gray-400">Gudang</div>
                                <x-dropdown-link :href="route('stock.index')" class="ps-6">{{ __('Manajemen Stok') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('packing_lists.index')" class="ps-6">{{ __('Barang
                                    Keluar') }}</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole

                    {{-- Menu Laporan --}}
                    @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('history.tasks', 'export.index') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                    <div>Laporan</div>
                                    <div class="ms-1"><i class="fas fa-chevron-down h-4 w-4"></i></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('history.tasks')">{{ __('Riwayat Tugas') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tasks.completed_history')">
                                    {{ __('Riwayat Tugas Selesai') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <x-dropdown-link :href="route('export.index')">{{ __('Ekspor Data') }}</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endrole

                    {{-- Menu Superadmin --}}
                    @role('SA00')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('Manajemen
                        Pengguna') }}</x-nav-link>
                    @endrole
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <div x-data="notifications()" class="relative me-3">
                    <button @click="toggle" class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-bell h-6 w-6"></i>
                        <span x-show="unreadCount > 0"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                            x-text="unreadCount" style="display: none;"></span>
                    </button>
                    <div x-show="isOpen" @click.away="isOpen = false" x-transition
                        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                        style="display: none;">
                        <div class="py-1">
                            <div
                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 font-semibold border-b dark:border-gray-600">
                                Notifikasi</div>
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="!unread.length && !read.length">
                                    <p class="text-center text-gray-500 dark:text-gray-400 py-4 text-sm">Tidak ada
                                        notifikasi.</p>
                                </template>
                                <template x-for="notification in unread" :key="notification.id">
                                    <a :href="notification.data.url" @click.prevent="markAsRead(notification.id)"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 bg-indigo-50 dark:bg-gray-800 border-l-4 border-indigo-400">
                                        <p class="font-bold" x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                            x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                    </a>
                                </template>
                                <template x-for="notification in read" :key="notification.id">
                                    <a :href="notification.data.url"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                                        <p x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                            x-text="new Date(notification.created_at).toLocaleString('id-ID')"></p>
                                    </a>
                                </template>
                            </div>
                            <div class="px-4 py-2 border-t dark:border-gray-600" x-show="unreadCount > 0"
                                style="display: none;">
                                <button @click="markAllAsRead"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline w-full text-center">Tandai
                                    semua sudah dibaca</button>
                            </div>
                        </div>
                    </div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><i class="fas fa-chevron-down h-4 w-4"></i></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
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

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-bars h-6 w-6" :class="{'hidden': open, 'inline-flex': ! open }"></i>
                    <i class="fas fa-times h-6 w-6" :class="{'hidden': ! open, 'inline-flex': open }"></i>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @auth
            {{-- Responsive Menu Staff --}}
            @role('HK02', 'TK02', 'SC02', 'PK02', 'WH02')
            <x-responsive-nav-link :href="route('tasks.available')" :active="request()->routeIs('tasks.available')">{{
                __('Papan Tugas') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.my_tasks')" :active="request()->routeIs('tasks.my_tasks')">
                {{ __('Tugas Aktif Saya') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.my_history')" :active="request()->routeIs('tasks.my_history')">{{
                __('Riwayat Tugas') }}</x-responsive-nav-link>
            @endrole

            {{-- Responsive Menu Leader & Atasan --}}
            @role('HK01', 'TK01', 'SC01', 'PK01', 'WH01', 'MG00', 'SA00')
            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">{{
                __('Laporan Masuk') }}</x-responsive-nav-link>
            <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">Manajemen Tugas</div>
                </div>
                <div class="mt-1 space-y-1">
                    <x-responsive-nav-link :href="route('tasks.create')">{{ __('Buat Tugas Baru') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.monitoring')">{{ __('Monitoring Tugas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.review_list')">{{ __('Review Laporan') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endrole

            {{-- Responsive Menu Manajemen Data --}}
            @role('SA00', 'MG00', 'WH01', 'WH02')
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">Manajemen Data</div>
                </div>
                <div class="mt-3 space-y-1">
                    @role('SA00', 'MG00')
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
                    <x-responsive-nav-link :href="route('master.maintenances.index')">{{ __('Jadwal Maintenance') }}
                    </x-responsive-nav-link>
                    @endrole
                    <x-responsive-nav-link :href="route('stock.index')">{{ __('Manajemen Stok') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('packing_lists.index')">{{ __('Barang Keluar') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endrole

            {{-- Responsive Menu Laporan --}}
            @role('SA00', 'MG00', 'HK01', 'TK01', 'SC01', 'PK01')
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">Laporan</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('history.tasks')">
                        {{ __('Riwayat Tugas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.completed_history')">
                        {{ __('Riwayat Tugas Selesai') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('export.index')">
                        {{ __('Ekspor Data') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endrole

            {{-- Responsive Menu Superadmin --}}
            @role('SA00')
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('Manajemen
                Pengguna') }}</x-responsive-nav-link>
            @endrole
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
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