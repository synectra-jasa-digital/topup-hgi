<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admins';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'email', 'password', 'role', 'is_active'];
    protected $useTimestamps    = true;

    public function findActiveByEmail(string $email): ?array
    {
        return $this->where('email', $email)->where('is_active', 1)->first();
    }
}
