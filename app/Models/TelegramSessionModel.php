<?php

namespace App\Models;

use CodeIgniter\Model;

class TelegramSessionModel extends Model
{
    protected $table      = 'telegram_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'telegram_user_id', 'chat_id', 'admin_id', 'notify',
        'last_active_at', 'expires_at',
    ];

    protected $useTimestamps = true;

    public function findByUserId(int $telegramUserId): ?array
    {
        return $this->where('telegram_user_id', $telegramUserId)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    /** Buat atau timpa sesi untuk user ini. */
    public function createSession(int $telegramUserId, int $chatId, int $adminId): void
    {
        $now     = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime('+7 days'));

        $existing = $this->where('telegram_user_id', $telegramUserId)->first();

        $data = [
            'telegram_user_id' => $telegramUserId,
            'chat_id'          => $chatId,
            'admin_id'         => $adminId,
            'notify'           => 1,
            'last_active_at'   => $now,
            'expires_at'       => $expires,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }

    public function deleteByUserId(int $telegramUserId): void
    {
        $this->where('telegram_user_id', $telegramUserId)->delete();
    }

    /** Hapus semua sesi milik admin_id tertentu (password diganti / akun nonaktif). */
    public function deleteByAdminId(int $adminId): void
    {
        $this->where('admin_id', $adminId)->delete();
    }

    public function touch(int $telegramUserId): void
    {
        $this->where('telegram_user_id', $telegramUserId)->set([
            'last_active_at' => date('Y-m-d H:i:s'),
            'expires_at'     => date('Y-m-d H:i:s', strtotime('+7 days')),
        ])->update();
    }

    /** Semua sesi aktif yang punya notifikasi ON — untuk broadcast. */
    public function getNotifiableSessions(): array
    {
        return $this->where('notify', 1)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->findAll();
    }

    /** Semua sesi aktif — untuk broadcast status toko. */
    public function getActiveSessions(): array
    {
        return $this->where('expires_at >', date('Y-m-d H:i:s'))->findAll();
    }

    /** Untuk command /sesi (owner only). */
    public function listActive(): array
    {
        return $this->db->table('telegram_sessions ts')
                        ->join('admins a', 'a.id = ts.admin_id')
                        ->select('ts.*, a.name as admin_name, a.role as admin_role')
                        ->where('ts.expires_at >', date('Y-m-d H:i:s'))
                        ->orderBy('ts.last_active_at', 'DESC')
                        ->get()->getResultArray();
    }

    public function revokeById(int $id): void
    {
        $this->delete($id);
    }
}
