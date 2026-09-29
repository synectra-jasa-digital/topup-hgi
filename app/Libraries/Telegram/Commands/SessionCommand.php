<?php

namespace App\Libraries\Telegram\Commands;

class SessionCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireRole($update, $session, ['owner'])) {
            return;
        }

        $chatId = $this->getChatId($update);

        // Jika dipicu via callback query (revoke)
        if (isset($update['callback_query'])) {
            $data = $update['callback_query']['data'] ?? '';
            if (str_starts_with($data, 'revoke_session:')) {
                $sessionId = (int) substr($data, strlen('revoke_session:'));
                $this->sessions->revokeById($sessionId);
                $this->bot->answerCallbackQuery($update['callback_query']['id'], 'Sesi telah dicabut.');
                $this->bot->sendMessage($chatId, "✅ Sesi ID {$sessionId} berhasil dicabut.");
            }
            return;
        }

        $list = $this->sessions->listActive();

        if (empty($list)) {
            $this->bot->sendMessage($chatId, "ℹ️ Tidak ada sesi bot aktif.");
            return;
        }

        $text = "🔑 <b>Sesi Bot Aktif</b> (" . count($list) . " sesi)\n\n";

        $buttons = [];
        foreach ($list as $s) {
            $roleLabel = strtoupper($s['admin_role']);
            $text .= "• <b>{$s['admin_name']}</b> ({$roleLabel})\n";
            $text .= "  Telegram ID: <code>{$s['telegram_user_id']}</code>\n";
            $text .= "  Terakhir aktif: <code>{$s['last_active_at']}</code>\n";
            $text .= "  Notif: " . ($s['notify'] ? 'ON' : 'OFF') . "\n\n";

            $buttons[] = [
                ['text' => "❌ Cabut sesi {$s['admin_name']}", 'callback_data' => "revoke_session:{$s['id']}"],
            ];
        }

        $markup = ['inline_keyboard' => $buttons];
        $this->bot->sendMessage($chatId, $text, $markup);
    }
}
