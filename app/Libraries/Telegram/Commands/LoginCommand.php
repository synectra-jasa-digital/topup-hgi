<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\AdminModel;
use App\Models\TelegramLoginAttemptModel;

class LoginCommand extends BaseCommand
{
    // State machine key di CI cache
    private const STATE_PREFIX = 'tg_login_state_';

    public function handle(array $update, ?array $session): void
    {
        if ($this->denyGroupChat($update)) {
            return;
        }

        $chatId  = $this->getChatId($update);
        $userId  = $this->getUserId($update);
        $text    = $this->getText($update);
        $command = trim($text);

        $attempts = new TelegramLoginAttemptModel();
        $cache    = \Config\Services::cache();

        if ($attempts->isLockedOut($userId)) {
            $this->bot->sendMessage($chatId, '❌ Terlalu banyak percobaan login gagal. Coba lagi 15 menit kemudian.');
            return;
        }

        $stateKey  = self::STATE_PREFIX . $userId;
        $state     = $cache->get($stateKey);

        // Sudah login
        if ($session !== null && $command === '/login') {
            $this->bot->sendMessage($chatId, '✅ Anda sudah login. Ketik /help untuk melihat command yang tersedia.');
            return;
        }

        if ($command === '/login') {
            $cache->save($stateKey, ['step' => 'awaiting_email'], 300);
            $this->bot->sendMessage($chatId, "🔐 <b>Login Admin</b>\n\nMasukkan email akun admin Anda:");
            return;
        }

        // Step: input email
        if (isset($state['step']) && $state['step'] === 'awaiting_email') {
            $email = trim($command);
            $cache->save($stateKey, ['step' => 'awaiting_password', 'email' => $email], 300);
            $this->bot->sendMessage($chatId, "📧 Email: <code>{$email}</code>\n\nMasukkan password:");
            return;
        }

        // Step: input password
        if (isset($state['step']) && $state['step'] === 'awaiting_password') {
            // Hapus pesan password secepatnya
            $messageId = $update['message']['message_id'] ?? null;
            if ($messageId) {
                $this->bot->deleteMessage($chatId, $messageId);
            }

            $email    = $state['email'];
            $password = $command;

            $adminModel = new AdminModel();
            $admin      = $adminModel->findActiveByEmail($email);

            $cache->delete($stateKey);

            if ($admin === null || ! password_verify($password, $admin['password'])) {
                $attempts->recordAttempt($userId, false);
                $this->bot->sendMessage($chatId, "❌ Email atau password salah. Silakan ketik /login untuk mencoba kembali.");
                return;
            }

            $attempts->recordAttempt($userId, true);

            $this->sessions->createSession($userId, $chatId, (int) $admin['id']);

            $this->bot->sendMessage($chatId,
                "✅ <b>Login berhasil!</b>\n\n"
                . "Selamat datang, <b>" . htmlspecialchars($admin['name']) . "</b>!\n"
                . "Role: <b>" . strtoupper($admin['role']) . "</b>\n\n"
                . "Ketik /help untuk melihat command yang tersedia."
            );

            return;
        }
    }
}
