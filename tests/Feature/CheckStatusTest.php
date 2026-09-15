<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class CheckStatusTest extends CIUnitTestCase
{
    use FeatureTestTrait, DatabaseTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrations = \Config\Services::migrations();
        $migrations->setNamespace('App')->setGroup('tests')->latest();
    }

    public function testCheckStatusFound(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Cat', 'slug' => 'cat', 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 20, 'is_active' => 1,
        ]);
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INV99999', 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 20,
            'game_id' => '123', 'whatsapp_number' => '0811111111', 'total_amount' => 20,
            'status' => 'menunggu_pembayaran', 'public_access_token' => str_repeat('a', 64),
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');

        $result = $this->call('post', 'cek-pesanan', [csrf_token() => csrf_hash(), 'invoice_number' => 'INV99999', 'access_token' => str_repeat('a', 64)]);
        $result->assertRedirectTo('/pesanan/INV99999?token=' . str_repeat('a', 64));
        $invoice = $this->get('/pesanan/INV99999?token=' . str_repeat('a', 64));
        $invoice->assertOK();
        $invoice->assertHeader('Cache-Control');
        $this->assertStringNotContainsString('123', $invoice->getBody());
        $this->assertStringNotContainsString('0811111111', $invoice->getBody());
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('/pesanan/INVDOESNOTEXIST?token=' . str_repeat('a', 64));
    }

    public function testCheckStatusNotFound(): void
    {
        $result = $this->withSession()->call('post', 'cek-pesanan', [csrf_token() => csrf_hash(), 'invoice_number' => 'INVKOSONG', 'access_token' => str_repeat('a', 64)]);
        $result->assertRedirect();
        $this->assertTrue(session()->has('error'));
    }
}
