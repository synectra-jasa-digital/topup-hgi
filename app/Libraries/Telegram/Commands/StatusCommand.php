<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\StoreSettingModel;

class StatusCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $settings = new StoreSettingModel();

        $maintenance = $settings->getVal('maintenance', '0');
        $updatedBy   = $settings->getVal('maintenance_updated_by', '-');
        $updatedAt   = $settings->getVal('maintenance_updated_at', '-');
        $reason      = $settings->getVal('maintenance_reason', '');

        $statusText = $maintenance === '1' ? '🔴 <b>Toko TUTUP (Maintenance)</b>' : '🟢 <b>Toko BUKA</b>';

        $text = "🏪 <b>Status Toko Saat Ini</b>\n\n"
            . "Status: {$statusText}\n";

        if ($maintenance === '1' && $reason !== '') {
            $text .= "Alasan: " . htmlspecialchars($reason) . "\n";
        }

        $text .= "Diubah oleh: <b>" . htmlspecialchars($updatedBy) . "</b>\n";
        $text .= "Waktu: <code>{$updatedAt}</code>";

        $this->bot->sendMessage($chatId, $text);
    }
}
