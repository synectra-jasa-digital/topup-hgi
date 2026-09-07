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
        
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    public function testCheckStatusFound()
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Cat', 'slug' => 'cat', 'is_active' => 1]);
        
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(),
            'name' => 'Prod', 'nominal' => '1', 'buy_price' => 10, 'sell_price' => 20, 'is_active' => 1
        ]);
        
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INV99999',
            'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Prod',
            'nominal_snapshot' => '1',
            'price_snapshot' => 20,
            'game_id' => '123',
            'whatsapp_number' => '0811111111',
            'total_amount' => 20,
            'status' => 'menunggu_pembayaran',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');

        $result = $this->call('post', 'cek-pesanan', [
            'invoice_number' => 'INV99999'
        ]);

        $result->assertRedirectTo('/pesanan/INV99999');
    }

    public function testCheckStatusNotFound()
    {
        $result = $this->withSession()->call('post', 'cek-pesanan', [
            'invoice_number' => 'INVKOSONG'
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('error'));
    }
}
