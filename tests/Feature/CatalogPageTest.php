<?php

namespace Tests\Feature;

use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class CatalogPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    public function testCatalogRendersFullWidthDesignAndProductNavigation(): void
    {
        $categoryModel = new ProductCategoryModel();
        $categoryModel->insert([
            'name'       => 'Koin Emas',
            'slug'       => 'koin-emas',
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        $productModel = new ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(),
            'name'        => 'Koin Emas 1B',
            'nominal'     => '1B',
            'sell_price'  => 63000,
            'cost_price'  => 60000,
            'sort_order'  => 1,
            'is_active'   => 1,
        ]);

        $result = $this->get('/');
        $body = $result->getBody();

        $result->assertOK();
        self::assertStringContainsString('Ayong Store - Top Up Higgs Games Island', $body);
        self::assertStringContainsString('Pilih Kategori Produk', $body);
        self::assertStringContainsString('data-cat="koin-emas"', $body);
        self::assertStringContainsString('Koin Emas 1B', $body);
        self::assertStringContainsString('Rp63.000', $body);
    }

    public function testCatalogEmptyStateRemainsAvailable(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        self::assertStringContainsString('Ayong Store - Top Up Higgs Games Island', $result->getBody());
    }
}
