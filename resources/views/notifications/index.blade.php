<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <i class="fas fa-bell text-indigo-600 dark:text-indigo-400"></i>
                {{ __('Notifikasi Anda') }}
            </h2>
            
            {{-- Tombol Aksi Global --}}
            <button onclick="markAllRead()" 
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition flex items-center gap-1 bg-white dark:bg-gray-800 py-2 px-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Container Utama --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700" x-data="notificationPage()">
                
                @if($notifications->isEmpty())
                    {{-- EMPTY STATE (Jika Kosong) --}}
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4">
                            <i class="far fa-bell-slash text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada notifikasi</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-xs mt-1">
                            Saat ini Anda belum memiliki notifikasi baru. Aktivitas terbaru akan muncul di sini.
                        </p>
                    </div>
                @else
                    {{-- LIST NOTIFIKASI --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($notifications as $notification)
                            @php
                                $isUnread = is_null($notification->read_at);
                                // Tentukan warna border & bg berdasarkan status
                                $bgClass = $isUnread ? 'bg-indigo-50/60 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50';
                                $borderClass = $isUnread ? 'border-l-4 border-indigo-500' : 'border-l-4 border-transparent';
                                $iconColor = $isUnread ? 'text-indigo-600 bg-indigo-100' : 'text-gray-400 bg-gray-100';
                            @endphp

                            <div class="group relative p-5 transition duration-200 ease-in-out {{ $bgClass }} {{ $borderClass }}" 
                                 id="notif-{{ $notification->id }}">
                                
                                <div class="flex items-start gap-4">
                                    {{-- Icon Kiri --}}
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $iconColor }} dark:bg-gray-700 dark:text-gray-300">
                                            <i class="fas fa-info-circle text-lg"></i>
                                        </div>
                                    </div>

                                    {{-- Konten Teks --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $notification->data['title'] ?? 'Pemberitahuan Sistem' }}
                                                @if($isUnread)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Baru
                                                    </span>
                                                @endif
                                            </p>
                                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                            {{ $notification->data['message'] ?? 'Tidak ada pesan detail.' }}
                                        </p>

                                        {{-- Actions Row --}}
                                        <div class="mt-3 flex items-center gap-4">
                                            @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" 
                                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 transition flex items-center gap-1">
                                                    Lihat Detail <i class="fas fa-arrow-right"></i>
                                                </a>
                                            @endif
                                            
                                            @if($isUnread)
                                                <button @click="markOne('{{ $notification->id }}')" 
                                                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                                    Tandai dibaca
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationPage', () => ({
                markOne(id) {
                    axios.post('{{ route("api.notifications.read.one") }}', { id: id })
                        .then(() => {
                            window.location.reload(); // Reload simpel biar UI update
                        })
                        .catch(err => console.error(err));
                }
            }));
        });

        // Global function for Header Button
        function markAllRead() {
            Swal.fire({
                title: 'Tandai semua dibaca?',
                text: "Semua notifikasi yang belum dibaca akan ditandai sebagai sudah dibaca.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Indigo-600
                cancelButtonColor: '#9ca3af', // Gray-400
                confirmButtonText: 'Ya, tandai semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show Loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post('{{ route("api.notifications.read.all") }}')
                        .then(response => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Semua notifikasi telah ditandai sebagai dibaca.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat memproses permintaan.',
                                'error'
                            );
                        });
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
