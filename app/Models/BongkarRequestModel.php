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
        'customer_note',
        'status',
    ];
    protected $useTimestamps = true;
}
