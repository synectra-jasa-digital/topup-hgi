<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentChannelModel extends Model
{
    protected $table         = 'payment_channels';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['type', 'name', 'account_number', 'account_holder', 'qr_image_path', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'id'   => 'permit_empty|is_natural',
        'type' => 'required|in_list[bank,qris]',
        'name' => 'required|max_length[100]',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }
}
