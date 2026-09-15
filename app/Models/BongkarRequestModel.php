<?php

namespace App\Models;

use CodeIgniter\Model;

class BongkarRequestModel extends Model
{
    protected $table = 'bongkar_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'request_number',
        'bongkar_catalog_id',
        'catalog_code_snapshot',
        'catalog_name_snapshot',
        'unit_label_snapshot',
        'quantity',
        'rate_snapshot',
        'estimated_amount',
        'customer_whatsapp',
        'payout_method',
        'payout_account_number',
        'payout_account_name',
        'customer_note',
        'status',
        'actor_admin_id',
        'previous_status',
        'status_changed_at',
        'notification_status',
        'notification_retry_count',
        'next_retry_at',
    ];
    protected $useTimestamps = true;

    public static function adminStatuses(): array
    {
        return ['pending', 'diproses', 'selesai', 'ditolak'];
    }

    public static function validTransitions(): array
    {
        return [
            'pending'  => ['diproses', 'ditolak'],
            'diproses' => ['selesai', 'ditolak'],
            'ditolak'  => ['diproses'],
        ];
    }

    public function canTransition(string $current, string $next): bool
    {
        $transitions = self::validTransitions();
        return isset($transitions[$current]) && in_array($next, $transitions[$current], true);
    }

    public function findNotificationQueue(int $limit = 50): array
    {
        return $this->groupStart()
            ->where('notification_status', 'pending')
            ->orGroupStart()
                ->where('notification_status', 'failed')
                ->where('notification_retry_count <', 5)
                ->where('next_retry_at <=', date('Y-m-d H:i:s'))
            ->groupEnd()
        ->groupEnd()
            ->orderBy('next_retry_at', 'ASC')
            ->findAll($limit);
    }

    public function claimNotification(int $id): bool
    {
        $builder = $this->builder();
        $builder->where('id', $id)
            ->groupStart()
                ->where('notification_status', 'pending')
                ->orWhere('notification_status', 'failed')
            ->groupEnd();

        return $builder->update(['notification_status' => 'processing']);
    }

    public function markNotificationResult(int $id, bool $sent): bool
    {
        $request = $this->find($id);
        if (! $request) {
            return false;
        }

        if ($sent) {
            return $this->builder()
                ->where('id', $id)
                ->where('notification_status', 'processing')
                ->update([
                'notification_status' => 'sent',
                'next_retry_at' => null,
                ]);
        }

        $retryCount = (int) $request['notification_retry_count'] + 1;
        return $this->builder()
            ->where('id', $id)
            ->where('notification_status', 'processing')
            ->update([
            'notification_status' => 'failed',
            'notification_retry_count' => $retryCount,
            'next_retry_at' => $retryCount >= 5 ? null : date('Y-m-d H:i:s', time() + min(3600, 60 * (2 ** $retryCount))),
            ]);
    }

    public function adminList(?string $status = null, int $perPage = 15): array
    {
        $query = $this->orderBy('created_at', 'DESC');
        if ($status !== null && in_array($status, self::adminStatuses(), true)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
