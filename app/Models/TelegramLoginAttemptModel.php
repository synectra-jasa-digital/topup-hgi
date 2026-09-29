<?php

namespace App\Models;

use CodeIgniter\Model;

class TelegramLoginAttemptModel extends Model
{
    protected $table      = 'telegram_login_attempts';
    protected $primaryKey = 'id';

    protected $allowedFields = ['telegram_user_id', 'attempted_at', 'success'];

    public function isLockedOut(int $telegramUserId): bool
    {
        $since = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $failedCount = $this->where('telegram_user_id', $telegramUserId)
                            ->where('success', 0)
                            ->where('attempted_at >=', $since)
                            ->countAllResults();

        return $failedCount >= 5;
    }

    public function recordAttempt(int $telegramUserId, bool $success): void
    {
        $this->insert([
            'telegram_user_id' => $telegramUserId,
            'attempted_at'     => date('Y-m-d H:i:s'),
            'success'          => $success ? 1 : 0,
        ]);
    }
}
