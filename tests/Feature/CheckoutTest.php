<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class CheckoutTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use \CodeIgniter\Test\FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect("tests");
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace("App")->setGroup("tests");
        $migrate->latest();
    }

    public function testDoubleSubmitCheckoutCreatesOnlyOneOrder(): void
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query("PRAGMA foreign_keys = OFF");
        $categoryModel->insert(["name" => "Test Category", "slug" => "test-category", "is_active" => 1]);
        $productModel = new \App\Models\ProductModel();
        $productId = $productModel->insert([
            "category_id" => $categoryModel->getInsertID(),
            "name" => "Test Product",
            "nominal" => "1",
            "cost_price" => 10000,
            "sell_price" => 20000,
            "is_active" => 1,
        ]);
        $categoryModel->db->query("PRAGMA foreign_keys = ON");

        $paymentChannelId = (new \App\Models\PaymentChannelModel())->insert([
            'type' => 'bank', 'name' => 'BCA', 'account_number' => '1234567890',
            'account_holder' => 'Ayong Store', 'is_active' => 1,
        ]);

        $this->get("/checkout/" . $productId);
        $idempotencyToken = session("checkout_idempotency_token");
        $this->assertNotEmpty($idempotencyToken);

        $result1 = $this->withSession()
            ->withBodyFormat("form")
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("/checkout/" . $productId, [
                "game_id" => "testgame",
                "whatsapp_number" => "08123456789",
                "idempotency_token" => $idempotencyToken,
                "voucher_code" => "",
                "payment_channel_id" => $paymentChannelId,
            ]);
        $result1->assertRedirect();

        $result2 = $this->withSession()
            ->withBodyFormat("form")
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post("/checkout/" . $productId, [
                "game_id" => "testgame",
                "whatsapp_number" => "08123456789",
                "idempotency_token" => $idempotencyToken,
                "voucher_code" => "",
                "payment_channel_id" => $paymentChannelId,
            ]);
        $result2->assertRedirect();

        $orderModel = new OrderModel();
        $orders = $orderModel->where("idempotency_token", $idempotencyToken)->findAll();
        $this->assertCount(1, $orders);
        $this->assertEquals($idempotencyToken, $orders[0]["idempotency_token"]);


    }
}
