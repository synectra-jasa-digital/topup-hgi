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

    public function testLoginPageIsOneSimpleColumn(): void
    {
        $body = $this->get('/admin/login')->getBody();

        self::assertSame(1, substr_count($body, '<main'));
        self::assertStringContainsString('max-w-sm', $body);
        // No multi-column layout and no promo artwork: only the heading, the form and a way back to the store.
        self::assertStringNotContainsString('lg:grid-cols', $body);
        self::assertStringNotContainsString('Banner promo toko', $body);
        self::assertStringContainsString('Kembali ke halaman toko', $body);
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
