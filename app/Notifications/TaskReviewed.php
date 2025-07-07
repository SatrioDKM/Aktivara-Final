<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TaskReviewed extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->telegram_chat_id ? ['database', 'telegram'] : ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $statusText = $this->task->status === 'completed' ? 'disetujui' : 'ditolak dan perlu revisi';
        $message = "Tugas '{$this->task->title}' yang Anda kerjakan telah {$statusText}.";

        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => $message,
            'url' => route('tasks.show', $this->task->id),
        ];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        $url = route('tasks.show', $this->task->id);

        if ($this->task->status === 'completed') {
            $content = "✅ *Tugas Disetujui*\n\nKerja bagus! Tugas *'{$this->task->title}'* yang Anda kerjakan telah disetujui oleh Leader.";
        } else {
            $content = "⚠️ *Tugas Ditolak*\n\nTugas *'{$this->task->title}'* yang Anda kerjakan ditolak dan memerlukan revisi. Silakan cek detail tugas untuk perbaikan.";
        }

        return TelegramMessage::create()
            ->content($content)
            ->button('Lihat Detail Tugas', $url);
    }
}
