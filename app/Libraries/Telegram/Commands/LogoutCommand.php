<?php

namespace App\Libraries\Telegram\Commands;

class LogoutCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        $chatId = $this->getChatId($update);
        $userId = $this->getUserId($update);

        if ($session === null) {
            $this->bot->sendMessage($chatId, 'Anda belum login.');
            return;
        }

        $this->sessions->deleteByUserId($userId);
        $this->bot->sendMessage($chatId, '👋 Logout berhasil. Sampai jumpa!');
    }
}
