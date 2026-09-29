<?php

namespace App\Libraries\Telegram\Commands;

class NotifCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $userId = $this->getUserId($update);
        $text   = strtolower(trim($this->getText($update)));

        $parts = explode(' ', $text);
        $opt   = count($parts) > 1 ? $parts[1] : '';

        if (! in_array($opt, ['on', 'off'], true)) {
            $currentStatus = ($session['notify'] ?? 1) ? 'ON' : 'OFF';
            $this->bot->sendMessage($chatId, "Format: /notif on|off\nStatus notifikasi Anda saat ini: <b>{$currentStatus}</b>");
            return;
        }

        $notifyVal = $opt === 'on' ? 1 : 0;
        $this->sessions->where('telegram_user_id', $userId)->set(['notify' => $notifyVal])->update();

        $this->bot->sendMessage($chatId, "✅ Notifikasi pesanan baru berhasil diubah ke: <b>" . strtoupper($opt) . "</b>");
    }
}
