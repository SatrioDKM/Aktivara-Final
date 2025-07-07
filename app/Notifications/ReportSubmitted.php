<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class ReportSubmitted extends Notification
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
            'message' => "{$this->staff->name} telah mengirim laporan untuk tugas '{$this->task->title}' dan menunggu review Anda.",
            'url' => route('tasks.show', $this->task->id),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('tasks.review_list');

        return TelegramMessage::create()
            // ->to($notifiable->telegram_chat_id)
            ->content("📝 *Laporan Dikirim*\n\n*{$this->staff->name}* telah mengirim laporan untuk tugas *{$this->task->title}*.\n\nSilakan review laporan tersebut.")
            ->button('Lihat Daftar Review', $url);
    }
}
