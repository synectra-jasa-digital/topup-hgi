<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class MidtransWebhookTest extends CIUnitTestCase
{
    use FeatureTestTrait, DatabaseTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->db = db_connect('tests');
        
        // Migrate required tables
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    public function testInvalidSignatureRejected()
    {
        $payload = [
            'order_id' => 'INV123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'wrong_signature'
        ];

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($payload))
                       ->post('webhook/midtrans');

        $result->assertStatus(403);
    }

    public function testValidSignatureUpdatesOrder()
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF'); // Disable FK explicitly for SQLite in testing
        
        $categoryModel->insert([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => 1
        ]);
        $categoryId = $categoryModel->getInsertID();

        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryId,
            'name' => 'Koin Emas 1B',
            'nominal' => '1B',
            'buy_price' => 50000,
            'sell_price' => 60000,
            'is_active' => 1
        ]);
        $productId = $productModel->getInsertID();

        $orderModel = new OrderModel();
        
        $order = [
            'invoice_number' => 'INVTEST123',
            'product_id' => $productId,
            'product_name_snapshot' => 'Koin Emas 1B',
            'nominal_snapshot' => '1B',
            'price_snapshot' => 60000,
            'game_id' => '1234567',
            'whatsapp_number' => '08123456789',
            'total_amount' => 60000,
            'status' => 'menunggu_pembayaran',
        ];
        
        $orderModel->insert($order);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');
        
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        
        $payload = [
            'order_id' => 'INVTEST123',
            'status_code' => '200',
            'gross_amount' => '60000.00',
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
            'transaction_id' => 'TEST-TRANS-123'
        ];
        
        $payload['signature_key'] = hash("sha512", 'INVTEST123' . '200' . '60000.00' . $serverKey);

        $result = $this->withBodyFormat('json')
                       ->withBody(json_encode($payload))
                       ->post('webhook/midtrans');

        $result->assertStatus(200);
        
        $updatedOrder = $orderModel->findByInvoice('INVTEST123');
        $this->assertEquals('diproses', $updatedOrder['status']);
        
        $orderPaymentModel = new OrderPaymentModel();
        $payment = $orderPaymentModel->where('order_id', $updatedOrder['id'])->first();
        $this->assertNotNull($payment);
        $this->assertNotNull($payment['paid_at']);
    }

    public function testValidSignatureWithMismatchedAmountIsRejected(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Amount Category', 'slug' => 'amount-category', 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Amount Product',
            'nominal' => '1', 'buy_price' => 50000, 'sell_price' => 60000, 'is_active' => 1,
        ]);
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INVMISMATCH', 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Amount Product', 'nominal_snapshot' => '1',
            'price_snapshot' => 60000, 'game_id' => '1234567', 'whatsapp_number' => '08123456789',
            'total_amount' => 60000, 'status' => 'menunggu_pembayaran',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        $payload = [
            'order_id' => 'INVMISMATCH', 'status_code' => '200', 'gross_amount' => '1.00',
            'transaction_status' => 'settlement', 'transaction_id' => 'MISMATCH-TRANS',
        ];
        $payload['signature_key'] = hash('sha512', 'INVMISMATCH2001.00' . $serverKey);
        $result = $this->withBodyFormat('json')->withBody(json_encode($payload))->post('webhook/midtrans');
        $result->assertStatus(422);
        $this->assertSame('menunggu_pembayaran', $orderModel->findByInvoice('INVMISMATCH')['status']);
    }

    public function testFinalOrderStatusCannotBeRevertedByWebhook(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Final Category', 'slug' => 'final-category', 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Final Product',
            'nominal' => '1', 'buy_price' => 50000, 'sell_price' => 60000, 'is_active' => 1,
        ]);
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INVFINAL', 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Final Product', 'nominal_snapshot' => '1',
            'price_snapshot' => 60000, 'game_id' => '1234567', 'whatsapp_number' => '08123456789',
            'total_amount' => 60000, 'status' => 'selesai',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        $payload = [
            'order_id' => 'INVFINAL', 'status_code' => '200', 'gross_amount' => '60000.00',
            'transaction_status' => 'cancel', 'transaction_id' => 'FINAL-TRANS',
        ];
        $payload['signature_key'] = hash('sha512', 'INVFINAL20060000.00' . $serverKey);
        $result = $this->withBodyFormat('json')->withBody(json_encode($payload))->post('webhook/midtrans');
        $result->assertOK();
        $this->assertSame('selesai', $orderModel->findByInvoice('INVFINAL')['status']);
    }

    public function testDuplicateWebhookIsIdempotent(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Duplicate Category', 'slug' => 'duplicate-category', 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Duplicate Product',
            'nominal' => '1', 'buy_price' => 50000, 'sell_price' => 60000, 'is_active' => 1,
        ]);
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INVDUPLICATE', 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Duplicate Product', 'nominal_snapshot' => '1',
            'price_snapshot' => 60000, 'game_id' => '1234567', 'whatsapp_number' => '08123456789',
            'total_amount' => 60000, 'status' => 'menunggu_pembayaran',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        $payload = [
            'order_id' => 'INVDUPLICATE', 'status_code' => '200', 'gross_amount' => '60000.00',
            'transaction_status' => 'settlement', 'transaction_id' => 'DUPLICATE-TRANS',
        ];
        $payload['signature_key'] = hash('sha512', 'INVDUPLICATE20060000.00' . $serverKey);
        $body = json_encode($payload);

        $first = $this->withBodyFormat('json')->withBody($body)->post('webhook/midtrans');
        $second = $this->withBodyFormat('json')->withBody($body)->post('webhook/midtrans');

        $first->assertOK();
        $second->assertOK();
        $orderId = $orderModel->findByInvoice('INVDUPLICATE')['id'];
        $this->assertSame(1, $this->db->table('order_payments')->where('order_id', $orderId)->countAllResults());
    }
}
