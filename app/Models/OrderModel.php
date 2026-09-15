<?php

namespace App\Models;

use App\Models\VoucherModel;
use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'invoice_number', 'product_id', 'product_name_snapshot', 'nominal_snapshot',
        'price_snapshot', 'game_id', 'whatsapp_number', 'voucher_id', 'discount_amount',
        'voucher_reserved', 'voucher_committed', 'voucher_reserved_until', 'idempotency_token', 'wablas_notification_claimed',
        'public_access_token', 'payment_channel_id', 'payment_channel_type', 'payment_channel_name',
        'payment_account_number', 'payment_account_holder', 'payment_qr_image_path',
        'payment_proof_path', 'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at', 'payment_rejection_reason',
        'total_amount', 'status', 'processed_by', 'completed_at',
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

    public function findByToken(string $token): ?array
    {
        return $this->where('idempotency_token', $token)->first();
    }

    public function findByInvoiceAndAccessToken(string $invoiceNumber, string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        return $this->where('invoice_number', $invoiceNumber)->where('public_access_token', $token)->first();
    }

    public function verifyPayment(int $orderId, int $adminId): bool
    {
        $now = date('Y-m-d H:i:s');
        $this->builder()->where('id', $orderId)->where('status', 'menunggu_verifikasi')->update([
            'status' => 'diproses', 'payment_verified_by' => $adminId,
            'payment_verified_at' => $now, 'payment_rejection_reason' => null, 'updated_at' => $now,
        ]);

        return $this->db->affectedRows() === 1;
    }

    public function rejectPayment(int $orderId, int $adminId, string $reason): bool
    {
        $now = date('Y-m-d H:i:s');
        $this->builder()->where('id', $orderId)->where('status', 'menunggu_verifikasi')->update([
            'status' => 'menunggu_pembayaran', 'payment_verified_by' => $adminId,
            'payment_verified_at' => $now, 'payment_rejection_reason' => $reason, 'updated_at' => $now,
        ]);

        return $this->db->affectedRows() === 1;
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
        return ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'selesai', 'gagal', 'dibatalkan'];
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

    public function releaseExpiredVoucherReservations(): int
    {
        $orders = $this->where('voucher_reserved', 1)
            ->where('voucher_reserved_until <', date('Y-m-d H:i:s'))
            ->findAll();
        $released = 0;
        $vouchers = new VoucherModel();
        foreach ($orders as $order) {
            $this->db->transStart();
            if ($vouchers->releaseReservation((int) $order['voucher_id'])) {
                $this->update($order['id'], ['voucher_reserved' => 0, 'voucher_reserved_until' => null]);
                $released++;
            }
            $this->db->transComplete();
        }
        return $released;
    }
}
