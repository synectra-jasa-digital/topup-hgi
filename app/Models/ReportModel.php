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

    // --- Ringkasan Harian (Revenue, Order Count) ---
    public function dailySummary(int $dayOffset = 0): array
    {
        $date = date('Y-m-d', strtotime("-$dayOffset days"));

        $query = $this->db->table('orders o')
            ->select(
                "SUM(o.total_amount) as revenue,"
                . "COUNT(o.id) as order_count"
            )
            ->join('order_payments op', 'op.order_id = o.id', 'left')
            ->where('o.status', 'selesai')
            ->groupStart()
                ->where('DATE(o.created_at)', $date)
                ->orGroupStart()
                    ->where('op.created_at >=', "$date 00:00:00")
                    ->where('op.created_at <', "$date 23:59:59")
                ->groupEnd()
            ->groupEnd()
            ->get();

        $row = $query->getRowArray() ?: [];

        return [
            'date'        => $date,
            'revenue'     => $row['revenue'] ?? 0,
            'order_count' => $row['order_count'] ?? 0,
        ];
    }

    // --- Ringkasan Bulanan ---
    public function monthlySummary(int $year, int $month): array
    {
        $query = $this->db->table('orders o')
            ->select(
                "DATE(o.created_at) as order_date,"
                . "o.total_amount as revenue"
            )
            ->where('o.status', 'selesai')
            ->where('MONTH(o.created_at)', $month)
            ->where('YEAR(o.created_at)', $year)
            ->get();

        return $query->getResultArray();
    }

    // --- Laporan Penjualan per Kategori Produk ---
    // Setiap order = satu produk (lihat CreateOrdersTable), jadi cukup join
    // langsung ke products/product_categories tanpa tabel order_items.
    public function topCategories(int $days = 30): array
    {
        return $this->db->table('orders o')
            ->select('pc.name as category_name, COUNT(o.id) as total_quantity, SUM(o.total_amount) as total_revenue')
            ->join('products p', 'p.id = o.product_id')
            ->join('product_categories pc', 'pc.id = p.category_id')
            ->where('o.status', 'selesai')
            ->where('o.created_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupBy('pc.name')
            ->orderBy('total_revenue', 'DESC')
            ->get()
            ->getResultArray();
    }
}
