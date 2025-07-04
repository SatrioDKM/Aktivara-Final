<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TaskClaimed extends Notification
{
    use Queueable;

    protected $task;
    protected $staff;

    public function __construct(Task $task, User $staff)
    {
        $this->task = $task;
        $this->staff = $staff;
    }

    public function via(object $notifiable): array
    {
        return $notifiable->telegram_chat_id ? ['database', 'telegram'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'staff_name' => $this->staff->name,
            'message' => "Tugas '{$this->task->title}' telah diambil oleh {$this->staff->name}.",
            'url' => route('tasks.show', $this->task->id),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('tasks.show', $this->task->id);

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("✅ *Tugas Diambil*\n\nTugas *{$this->task->title}* yang Anda buat telah diambil oleh *{$this->staff->name}*.")
            ->button('Lihat Detail Tugas', $url);
    }
}
