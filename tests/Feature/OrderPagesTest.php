<?php

namespace Tests\Feature;

use App\Filters\RateLimitFilter;
use App\Models\OrderModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\App;

final class OrderPagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private const TOKEN = 'a1b2c3d4e5f6a7b8c9d0a1b2c3d4e5f6a7b8c9d0a1b2c3d4e5f6a7b8c9d0abcd';

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();
    }

    private function makeOrder(string $status, array $extra = []): void
    {
        $categories = new \App\Models\ProductCategoryModel();
        $categories->db->query('PRAGMA foreign_keys = OFF');
        $categories->insert(['name' => 'Cat', 'slug' => 'cat', 'is_active' => 1]);
        $products = new \App\Models\ProductModel();
        $products->insert([
            'category_id' => $categories->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 15000, 'is_active' => 1,
        ]);
        (new OrderModel())->insert($extra + [
            'invoice_number' => 'INV77777', 'product_id' => $products->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 15000,
            'game_id' => '987650432', 'whatsapp_number' => '0811111111', 'total_amount' => 15000,
            'status' => $status, 'public_access_token' => self::TOKEN,
            'payment_channel_type' => 'bank', 'payment_channel_name' => 'BCA Toko',
            'payment_account_number' => '5550001234', 'payment_account_holder' => 'Toko',
        ]);
    }

    public function testInvoiceShowsWhereTheOrderIsAndHowToPay(): void
    {
        $this->makeOrder('menunggu_pembayaran');

        $body = $this->get('/pesanan/INV77777?token=' . self::TOKEN)->getBody();

        // Progress: one current step among four, announced to assistive technology.
        self::assertStringContainsString('aria-label="Tahapan pesanan"', $body);
        self::assertSame(1, substr_count($body, 'aria-current="step"'));
        self::assertStringContainsString('(tahap saat ini)', $body);
        // Public page carries its own status styling (the admin-only badge classes are not loaded here).
        self::assertStringContainsString('ring-amber-300', $body);
        self::assertStringNotContainsString('badge-warning', $body);
        // Payment details can be copied, and the exact amount is shown.
        self::assertStringContainsString('data-copy="5550001234"', $body);
        self::assertStringContainsString('data-copy="15000"', $body);
        self::assertStringContainsString('Rp15.000', $body);
        // The private link is the way back to this page; Cek Pesanan only needs the invoice number.
        self::assertStringContainsString('Simpan tautan invoice', $body);
        self::assertStringContainsString('data-copy="' . base_url('pesanan/INV77777') . '?token=' . self::TOKEN . '"', $body);
        self::assertStringContainsString('data-copy="INV77777"', $body);
        self::assertStringNotContainsString('access-token-field', $body);
        self::assertStringNotContainsString('Salin token', $body);
        // Nothing may rely on inline event handlers (blocked when CSP is on in production).
        self::assertStringNotContainsString('onclick=', $body);
    }

    public function testInvoiceStaysPrivateEvenWithTheNewDetails(): void
    {
        $this->makeOrder('menunggu_pembayaran');

        $body = $this->get('/pesanan/INV77777?token=' . self::TOKEN)->getBody();

        self::assertStringNotContainsString('987650432', $body);
        self::assertStringNotContainsString('0811111111', $body);
    }

    public function testFailedOrderHasNoProgressTrackerAndExplainsWhatToDo(): void
    {
        $this->makeOrder('gagal');

        $body = $this->get('/pesanan/INV77777?token=' . self::TOKEN)->getBody();

        self::assertStringNotContainsString('aria-label="Tahapan pesanan"', $body);
        self::assertStringContainsString('gagal diproses', $body);
        self::assertStringContainsString('ring-rose-300', $body);
        self::assertStringNotContainsString('Unggah bukti pembayaran', $body);
    }

    public function testCompletedOrderMarksEveryStepDone(): void
    {
        $this->makeOrder('selesai');

        $body = $this->get('/pesanan/INV77777?token=' . self::TOKEN)->getBody();

        self::assertStringContainsString('aria-label="Tahapan pesanan"', $body);
        self::assertStringContainsString('ring-emerald-300', $body);
        self::assertSame(4, substr_count($body, '>check</span>'));
        self::assertStringContainsString('menandai pesanan ini selesai', $body);
    }

    public function testCheckStatusPageAsksOnlyForTheInvoiceNumber(): void
    {
        $body = $this->get('/cek-pesanan')->getBody();

        self::assertStringContainsString('Masukkan nomor invoice untuk melihat', $body);
        self::assertStringContainsString('name="invoice_number"', $body);
        self::assertStringNotContainsString('access_token', $body);
        self::assertStringNotContainsString('Token Akses', $body);
        // The old copy claimed a code had been sent to WhatsApp, which the system does not do at order time.
        self::assertStringNotContainsString('telah dikirim ke WhatsApp Anda', $body);
        self::assertStringNotContainsString('onclick=', $body);
    }

    public function testStatusPageShowsProgressWithoutPaymentDetails(): void
    {
        $this->makeOrder('diproses');

        $body = $this->get('/cek-pesanan/INV77777')->getBody();

        self::assertStringContainsString('aria-label="Tahapan pesanan"', $body);
        self::assertSame(1, substr_count($body, 'aria-current="step"'));
        self::assertStringContainsString('Pembayaran sudah diverifikasi', $body);
        self::assertStringNotContainsString('5550001234', $body);
        self::assertStringNotContainsString('data-copy', $body);
    }

    public function testPublicMessageViewIsFriendlyAndInIndonesian(): void
    {
        $html = view('errors/public_message', [
            'code'    => '429',
            'heading' => 'Terlalu banyak percobaan',
            'message' => 'Tunggu sekitar 30 detik lalu coba lagi.',
            'actions' => [['label' => 'Ke Beranda', 'href' => 'http://example.test/', 'primary' => true]],
        ]);

        self::assertStringContainsString('<html lang="id">', $html);
        self::assertStringContainsString('Terlalu banyak percobaan', $html);
        self::assertStringContainsString('Ke Beranda', $html);
        self::assertStringContainsString('noindex', $html);
    }

    public function testNotFoundPageDoesNotEchoTheFrameworkMessage(): void
    {
        $message = 'Can not find the route for /pesanan/INV1?token=SECRET';
        ob_start();
        include APPPATH . 'Views/errors/html/error_404.php';
        $html = (string) ob_get_clean();

        self::assertStringContainsString('Halaman tidak ditemukan', $html);
        self::assertStringContainsString('Cek Pesanan', $html);
        self::assertStringNotContainsString('SECRET', $html);
        self::assertStringNotContainsString('Can not find', $html);
    }

    private function requestAccepting(string $accept, bool $ajax): IncomingRequest
    {
        $config  = new App();
        $request = new IncomingRequest($config, new SiteURI($config, 'cek-pesanan'), 'php://input', new UserAgent());
        $request->setHeader('Accept', $accept);
        if ($ajax) {
            $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        }

        return $request;
    }

    public function testRateLimitAnswersBrowsersWithAPageAndScriptsWithJson(): void
    {
        $filter = new RateLimitFilter();

        $page = $filter->tooManyRequests($this->requestAccepting('text/html,application/xhtml+xml', false), 30);
        self::assertSame(429, $page->getStatusCode());
        self::assertSame('30', $page->getHeaderLine('Retry-After'));
        self::assertStringContainsString('Terlalu banyak percobaan', (string) $page->getBody());
        self::assertStringContainsString('sekitar 30 detik', (string) $page->getBody());
        self::assertStringNotContainsString('"error"', (string) $page->getBody());

        $json = $filter->tooManyRequests($this->requestAccepting('application/json', true), 12);
        self::assertSame(429, $json->getStatusCode());
        self::assertSame('12', $json->getHeaderLine('Retry-After'));
        self::assertStringContainsString('"error"', (string) $json->getBody());
    }
}
