<?php

namespace App\Models;

use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table         = 'vouchers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'code', 'type', 'value', 'min_purchase', 'max_discount',
        'quota', 'used_count', 'start_date', 'end_date', 'is_active',
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'code' => 'required|max_length[50]|is_unique[vouchers.code,id,{id}]',
    ];

    public function findValid(string $code, float $subtotal): ?array
    {
        $today   = date('Y-m-d');
        $voucher = $this->where('code', $code)->where('is_active', 1)->first();

        if (! $voucher) {
            return null;
        }
        if (! empty($voucher['start_date']) && $voucher['start_date'] > $today) {
            return null;
        }
        if (! empty($voucher['end_date']) && $voucher['end_date'] < $today) {
            return null;
        }
        if ((int) $voucher['quota'] > 0 && (int) $voucher['used_count'] >= (int) $voucher['quota']) {
            return null;
        }
        if ($subtotal < (float) $voucher['min_purchase']) {
            return null;
        }

        return $voucher;
    }

    public function calculateDiscount(array $voucher, float $subtotal): float
    {
        $discount = $voucher['type'] === 'percentage'
            ? $subtotal * ((float) $voucher['value'] / 100)
            : (float) $voucher['value'];

        if ($voucher['max_discount'] !== null) {
            $discount = min($discount, (float) $voucher['max_discount']);
        }

        return min($discount, $subtotal);
    }

    public function incrementUsage(int $id): void
    {
        $this->set('used_count', 'used_count + 1', false)->update($id);
    }
}
