<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [];
    protected $useTimestamps = false;

    // --- Ringkasan Harian (Revenue, Order Count, Payment Methods) ---
    public function dailySummary(int $dayOffset = 0): array
    {
        $date = date('Y-m-d', strtotime("-$dayOffset days"));

        $query = $this->db->table('orders')
            ->select(
                "DATE(o.created_at) as date,"
                . "SUM(o.grand_total) as revenue,"
                . "COUNT(o.id) as order_count,"
                . "COALESCE(SUM(CASE WHEN op.method = 'wallet' THEN op.amount ELSE 0 END), 0) as wallet_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'qr' THEN op.amount ELSE 0 END), 0) as qr_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'gopay' THEN op.amount ELSE 0 END), 0) as gopay_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'ovo' THEN op.amount ELSE 0 END), 0) as ovo_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'dana' THEN op.amount ELSE 0 END), 0) as dana_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'bni' THEN op.amount ELSE 0 END), 0) as bni_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'bca' THEN op.amount ELSE 0 END), 0) as bca_revenue,"
                . "COALESCE(SUM(CASE WHEN op.method = 'mandiri' THEN op.amount ELSE 0 END), 0) as mandiri_revenue"
            )
            ->join('order_payments op', 'op.order_id = o.id', 'left')
            ->where('o.status', 'selesai')
            ->groupStart()
                ->groupStart()
                    ->where('DATE(o.created_at)', $date)
                ->otherwise()
                    ->where('op.created_at >=', "$date 00:00:00")
                    ->where('op.created_at <', "$date 23:59:59")
                ->endGroup()
            ->endGroup()
            ->get();

        return $query->getRowArray() ?: ['date' => $date, 'revenue' => 0, 'order_count' => 0];
    }

    // --- Ringkasan Bulanan ---
    public function monthlySummary(int $year, int $month): array
    {
        $period = "$year-$month-01";

        $query = $this->db->table('orders')
            ->select(
                "DATE(o.created_at) as order_date,"
                . "o.grand_total as revenue"
            )
            ->where('o.status', 'selesai')
            ->where('MONTH(o.created_at)', $month)
            ->where('YEAR(o.created_at)', $year)
            ->get();

        return $query->getResultArray();
    }

    // --- List Pembayaran per Order ---
    public function orderPayments(int $orderId): array
    {
        return $this->db->table('order_payments')
            ->select('method, amount, created_at, admin_name')
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // --- Laporan Penjualan per Kategori Produk ---
    public function topCategories(int $days = 30): array
    {
        return $this->db->table('order_items')
            ->select('pc.name as category_name, COUNT(oi.id) as total_quantity, SUM(oi.total_price) as total_revenue')
            ->join('products p', 'p.id = oi.product_id')
            ->join('product_categories pc', 'pc.id = p.category_id')
            ->where('o.status', 'selesai')
            ->where('o.created_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupBy('pc.name')
            ->orderBy('total_revenue', 'DESC')
            ->get()
            ->getResultArray();
    }

    // --- Get Order Detail for PDF Report ---
    public function orderDetail(int $orderId): array
    {
        return $this->db->table('orders')
            ->select(
                'o.id, o.invoice_number, o.user_id, o.customer_name, o.customer_phone,'
                . 'o.grand_total, o.status, o.created_at,'
                . '(SELECT SUM(oi.total_price) FROM order_items oi WHERE oi.order_id = o.id) as subtotal,'
                . '(SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count'
            )
            ->where('o.id', $orderId)
            ->get()
            ->getRowArray();
    }
}
