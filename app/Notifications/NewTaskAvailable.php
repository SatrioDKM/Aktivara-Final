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

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        // Kirim notifikasi ke database dan ke Telegram jika user punya chat_id
        return $notifiable->telegram_chat_id ? ['database', 'telegram'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'creator_name' => $this->task->creator->name,
            'message' => "Tugas baru '{$this->task->title}' telah tersedia di Papan Tugas.",
            'url' => route('tasks.available'),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('tasks.available');

        return TelegramMessage::create()
            // ->to($notifiable->telegram_chat_id)
            ->content("🔔 *Tugas Baru Tersedia*\n\nSebuah tugas baru telah dibuat oleh *{$this->task->creator->name}*.\n\n*Judul:* {$this->task->title}\n\nSilakan cek papan tugas untuk mengambilnya.")
            ->button('Lihat Papan Tugas', $url);
    }
}
