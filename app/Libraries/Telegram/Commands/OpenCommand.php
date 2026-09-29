<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\StoreSettingModel;

class OpenCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId   = $this->getChatId($update);
        $settings = new StoreSettingModel();
        $now      = date('Y-m-d H:i:s');
        $adminName = $session['admin_name'] ?? 'Admin';

        $settings->setVal('maintenance', '0');
        $settings->setVal('maintenance_updated_by', $adminName);
        $settings->setVal('maintenance_updated_at', $now);

        $this->logActivity((int) $session['admin_id'], 'telegram_buka_toko', 'Membuka toko');

        $msg = "🟢 <b>Toko Telah DIBUKA</b>\n\n"
            . "Oleh: <b>" . htmlspecialchars($adminName) . "</b>\n"
            . "Waktu: <code>{$now}</code>";

        $this->bot->sendMessage($chatId, $msg);

        // Broadcast ke admin lain yang sedang login
        $activeSessions = $this->sessions->getActiveSessions();
        foreach ($activeSessions as $s) {
            if ((int) $s['chat_id'] !== $chatId) {
                $this->bot->sendMessage((int) $s['chat_id'], $msg);
            }
        }
    }
}
