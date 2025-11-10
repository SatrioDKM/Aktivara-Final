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
        $statusText = '';
        $message = '';

        switch ($this->task->status) {
            case 'completed':
                $statusText = 'disetujui';
                $message = "Tugas '{$this->task->title}' yang Anda kerjakan telah disetujui.";
                break;
            case 'revised':
                $statusText = 'membutuhkan revisi';
                $message = "Tugas '{$this->task->title}' yang Anda kerjakan membutuhkan revisi. Silakan cek detail tugas.";
                break;
            case 'cancelled':
                $statusText = 'dibatalkan';
                $message = "Tugas '{$this->task->title}' yang Anda kerjakan telah dibatalkan.";
                break;
            case 'rejected': // 'rejected' is now a final state, not 'needs revision'
                $statusText = 'ditolak';
                $message = "Tugas '{$this->task->title}' yang Anda kerjakan telah ditolak.";
                break;
            default:
                $statusText = 'diperbarui';
                $message = "Tugas '{$this->task->title}' yang Anda kerjakan telah diperbarui.";
                break;
        }

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
        $content = '';

        switch ($this->task->status) {
            case 'completed':
                $content = "✅ *Tugas Disetujui*\n\nKerja bagus! Tugas *'{$this->task->title}'* yang Anda kerjakan telah disetujui oleh Leader.";
                break;
            case 'revised':
                $content = "🔄 *Tugas Membutuhkan Revisi*\n\nTugas *'{$this->task->title}'* yang Anda kerjakan membutuhkan revisi. Silakan cek detail tugas untuk perbaikan.";
                if ($this->task->review_notes) {
                    $content .= "\n\n*Catatan Review:* {$this->task->review_notes}";
                }
                break;
            case 'cancelled':
                $content = "❌ *Tugas Dibatalkan*\n\nTugas *'{$this->task->title}'* yang Anda kerjakan telah dibatalkan oleh Leader.";
                if ($this->task->review_notes) {
                    $content .= "\n\n*Catatan Pembatalan:* {$this->task->review_notes}";
                }
                break;
            case 'rejected':
                $content = "🚫 *Tugas Ditolak*\n\nTugas *'{$this->task->title}'* yang Anda kerjakan telah ditolak oleh Leader.";
                if ($this->task->review_notes) {
                    $content .= "\n\n*Catatan Penolakan:* {$this->task->review_notes}";
                }
                break;
            default:
                $content = "ℹ️ *Tugas Diperbarui*\n\nTugas *'{$this->task->title}'* yang Anda kerjakan telah diperbarui.";
                break;
        }

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content($content)
            ->button('Lihat Detail Tugas', $url);
    }
}
