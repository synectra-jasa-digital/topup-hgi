<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index(): string
    {
        $categories = (new ProductCategoryModel())->listActive();
        $products   = new ProductModel();

        $sections = [];
        foreach ($categories as $category) {
            $items = $products->listActiveByCategory($category['id']);
            if ($items === []) {
                continue;
            }
            $sections[] = ['category' => $category, 'products' => $items];
        }

        return view('catalog/index', [
            'title'      => 'Ayong Store - Top Up Higgs Games Island',
            'banners'    => (new BannerModel())->listActiveForDisplay(),
            'categories' => $categories,
            'sections'   => $sections,
        ]);
    }

    public function kategori(string $slug): string
    {
        $category = (new ProductCategoryModel())->findActiveBySlug($slug);

        if (! $category) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('catalog/category', [
            'title'    => $category['name'] . ' - Ayong Store',
            'category' => $category,
            'products' => (new ProductModel())->listActiveByCategory($category['id']),
        ]);
    }
}
