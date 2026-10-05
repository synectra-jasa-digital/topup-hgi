<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use App\Models\VoucherModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Jalur uang kritis: verifikasi / penolakan / penyelesaian pembayaran.
 * Guard affectedRows === 1 pada OrderModel adalah pertahanan double-approve
 * dan double-commit voucher — test ini menguncinya.
 */
final class AdminPaymentVerificationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private const TOKEN = 'b1b2c3d4e5f6a7b8c9d0a1b2c3d4e5f6a7b8c9d0a1b2c3d4e5f6a7b8c9d0abcd';

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();
    }

    private function makeOrder(string $status, array $extra = []): int
    {
        $categories = new \App\Models\ProductCategoryModel();
        $categories->db->query('PRAGMA foreign_keys = OFF');
        $categories->insert(['name' => 'Cat', 'slug' => 'cat', 'is_active' => 1]);
        $products = new \App\Models\ProductModel();
        $products->insert([
            'category_id' => $categories->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 15000, 'is_active' => 1,
        ]);

        return (int) (new OrderModel())->insert($extra + [
            'invoice_number' => 'INV' . bin2hex(random_bytes(4)), 'product_id' => $products->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 15000,
            'game_id' => '987650432', 'whatsapp_number' => '0811111111', 'total_amount' => 15000,
            'status' => $status, 'public_access_token' => self::TOKEN,
            'payment_channel_type' => 'bank', 'payment_channel_name' => 'BCA Toko',
            'payment_account_number' => '5550001234', 'payment_account_holder' => 'Toko',
        ]);
    }

    private function postAs(string $uri, array $body = [], int $adminId = 7)
    {
        return $this->withSession(['admin_id' => $adminId, 'admin_role' => 'owner'])
            ->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post($uri, $body);
    }

    public function testVerifyMovesOrderFromMenungguVerifikasiToDiproses(): void
    {
        $id = $this->makeOrder('menunggu_verifikasi');

        $this->postAs('/admin/pesanan/' . $id . '/verifikasi')->assertRedirect();

        $order = (new OrderModel())->find($id);
        self::assertSame('diproses', $order['status']);
        self::assertSame(7, (int) $order['payment_verified_by']);
        self::assertNotEmpty($order['payment_verified_at']);
        self::assertNull($order['payment_rejection_reason']);
    }

    public function testSecondVerifyIsIgnoredAndDoesNotDoubleCommitVoucher(): void
    {
        $voucherId = (new VoucherModel())->insert([
            'code' => 'LOCKED', 'type' => 'nominal', 'value' => 1000,
            'min_purchase' => 0, 'quota' => 2, 'used_count' => 0,
            'reserved_count' => 1, 'is_active' => 1,
        ]);
        $id = $this->makeOrder('menunggu_verifikasi', [
            'voucher_id' => $voucherId, 'voucher_reserved' => 1, 'discount_amount' => 1000,
        ]);

        $this->postAs('/admin/pesanan/' . $id . '/verifikasi', [], 7)->assertRedirect();

        $afterFirst = (new VoucherModel())->find($voucherId);
        self::assertSame(1, (int) $afterFirst['used_count'], 'Verifikasi pertama harus commit reservasi voucher');
        self::assertSame(0, (int) $afterFirst['reserved_count']);

        // Verifikasi kedua oleh admin berbeda: guard harus menolak tanpa menyentuh apa pun.
        $this->postAs('/admin/pesanan/' . $id . '/verifikasi', [], 8)->assertRedirect();

        $order = (new OrderModel())->find($id);
        self::assertSame('diproses', $order['status']);
        self::assertSame(7, (int) $order['payment_verified_by'], 'Admin kedua tidak boleh menimpa verifier');

        $afterSecond = (new VoucherModel())->find($voucherId);
        self::assertSame(1, (int) $afterSecond['used_count'], 'Verifikasi kedua tidak boleh menghitung voucher dua kali');
    }

    public function testRejectWithoutReasonKeepsStatus(): void
    {
        $id = $this->makeOrder('menunggu_verifikasi');

        $this->postAs('/admin/pesanan/' . $id . '/tolak', ['reason' => ''])->assertRedirect();

        $order = (new OrderModel())->find($id);
        self::assertSame('menunggu_verifikasi', $order['status']);
        self::assertNull($order['payment_rejection_reason']);
    }

    public function testRejectReturnsOrderToMenungguPembayaranWithReason(): void
    {
        $id = $this->makeOrder('menunggu_verifikasi');

        $this->postAs('/admin/pesanan/' . $id . '/tolak', ['reason' => 'Bukti tidak terbaca'])->assertRedirect();

        $order = (new OrderModel())->find($id);
        self::assertSame('menunggu_pembayaran', $order['status']);
        self::assertSame('Bukti tidak terbaca', $order['payment_rejection_reason']);
        self::assertSame(7, (int) $order['payment_verified_by']);
    }

    public function testCompleteRequiresDiprosesAndThenMarksSelesai(): void
    {
        $pending = $this->makeOrder('menunggu_verifikasi');
        $this->postAs('/admin/pesanan/' . $pending . '/selesai')->assertRedirect();
        self::assertSame('menunggu_verifikasi', (new OrderModel())->find($pending)['status'], 'Pesanan belum diproses tidak boleh bisa diselesaikan');

        $id = $this->makeOrder('diproses');
        $this->postAs('/admin/pesanan/' . $id . '/selesai')->assertRedirect();

        $order = (new OrderModel())->find($id);
        self::assertSame('selesai', $order['status']);
        self::assertSame(7, (int) $order['processed_by']);
        self::assertNotEmpty($order['completed_at']);
    }

    public function testVerifyRequiresAdminLogin(): void
    {
        $id = $this->makeOrder('menunggu_verifikasi');

        $result = $this->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('/admin/pesanan/' . $id . '/verifikasi');

        $result->assertRedirect();
        self::assertSame('menunggu_verifikasi', (new OrderModel())->find($id)['status'], 'Tamu tidak boleh memverifikasi pembayaran');
    }
}
