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

    public function adminList(?string $status = null, int $perPage = 15): array
    {
        $query = $this->orderBy('created_at', 'DESC');
        if ($status !== null && in_array($status, self::adminStatuses(), true)) {
            $query->where('status', $status);
        }
        return $query->paginate($perPage);
    }

    public static function adminStatuses(): array
    {
        return ['menunggu_pembayaran', 'dibayar', 'diproses', 'selesai', 'gagal', 'dibatalkan'];
    }

    public function countByStatus(): array
    {
        $rows = $this->select('status, COUNT(id) as total')->groupBy('status')->findAll();

        $counts = array_fill_keys(self::adminStatuses(), 0);
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    public function complete(int $orderId, int $adminId): bool
    {
        $now = date('Y-m-d H:i:s');
        $this->db->transStart();
        $this->builder()->where('id', $orderId)->where('status', 'diproses')->update([
            'status' => 'selesai', 'processed_by' => $adminId,
            'completed_at' => $now, 'updated_at' => $now,
        ]);
        $affected = $this->db->affectedRows();
        $this->db->transComplete();
        return $this->db->transStatus() && $affected === 1;
    }
}
