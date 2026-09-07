<?php

namespace App\Models;

use CodeIgniter\Model;

class BannerCategoryModel extends Model
{
    protected $table         = 'banner_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'name' => 'required|max_length[100]',
    ];
}
