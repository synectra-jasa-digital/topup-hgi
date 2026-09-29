<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\StoreSettingModel;

class CloseCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $text   = $this->getText($update);

        // Memeriksa konfirmasi callback
        if (isset($update['callback_query'])) {
            $data = $update['callback_query']['data'] ?? '';
            if (str_starts_with($data, 'close_confirm:')) {
                $reason = substr($data, strlen('close_confirm:'));
                $this->executeClose($chatId, $session, $reason, $update['callback_query']['id']);
            }
            return;
        }

        // Parse alasan dari command: /tutup Alasan toko tutup
        $parts  = explode(' ', trim($text), 2);
        $reason = count($parts) > 1 ? trim($parts[1]) : 'Toko tutup';

        // Tampilkan konfirmasi tombol sebelum menutup
        $markup = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Ya, Tutup Toko', 'callback_data' => 'close_confirm:' . substr($reason, 0, 40)],
                    ['text' => '❌ Batal', 'callback_data' => 'close_cancel'],
                ],
            ],
        ];

        $this->bot->sendMessage(
            $chatId,
            "⚠️ <b>Konfirmasi Tutup Toko</b>\n\n"
            . "Anda yakin ingin menutup toko?\n"
            . "Alasan: <i>" . htmlspecialchars($reason) . "</i>",
            $markup
        );
    }

    private function executeClose(int $chatId, array $session, string $reason, string $callbackQueryId): void
    {
        $settings = new StoreSettingModel();
        $now      = date('Y-m-d H:i:s');
        $adminName = $session['admin_name'] ?? 'Admin';

        $settings->setVal('maintenance', '1');
        $settings->setVal('maintenance_reason', $reason);
        $settings->setVal('maintenance_updated_by', $adminName);
        $settings->setVal('maintenance_updated_at', $now);

        $this->logActivity((int) $session['admin_id'], 'telegram_tutup_toko', "Menutup toko dengan alasan: {$reason}");

        $this->bot->answerCallbackQuery($callbackQueryId, 'Toko berhasil ditutup');

        $msg = "🔴 <b>Toko Telah DITUTUP</b>\n\n"
            . "Oleh: <b>" . htmlspecialchars($adminName) . "</b>\n"
            . "Alasan: <i>" . htmlspecialchars($reason) . "</i>\n"
            . "Waktu: <code>{$now}</code>";

        $this->bot->sendMessage($chatId, $msg);

        // Broadcast ke semua admin/owner lain yang login
        $activeSessions = $this->sessions->getActiveSessions();
        foreach ($activeSessions as $s) {
            if ((int) $s['chat_id'] !== $chatId) {
                $this->bot->sendMessage((int) $s['chat_id'], $msg);
            }
        }
    }
}
