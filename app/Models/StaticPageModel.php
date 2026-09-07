<?php

namespace App\Models;

use CodeIgniter\Model;

class StaticPageModel extends Model
{
    protected $table         = 'static_pages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['slug', 'title', 'content'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'title'   => 'required|max_length[150]',
        'content' => 'required',
    ];

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
