<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\StoreSettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index(): string
    {
        $categories = (new ProductCategoryModel())->listActive();
        $products   = new ProductModel();
        $settings   = new StoreSettingModel();

        $sections = [];
        foreach ($categories as $category) {
            $items = $products->listActiveByCategory($category['id']);
            if ($items === []) {
                continue;
            }
            $sections[] = ['category' => $category, 'products' => $items];
        }

        $adminWhatsapp = trim($settings->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: '')));

        return view('catalog/index', [
            'title'          => 'Ayong Store - Top Up Higgs Games Island',
            'banners'        => (new BannerModel())->listActiveForDisplay(),
            'categories'     => $categories,
            'sections'       => $sections,
            'adminWhatsapp'  => $adminWhatsapp,
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
