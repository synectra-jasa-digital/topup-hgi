<?php

namespace App\Models;

use CodeIgniter\Model;

class BongkarPayoutMethodModel extends Model
{
    protected $table         = 'bongkar_payout_methods';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['code', 'name', 'category', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural',
        'code'     => 'required|max_length[50]|is_unique[bongkar_payout_methods.code,id,{id}]',
        'name'     => 'required|max_length[100]',
        'category' => 'required|in_list[bank,ewallet]',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }
}
