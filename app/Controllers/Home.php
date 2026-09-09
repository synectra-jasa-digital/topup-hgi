<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\BongkarCatalogModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\StoreSettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index(): string
    {
        $categories = (new ProductCategoryModel())->listActive();
        $products        = new ProductModel();
        $settings        = new StoreSettingModel();
        $bongkarCatalogs = new BongkarCatalogModel();

        $sections = [];
        foreach ($categories as $category) {
            $items = $products->listActiveByCategory($category['id']);
            if ($items === []) {
                continue;
            }
            $sections[] = ['category' => $category, 'products' => $items];
        }

        $adminWhatsapp = trim($settings->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: '')));

        $storeName    = $settings->getVal('store_name', 'Ayong Store');
        $categoryList = implode(', ', array_column($categories, 'name'));
        $banners      = (new BannerModel())->listActiveForDisplay();

        $heroPreloadImage = null;
        if (! empty($banners[0]['image_path']) && ! str_starts_with($banners[0]['image_path'], 'http')) {
            $path = $banners[0]['image_path'];
            $webp = str_ends_with($path, '.png') ? substr($path, 0, -4) . '.webp' : null;
            $heroPreloadImage = base_url(($webp && is_file(FCPATH . $webp)) ? $webp : $path);
        }

        return view('catalog/index', [
            'title'            => $storeName . ' - Top Up Higgs Games Island',
            'metaDescription'  => "Top up {$categoryList} otomatis di {$storeName}. Proses instan 24 jam, pembayaran QRIS/e-wallet/VA, harga bersaing.",
            'heroPreloadImage' => $heroPreloadImage,
            'banners'          => $banners,
            'categories'      => $categories,
            'sections'        => $sections,
            'bongkarCatalogs' => $bongkarCatalogs->listActive(),
            'adminWhatsapp'   => $adminWhatsapp,
        ]);
    }

    public function sitemap()
    {
        $categories = (new ProductCategoryModel())->listActive();

        $urls = [
            ['loc' => base_url('/'), 'priority' => '1.0'],
        ];
        foreach ($categories as $category) {
            $urls[] = ['loc' => base_url('kategori/' . $category['slug']), 'priority' => '0.8'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . esc($url['loc']) . '</loc><priority>' . $url['priority'] . '</priority></url>' . "\n";
        }
        $xml .= '</urlset>';

        return $this->response->setContentType('application/xml')->setBody($xml);
    }

    public function kategori(string $slug): string
    {
        $category = (new ProductCategoryModel())->findActiveBySlug($slug);

        if (! $category) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('catalog/category', [
            'title'           => 'Top Up ' . $category['name'] . ' - Ayong Store',
            'metaDescription' => "Top up {$category['name']} murah, cepat, dan aman di Ayong Store. Pilih nominal, bayar instan, item langsung diproses.",
            'category'        => $category,
            'products'        => (new ProductModel())->listActiveByCategory($category['id']),
        ]);
    }
}
