<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderPaymentModel extends Model
{
    protected $table         = 'order_payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'order_id', 'midtrans_order_id', 'midtrans_transaction_id', 'payment_method',
        'payment_channel', 'paid_at', 'raw_notification'
    ];
    protected $useTimestamps = true;
}
