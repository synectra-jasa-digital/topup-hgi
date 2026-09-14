<?php

namespace App\Models;

use App\Libraries\Money;
use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['code', 'type', 'value', 'min_purchase', 'max_discount', 'quota', 'used_count', 'reserved_count', 'start_date', 'end_date', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = ['code' => 'required|max_length[50]|is_unique[vouchers.code,id,{id}]'];

    public function findValid(string $code, int $subtotal): ?array
    {
        $today = date('Y-m-d');
        $voucher = $this->where('code', $code)->where('is_active', 1)->first();
        if (! $voucher || (! empty($voucher['start_date']) && $voucher['start_date'] > $today) || (! empty($voucher['end_date']) && $voucher['end_date'] < $today)) return null;
        if ((int) $voucher['quota'] > 0 && (int) $voucher['used_count'] + (int) ($voucher['reserved_count'] ?? 0) >= (int) $voucher['quota']) return null;
        if ($subtotal < Money::rupiah($voucher['min_purchase'])) return null;
        return $voucher;
    }

    public function calculateDiscount(array $voucher, int $subtotal): int
    {
        $discount = $voucher['type'] === 'percentage' ? Money::percentage($subtotal, $voucher['value']) : Money::rupiah($voucher['value']);
        if ($voucher['max_discount'] !== null) $discount = min($discount, Money::rupiah($voucher['max_discount']));
        return min(max(0, $discount), max(0, $subtotal));
    }

    public function reserve(int $id): bool
    {
        $this->set('reserved_count', 'reserved_count + 1', false)->where('id', $id)->where('is_active', 1)
            ->groupStart()->where('quota', 0)->orWhere('used_count + reserved_count < quota', null, false)->groupEnd()->update();
        return $this->db->affectedRows() === 1;
    }

    public function commitReservation(int $id): bool
    {
        $this->set('used_count', 'used_count + 1', false)->set('reserved_count', 'reserved_count - 1', false)
            ->where('id', $id)->where('reserved_count >', 0)->update();
        return $this->db->affectedRows() === 1;
    }

    public function releaseReservation(int $id): bool
    {
        $this->set('reserved_count', 'reserved_count - 1', false)->where('id', $id)->where('reserved_count >', 0)->update();
        return $this->db->affectedRows() === 1;
    }
}
