<?php

namespace App\Models;

use CodeIgniter\Model;

class TelegramOutboxModel extends Model
{
    protected $table      = 'telegram_outbox';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'chat_id', 'order_id', 'type', 'payload',
        'attempts', 'sent_message_id', 'status', 'error_message',
    ];

    protected $useTimestamps = true;

    /**
     * Enqueue pesan ke outbox.
     *
     * @param array<string,mixed> $payload  Data mentah untuk dikodekan JSON.
     */
    public function enqueue(int $chatId, string $type, array $payload, ?int $orderId = null): int
    {
        return $this->insert([
            'chat_id'  => $chatId,
            'order_id' => $orderId,
            'type'     => $type,
            'payload'  => json_encode($payload),
            'status'   => 'pending',
        ]);
    }

    /** Ambil item pending yang siap dikirim ulang (max 50). */
    public function getPending(int $limit = 50): array
    {
        return $this->where('status', 'pending')
                    ->where('attempts <', 3)
                    ->orderBy('created_at', 'ASC')
                    ->findAll($limit);
    }

    public function markSent(int $id, int $sentMessageId): void
    {
        $this->update($id, [
            'status'          => 'sent',
            'sent_message_id' => $sentMessageId,
        ]);
    }

    public function markFailed(int $id, string $error): void
    {
        $item = $this->find($id);
        $attempts = ($item['attempts'] ?? 0) + 1;

        $this->update($id, [
            'attempts'      => $attempts,
            'error_message' => $error,
            'status'        => $attempts >= 3 ? 'failed' : 'pending',
        ]);
    }

    /**
     * Perbarui sent_message_id pada item SENT tertentu (untuk edit pesan setelah verifikasi).
     */
    public function findSentByOrderAndType(int $orderId, string $type): array
    {
        return $this->where('order_id', $orderId)
                    ->where('type', $type)
                    ->where('status', 'sent')
                    ->findAll();
    }
}
