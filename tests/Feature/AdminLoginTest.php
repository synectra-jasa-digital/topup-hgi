<?php

namespace Tests\Feature;

use App\Models\AdminModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class AdminLoginTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();
    }

    public function testLoginPageNeedsNoThirdPartyScriptAndIsUsableWithAKeyboard(): void
    {
        $result = $this->get('/admin/login');
        $body = $result->getBody();

        $result->assertOK();
        self::assertStringContainsString('Masuk ke panel admin', $body);
        self::assertStringContainsString('name="email"', $body);
        self::assertStringContainsString('autocomplete="current-password"', $body);
        // The show-password button reports its state and is a proper touch target.
        self::assertStringContainsString('aria-pressed="false"', $body);
        self::assertStringContainsString('h-11 w-11', $body);
        // Errors are shown inline, so no popup library has to be fetched from a CDN.
        self::assertStringNotContainsString('sweetalert', strtolower($body));
        self::assertStringNotContainsString('cdn.jsdelivr.net', $body);
        // Sign-in pages stay out of search results, and the title uses no em dash.
        self::assertStringContainsString('noindex', $body);
        self::assertStringContainsString('<title>Masuk Panel Admin - ', $body);
        self::assertStringNotContainsString('—', $body);
        // Nothing may rely on inline event handlers (blocked when CSP is on in production).
        self::assertStringNotContainsString('onclick=', $body);
        self::assertStringNotContainsString('onload=', $body);
    }

    public function testLoginPageShowsTheStoresActivePromoBannerOnLargeScreensOnly(): void
    {
        $categories = new \App\Models\BannerCategoryModel();
        $categories->db->query('PRAGMA foreign_keys = OFF');
        $categories->insert(['name' => 'Promo', 'is_active' => 1]);
        (new \App\Models\BannerModel())->insert([
            'banner_category_id' => $categories->getInsertID(),
            'image_path' => 'assets/uploads/banners/promo-test.jpg',
            'sort_order' => 1, 'is_active' => 1,
        ]);

        $body = $this->get('/admin/login')->getBody();

        self::assertStringContainsString('alt="Banner promo toko"', $body);
        self::assertStringContainsString('assets/uploads/banners/promo-test.jpg', $body);
        // On a phone the form must stay right under the heading, so the banner is desktop only.
        self::assertMatchesRegularExpression('/class="hidden [^"]*lg:block"[^>]*>\s*<picture>/', $body);
    }

    public function testLoginPageWithoutABannerCentersTheHeadingInsteadOfLeavingAGap(): void
    {
        $body = $this->get('/admin/login')->getBody();

        self::assertStringNotContainsString('alt="Banner promo toko"', $body);
        self::assertStringContainsString('lg:justify-center', $body);
        self::assertStringNotContainsString('lg:justify-between', $body);
    }

    public function testRejectedSignInIsShownInlineWithoutSayingWhichFieldWasWrong(): void
    {
        $post = $this->withSession()->call('post', 'admin/login', [
            csrf_token() => csrf_hash(), 'email' => 'nobody@example.com', 'password' => 'wrong-password',
        ]);
        $post->assertRedirect();
        self::assertSame('Email atau kata sandi salah.', session()->getFlashdata('error'));

        // What the next request sees after the redirect: a flash message marked as carried over.
        $page = $this->withSession([
            'error'    => 'Email atau kata sandi salah.',
            '__ci_vars' => ['error' => 'old'],
        ])->get('/admin/login')->getBody();

        self::assertStringContainsString('role="alert"', $page);
        self::assertStringContainsString('Email atau kata sandi salah.', $page);
    }

    public function testCorrectCredentialsSignInAndGoToTheDashboard(): void
    {
        (new AdminModel())->insert([
            'name' => 'Owner', 'email' => 'owner@example.com', 'role' => 'owner', 'is_active' => 1,
            'password' => password_hash('correct-horse', PASSWORD_DEFAULT),
        ]);

        $result = $this->withSession()->call('post', 'admin/login', [
            csrf_token() => csrf_hash(), 'email' => 'owner@example.com', 'password' => 'correct-horse',
        ]);

        $result->assertRedirectTo('/admin/dashboard');
        self::assertSame('owner', session()->get('admin_role'));
    }

    public function testSigningInIsRateLimitedOnBothLoginPaths(): void
    {
        $collector = new \CodeIgniter\Commands\Utilities\Routes\FilterCollector();

        foreach (['admin/login', 'login'] as $path) {
            $before  = $collector->get('POST', $path)['before'];
            $limited = array_filter($before, static fn (string $filter): bool => str_starts_with($filter, 'ratelimit'));
            self::assertNotEmpty($limited, 'POST ' . $path . ' must be rate limited, got: ' . implode(', ', $before));
        }
    }
}
