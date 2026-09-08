<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['admin_id', 'action', 'description', 'ip_address', 'created_at'];
    protected $useTimestamps = false;

    public function log(int $adminId, string $action, string $description): void
    {
        $this->insert([
            'admin_id'   => $adminId,
            'action'     => $action,
            'description'=> $description,
            'ip_address' => service('request')->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function listWithAdmin(?int $adminId = null, int $perPage = 30): array
    {
        $q = $this->select('activity_logs.*, admins.name as admin_name')
                  ->join('admins', 'admins.id = activity_logs.admin_id')
                  ->orderBy('activity_logs.id', 'DESC');

        if ($adminId !== null) {
            $q->where('activity_logs.admin_id', $adminId);
        }

        return $q->paginate($perPage);
    }
}
