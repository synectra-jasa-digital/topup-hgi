<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['category_id', 'name', 'nominal', 'sell_price', 'cost_price', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'name'        => 'required|max_length[150]',
        'nominal'     => 'required|max_length[100]',
        'sell_price'  => 'required|decimal',
        'cost_price'  => 'required|decimal',
    ];

    public function listWithCategory(): array
    {
        return $this->select('products.*, product_categories.name as category_name')
            ->join('product_categories', 'product_categories.id = products.category_id')
            ->orderBy('products.category_id')
            ->orderBy('products.sort_order')
            ->findAll();
    }

    public function listActiveByCategory(int $categoryId): array
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->findAll();
    }
}
