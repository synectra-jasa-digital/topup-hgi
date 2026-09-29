<?php

namespace App\Models;

use CodeIgniter\Model;

class TelegramUpdateModel extends Model
{
    protected $table      = 'telegram_updates';
    protected $primaryKey = 'id';

    protected $allowedFields = ['update_id', 'received_at'];

    /** Record update_id. Ret return false if already processed (idempotency). */
    public function record(int $updateId): bool
    {
        if ($this->where('update_id', $updateId)->first()) {
            return false;
        }

        $this->insert([
            'update_id'   => $updateId,
            'received_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }
}
