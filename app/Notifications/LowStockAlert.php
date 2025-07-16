<?php

namespace App\Notifications;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function via(object $notifiable): array
    {
        return $notifiable->telegram_chat_id ? ['database', 'telegram'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name_asset,
            'message' => "Stok untuk aset '{$this->asset->name_asset}' menipis. Stok saat ini: {$this->asset->current_stock}.",
            'url' => route('assets.index'),
        ];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('assets.index');

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content("⚠️ *Peringatan Stok Menipis*\n\nAset: *{$this->asset->name_asset}*\nStok Saat Ini: *{$this->asset->current_stock}*\nStok Minimum: *{$this->asset->minimum_stock}*\n\nMohon segera lakukan pengadaan.")
            ->button('Lihat Manajemen Aset', $url);
    }
}
