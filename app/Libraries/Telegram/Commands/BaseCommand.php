<?php

namespace App\Libraries\Telegram\Commands;

use App\Libraries\TelegramBot;
use App\Models\TelegramSessionModel;

/**
 * Base class untuk semua Telegram command.
 * Menyediakan akses sesi, bot, dan helper format.
 */
abstract class BaseCommand
{
    protected TelegramBot $bot;
    protected TelegramSessionModel $sessions;

    public function __construct(?TelegramBot $bot = null, ?TelegramSessionModel $sessions = null)
    {
        $this->bot      = $bot ?? new TelegramBot();
        $this->sessions = $sessions ?? new TelegramSessionModel();
    }

    abstract public function handle(array $update, ?array $session): void;

    /** Apakah update ini berasal dari private chat. */
    protected function isPrivateChat(array $update): bool
    {
        $chatType = $update['message']['chat']['type']
            ?? $update['callback_query']['message']['chat']['type']
            ?? '';

        return $chatType === 'private';
    }

    protected function getChatId(array $update): int
    {
        return (int) (
            $update['message']['chat']['id']
            ?? $update['callback_query']['message']['chat']['id']
            ?? 0
        );
    }

    protected function getUserId(array $update): int
    {
        return (int) (
            $update['message']['from']['id']
            ?? $update['callback_query']['from']['id']
            ?? 0
        );
    }

    protected function getText(array $update): string
    {
        return $update['message']['text'] ?? '';
    }

    /** Format Rupiah: 150000 → Rp 150.000 */
    protected function rp(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /** Deny command dari non-private chat. */
    protected function denyGroupChat(array $update): bool
    {
        if (! $this->isPrivateChat($update)) {
            $chatId = $this->getChatId($update);
            $this->bot->sendMessage($chatId, 'Command ini hanya bisa digunakan di chat pribadi dengan bot.');
            return true;
        }
        return false;
    }

    /** Deny jika belum login. Return false jika aman. */
    protected function requireLogin(array $update, ?array $session): bool
    {
        if ($session === null) {
            $chatId = $this->getChatId($update);
            $this->bot->sendMessage($chatId, 'Silakan /login terlebih dahulu.');
            return true;
        }
        return false;
    }

    /** Require role tertentu (admin atau owner). */
    protected function requireRole(array $update, ?array $session, array $roles): bool
    {
        if ($this->requireLogin($update, $session)) {
            return true;
        }

        if (! in_array($session['admin_role'] ?? '', $roles, true)) {
            $chatId = $this->getChatId($update);
            $this->bot->sendMessage($chatId, 'Anda tidak memiliki akses untuk command ini.');
            return true;
        }

        return false;
    }

    protected function logActivity(int $adminId, string $action, string $description): void
    {
        $logModel = new \App\Models\ActivityLogModel();
        $logModel->log($adminId, $action, $description);
    }
}
