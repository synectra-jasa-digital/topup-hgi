<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'invoice_number', 'product_id', 'product_name_snapshot', 'nominal_snapshot',
        'price_snapshot', 'game_id', 'whatsapp_number', 'voucher_id', 'discount_amount',
        'total_amount', 'status', 'processed_by', 'completed_at', 'snap_token',
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'game_id'         => ['label' => 'ID Akun Higgs Games Island', 'rules' => 'required|max_length[100]'],
        'whatsapp_number' => ['label' => 'Nomor WhatsApp', 'rules' => 'required|max_length[20]|regex_match[/^(08|628)[0-9]{7,12}$/]'],
    ];

    public function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV' . date('Ymd') . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->where('invoice_number', $number)->first());

        return $number;
    }

    public function findByInvoice(string $invoiceNumber): ?array
    {
        return $this->where('invoice_number', $invoiceNumber)->first();
    }
}
