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
        self::assertStringContainsString('Ayong Store - Top Up Koin Emas Higgs Domino &amp; Global Murah 24 Jam', $body);
        self::assertStringContainsString('Pilih Kategori Produk', $body);
        self::assertStringContainsString('data-cat="koin-emas"', $body);
        self::assertStringContainsString('Koin Emas 1B', $body);
        self::assertStringContainsString('Rp63.000', $body);
    }

    public function testCatalogEmptyStateRemainsAvailable(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        self::assertStringContainsString('Ayong Store - Top Up Koin Emas Higgs Domino &amp; Global Murah 24 Jam', $result->getBody());
    }

    public function testCatalogDoesNotShipAHardcodedVoucherOrUnbackedClaims(): void
    {
        $body = $this->get('/')->getBody();

        // The voucher is validated by the server at checkout, so no code or discount may be baked into the page.
        self::assertStringNotContainsString("=== 'AYONGHEMAT'", $body);
        self::assertStringNotContainsString('btn-sample-coupon', $body);
        self::assertStringNotContainsString('state.discount', $body);
        // Payment is verified by an admin, so the page must not promise instant or guaranteed results.
        self::assertStringNotContainsString('Garansi 100%', $body);
        self::assertStringNotContainsString('LIVE RATE', $body);
        self::assertStringNotContainsString('Proses instan otomatis', $body);
        self::assertStringNotContainsString('Proses 1 Detik', $body);
        self::assertStringNotContainsString('Bebas Banned', $body);
        self::assertStringNotContainsString('Harga Termurah', $body);
        // Footer and structured data: no invented payment badges, guarantees or always-on claims.
        self::assertStringNotContainsString('Sistem Otomatis Instan', $body);
        self::assertStringNotContainsString('Garansi Resmi', $body);
        self::assertStringNotContainsString('Sistem Terenkripsi', $body);
        self::assertStringNotContainsString('Virtual Account', $body);
        self::assertStringNotContainsString('GoPay', $body);
        self::assertStringNotContainsString('BCA VA', $body);
        // Nothing may point at a WhatsApp link without a number.
        self::assertStringNotContainsString('href="https://wa.me/"', $body);
    }

    public function testCatalogExplainsEmptyStatesInsteadOfShowingNothing(): void
    {
        $body = $this->get('/')->getBody();

        self::assertStringContainsString('Belum ada produk yang dibuka untuk dibeli', $body);
        self::assertStringContainsString('Belum ada metode pembayaran yang aktif', $body);
        self::assertStringContainsString('Layanan Bongkar Belum Tersedia', $body);
    }

    public function testDialogsAreAccessibleAndSitAboveTheMobileBars(): void
    {
        $body = $this->get('/')->getBody();

        foreach (['checkout-modal-title', 'guide-modal-title'] as $titleId) {
            self::assertStringContainsString('aria-labelledby="' . $titleId . '"', $body);
            self::assertStringContainsString('id="' . $titleId . '"', $body);
        }
        self::assertSame(2, substr_count($body, 'role="dialog" aria-modal="true"'));
        // The mobile bars are z-998/999, so the dialogs must be above them.
        self::assertSame(2, substr_count($body, 'z-[1100]'));
        // Keyboard users can leave a dialog: the script must handle Escape for it.
        self::assertStringContainsString("e.key === 'Escape'", $body);
        // No blocking browser dialogs: messages go through the toast instead.
        self::assertDoesNotMatchRegularExpression('/(?<![.\w])alert\s*\(\s*[\'"`]/', $body);
    }

    public function testAnnouncementTickerIsFullWidthAndNotReadTwice(): void
    {
        (new \App\Models\AnnouncementModel())->insert(['message' => 'Bayar lewat transfer bank atau QRIS.', 'sort_order' => 1, 'is_active' => 1]);

        $body = $this->get('/')->getBody();

        self::assertStringContainsString('aria-label="Info toko"', $body);
        // The bar has no label or button of its own, and the looping copy is hidden from screen readers.
        self::assertStringNotContainsString('ticker-toggle', $body);
        self::assertStringNotContainsString('btn-coverflow-toggle', $body);
        self::assertSame(2, substr_count($body, 'Bayar lewat transfer bank atau QRIS.'));
        self::assertSame(1, substr_count($body, '<ul class="flex shrink-0 items-center gap-x-4 pr-4" aria-hidden="true">'));
        // No unverifiable "official" badge, and no colored status dots.
        self::assertStringNotContainsString('INFO RESMI', $body);
        self::assertStringNotContainsString("bg-amber-400' : 'bg-emerald-400", $body);
    }
}
