<?php

namespace App\Models;

use CodeIgniter\Model;

class BongkarCatalogModel extends Model
{
    protected $table         = 'bongkar_catalogs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['code', 'name', 'unit_label', 'base_rate', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'code'       => 'required|max_length[100]|is_unique[bongkar_catalogs.code,id,{id}]',
        'name'       => 'required|max_length[150]',
        'unit_label' => 'required|max_length[50]',
        'base_rate'  => 'required|decimal',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }
}
