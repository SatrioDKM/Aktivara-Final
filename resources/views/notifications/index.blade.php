<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifikasi Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Notifikasi Belum Dibaca</h3>
                    @forelse ($unreadNotifications as $notification)
                        <div class="mb-4 p-4 border rounded-lg {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700' : 'bg-blue-50 dark:bg-blue-900' }}">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            <p class="mt-1 text-gray-800 dark:text-gray-200">
                                @if(isset($notification->data['task_id']))
                                    <a href="{{ route('tasks.check', $notification->data['task_id']) }}" class="hover:underline text-blue-600 dark:text-blue-400">
                                        {{ $notification->data['message'] ?? 'Pesan notifikasi tidak tersedia.' }}
                                    </a>
                                @else
                                    {{ $notification->data['message'] ?? 'Pesan notifikasi tidak tersedia.' }}
                                @endif
                            </p>
                            @if (!$notification->read_at)
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 text-sm">Tandai Sudah Dibaca</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p>Tidak ada notifikasi belum dibaca.</p>
                    @endforelse
                    <div class="mt-4">
                        {{ $unreadNotifications->links() }}
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-8 mb-4">Notifikasi Sudah Dibaca</h3>
                    @forelse ($readNotifications as $notification)
                        <div class="mb-4 p-4 border rounded-lg bg-gray-100 dark:bg-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            <p class="mt-1 text-gray-800 dark:text-gray-200">
                                @if(isset($notification->data['task_id']))
                                    <a href="{{ route('tasks.check', $notification->data['task_id']) }}" class="hover:underline text-blue-600 dark:text-blue-400">
                                        {{ $notification->data['message'] ?? 'Pesan notifikasi tidak tersedia.' }}
                                    </a>
                                @else
                                    {{ $notification->data['message'] ?? 'Pesan notifikasi tidak tersedia.' }}
                                @endif
                            </p>
                        </div>
                    @empty
                        <p>Tidak ada notifikasi sudah dibaca.</p>
                    @endforelse
                    <div class="mt-4">
                        {{ $readNotifications->links() }}
                    </div>

                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mt-6">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Tandai Semua Sudah Dibaca
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
