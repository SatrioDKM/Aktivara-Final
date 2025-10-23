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
    }

    public function via(object $notifiable): array
    {
        return $notifiable->telegram_chat_id ? ['database', 'telegram'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        // --- PERBAIKAN LOGIKA DI SINI ---
        // Jika ada nama pelapor (dari tamu), gunakan itu.
        // Jika tidak, baru gunakan nama pembuat tugas (leader).
        $creatorName = $this->originatorName ?? $this->task->creator->name;

        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'creator_name' => $creatorName,
            'message' => "Tugas baru '{$this->task->title}' (dari {$creatorName}) telah tersedia di Papan Tugas.",
            'url' => route('tasks.available'),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('tasks.available');

        // --- PERBAIKAN LOGIKA DI SINI ---
        $creatorName = $this->originatorName ?? $this->task->creator->name;

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("🔔 *Tugas Baru Tersedia*\n\nSebuah tugas baru telah dibuat oleh *{$creatorName}*.\n\n*Judul:* {$this->task->title}\n\nSilakan cek papan tugas untuk mengambilnya.")
            ->button('Lihat Papan Tugas', $url);
    }
}
