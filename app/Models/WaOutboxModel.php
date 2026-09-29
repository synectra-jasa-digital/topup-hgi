<?php

namespace App\Models;

use CodeIgniter\Model;

class WaOutboxModel extends Model
{
    protected $table            = 'wa_outbox';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'recipient',
        'type',
        'message',
        'ref_type',
        'ref_id',
        'dedupe_key',
        'status',
        'attempts',
        'next_retry_at',
        'gateway_message_id',
        'error_message',
        'created_at',
        'sent_at',
    ];
    protected $useTimestamps = false;

    public function enqueue(array $data): ?int
    {
        $existing = $this->where('dedupe_key', $data['dedupe_key'])->first();
        if ($existing) {
            return null;
        }

        if (! isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (! isset($data['status'])) {
            $data['status'] = 'pending';
        }

        try {
            return (int) $this->insert($data, true);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function claim(int $limit = 10): array
    {
        $db = $this->db;
        $db->transStart();

        $rows = $this->whereIn('status', ['pending', 'failed'])
                     ->groupStart()
                         ->where('next_retry_at IS NULL')
                         ->orWhere('next_retry_at <=', date('Y-m-d H:i:s'))
                     ->groupEnd()
                     ->where('attempts <', 5)
                     ->orderBy('id', 'ASC')
                     ->limit($limit)
                     ->get()
                     ->getResultArray();

        if (! empty($rows)) {
            $ids = array_column($rows, 'id');
            $this->whereIn('id', $ids)->set(['status' => 'processing'])->update();
        }

        $db->transComplete();

        return $db->transStatus() ? $rows : [];
    }

    public function markSent(int $id, ?string $messageId = null): bool
    {
        return $this->update($id, [
            'status'             => 'sent',
            'gateway_message_id' => $messageId,
            'sent_at'            => date('Y-m-d H:i:s'),
            'error_message'      => null,
        ]);
    }

    public function markFailed(int $id, string $errorMessage): bool
    {
        $row = $this->find($id);
        if (! $row) {
            return false;
        }

        $attempts = ((int) $row['attempts']) + 1;

        if ($attempts >= 5) {
            return $this->update($id, [
                'status'        => 'failed',
                'attempts'      => $attempts,
                'error_message' => $errorMessage,
            ]);
        }

        // Exponential backoff: 2 min, 4 min, 8 min, 16 min, 32 min
        $delaySeconds = 120 * pow(2, $attempts - 1);
        $nextRetryAt  = date('Y-m-d H:i:s', time() + (int) $delaySeconds);

        return $this->update($id, [
            'status'        => 'pending',
            'attempts'      => $attempts,
            'next_retry_at' => $nextRetryAt,
            'error_message' => $errorMessage,
        ]);
    }

    public function markInvalidNumber(int $id, string $errorMessage): bool
    {
        return $this->update($id, [
            'status'        => 'invalid_number',
            'error_message' => $errorMessage,
        ]);
    }

    public function cleanupOldMessages(int $days = 30): int
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $this->whereIn('status', ['sent', 'failed', 'invalid_number'])
             ->where('created_at <=', $threshold)
             ->where('message !=', '')
             ->set(['message' => ''])
             ->update();

        return $this->db->affectedRows();
    }
}
