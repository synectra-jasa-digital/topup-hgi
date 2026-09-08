<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table         = 'announcements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['message', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'message' => 'required|max_length[255]',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }
}
