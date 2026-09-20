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

    private function makeOrder(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Cat', 'slug' => 'cat', 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 20, 'is_active' => 1,
        ]);
        (new OrderModel())->insert([
            'invoice_number' => 'INV99999', 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 20,
            'game_id' => '987650432', 'whatsapp_number' => '0811111111', 'total_amount' => 20,
            'status' => 'menunggu_pembayaran', 'public_access_token' => str_repeat('a', 64),
            'payment_channel_type' => 'bank', 'payment_channel_name' => 'BCA Toko',
            'payment_account_number' => '5550001234', 'payment_account_holder' => 'Toko',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');
    }

    public function testCheckStatusNeedsOnlyTheInvoiceNumberAndShowsNothingPersonal(): void
    {
        $this->makeOrder();

        // Lookup by invoice number alone (a stray space and lower case are tolerated).
        $result = $this->call('post', 'cek-pesanan', [csrf_token() => csrf_hash(), 'invoice_number' => ' inv99999 ']);
        $result->assertRedirectTo('/cek-pesanan/INV99999');

        $status = $this->get('/cek-pesanan/INV99999');
        $status->assertOK();
        $status->assertHeader('Cache-Control');
        $body = $status->getBody();
        self::assertStringContainsString('INV99999', $body);
        self::assertStringContainsString('Menunggu Pembayaran', $body);
        // The public page must not leak what the private invoice protects.
        self::assertStringNotContainsString('987650432', $body);
        self::assertStringNotContainsString('0811111111', $body);
        self::assertStringNotContainsString('5550001234', $body);
        self::assertStringNotContainsString(str_repeat('a', 64), $body);
        self::assertStringNotContainsString('Unggah bukti pembayaran', $body);
    }

    public function testFullInvoiceStillNeedsTheAccessToken(): void
    {
        $this->makeOrder();

        $invoice = $this->get('/pesanan/INV99999?token=' . str_repeat('a', 64));
        $invoice->assertOK();
        $invoice->assertHeader('Cache-Control');
        // Even the private invoice masks the game ID and phone number.
        self::assertStringNotContainsString('987650432', $invoice->getBody());
        self::assertStringNotContainsString('0811111111', $invoice->getBody());

        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('/pesanan/INV99999?token=' . str_repeat('b', 64));
    }

    public function testStatusPageOfAnUnknownInvoiceIsNotFound(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('/cek-pesanan/INVDOESNOTEXIST');
    }

    public function testCheckStatusNotFound(): void
    {
        $result = $this->withSession()->call('post', 'cek-pesanan', [csrf_token() => csrf_hash(), 'invoice_number' => 'INVKOSONG']);
        $result->assertRedirect();
        $this->assertTrue(session()->has('error'));
    }

    public function testCheckStatusRequiresAnInvoiceNumber(): void
    {
        $result = $this->withSession()->call('post', 'cek-pesanan', [csrf_token() => csrf_hash(), 'invoice_number' => '']);
        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));
    }
}
