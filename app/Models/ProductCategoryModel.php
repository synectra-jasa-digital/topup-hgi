<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $table         = 'product_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'slug', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'id'   => 'permit_empty|is_natural',
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[120]|is_unique[product_categories.slug,id,{id}]',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->where('is_active', 1)->first();
    }
}
