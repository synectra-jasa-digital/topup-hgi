<?php

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table         = 'banners';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['banner_category_id', 'image_path', 'link_url', 'start_date', 'end_date', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'banner_category_id' => 'required|is_natural_no_zero',
        'link_url'           => 'permit_empty|max_length[255]|valid_url_strict',
        'start_date'         => 'permit_empty|valid_date',
        'end_date'           => 'permit_empty|valid_date',
    ];

    public function listWithCategory(): array
    {
        return $this->select('banners.*, banner_categories.name as category_name')
            ->join('banner_categories', 'banner_categories.id = banners.banner_category_id')
            ->orderBy('banners.sort_order')
            ->findAll();
    }

    public function listActiveForDisplay(): array
    {
        $today = date('Y-m-d');

        return $this->where('is_active', 1)
            ->groupStart()
                ->where('start_date IS NULL')
                ->orWhere('start_date <=', $today)
            ->groupEnd()
            ->groupStart()
                ->where('end_date IS NULL')
                ->orWhere('end_date >=', $today)
            ->groupEnd()
            ->orderBy('sort_order')
            ->findAll();
    }
}
