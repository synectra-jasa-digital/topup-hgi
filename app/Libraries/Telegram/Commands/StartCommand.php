<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\StoreSettingModel;
use App\Models\TelegramSessionModel;

class StartCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        $chatId = $this->getChatId($update);

        $storeName = (new StoreSettingModel())->getVal('store_name', 'Ayong Store');

        $greeting = "👋 <b>Selamat datang di bot {$storeName}!</b>\n\n"
            . "Bot ini membantu admin mengelola toko dari Telegram.\n\n";

        if ($session !== null) {
            $greeting .= "Anda sudah login. Ketik /help untuk melihat command.";
        } else {
            $greeting .= "Ketik /login untuk masuk, atau /help untuk informasi lebih lanjut.";
        }

        $this->bot->sendMessage($chatId, $greeting);
    }
}
