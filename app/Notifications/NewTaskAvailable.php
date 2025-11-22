<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewTaskAvailable extends Notification
{
    use Queueable;

    protected $task;
    protected $originatorName; // Kolom baru untuk menyimpan nama pelapor

    /**
     * Create a new notification instance.
     *
     * @param Task $task
     * @param string|null $originatorName Nama pelapor (jika dari tamu)
     */
    public function __construct(Task $task, string $originatorName = null)
    {
        $this->task = $task;
        $this->originatorName = $originatorName;

        // --- PERBAIKAN: MENCEGAH N+1 QUERY ---
        // Memastikan relasi 'creator' sudah di-load sebelum diakses.
        // Ini akan mencegah query tambahan saat notifikasi dikirim ke banyak user.
        if (!$this->task->relationLoaded('creator')) {
            $this->task->load('creator');
        }
    }

    public function via(object $notifiable): array
    {
        $channels = ['database']; // Selalu kirim ke database
        
        // Hanya tambahkan Telegram jika:
        // 1. User punya telegram_chat_id
        // 2. Telegram bot token dikonfigurasi
        if ($notifiable->telegram_chat_id && config('telegram-notification.telegram_bot_token')) {
            $channels[] = 'telegram';
        }
        
        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        // Logika ini sekarang aman dari N+1
        $creatorName = $this->originatorName ?? $this->task->creator->name;

        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'creator_name' => $creatorName,
            'message' => "Tugas baru '{$this->task->title}' (dari {$creatorName}) telah tersedia.",
            // GANTI 'tasks.available' MENJADI 'tasks.show'
            'url' => route('tasks.show', $this->task->id),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        // GANTI DI SINI JUGA
        $url = route('tasks.show', $this->task->id);

        // Logika ini sekarang aman dari N+1
        $creatorName = $this->originatorName ?? $this->task->creator->name;

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("🔔 *Tugas Baru Tersedia*\n\nSebuah tugas baru telah dibuat oleh *{$creatorName}*.\n\n*Judul:* {$this->task->title}\n\nSilakan cek detail tugas.")
            ->button('Lihat Detail Tugas', $url); // Ubah label tombol
    }
}
