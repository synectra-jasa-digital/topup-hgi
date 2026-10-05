<?php

namespace Tests\Feature;

use App\Models\AdminModel;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Guard konsistensi otorisasi rute.
 *
 * Panel web sudah membatasi method pembayaran ke owner (metode-bayar), tapi
 * rute yang menyentuh uang lain hanya memakai filter 'auth':
 *   - bongkar-metode-pencairan = tujuan dana customer
 *   - bongkar-katalog           = base_rate penilaian kartu
 *   - voucher                   = nilai diskon & kuota
 *
 * Admin non-owner bisa mengubah ketiganya. Test ini mengunci owner-only.
 */
final class OwnerOnlyRouteCoverageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private const OWNER_ONLY_ROUTES = [
        '/admin/voucher',
        '/admin/voucher/tambah',
        '/admin/metode-bayar',
        '/admin/bongkar-metode-pencairan',
        '/admin/bongkar-metode-pencairan/tambah',
        '/admin/bongkar-katalog',
        '/admin/bongkar-katalog/tambah',
    ];

    private int $ownerId;
    private int $adminId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();

        $admins     = new AdminModel();
        $this->ownerId = (int) $admins->insert([
            'name' => 'Owner', 'email' => 'owner-rute@example.test',
            'password' => password_hash('x', PASSWORD_BCRYPT), 'role' => 'owner', 'is_active' => 1,
        ]);
        $this->adminId = (int) $admins->insert([
            'name' => 'Admin', 'email' => 'admin-rute@example.test',
            'password' => password_hash('x', PASSWORD_BCRYPT), 'role' => 'admin', 'is_active' => 1,
        ]);
    }

    public static function ownerOnlyRoutes(): array
    {
        $cases = [];
        foreach (self::OWNER_ONLY_ROUTES as $route) {
            $cases[$route] = [$route];
        }
        return $cases;
    }

    #[DataProvider('ownerOnlyRoutes')]
    public function testPlainAdminIsRedirectedAwayFromOwnerOnlyPage(string $route): void
    {
        $response = $this->withSession(['admin_id' => $this->adminId, 'admin_role' => 'admin'])
            ->get($route);

        $this->assertTrue(
            $response->isRedirect(),
            "{$route} seharusnya redirect untuk role admin"
        );
        $this->assertStringNotContainsString(
            $route,
            (string) $response->getHeaderLine('Location'),
            "{$route} mengarahkan admin ke halaman owner-only"
        );
    }

    #[DataProvider('ownerOnlyRoutes')]
    public function testOwnerCanStillReachThePage(string $route): void
    {
        $response = $this->withSession(['admin_id' => $this->ownerId, 'admin_role' => 'owner'])
            ->get($route);

        $this->assertFalse(
            $response->isRedirect(),
            "{$route} seharusnya bisa diakses owner"
        );
        $this->assertSame(200, $response->response()->getStatusCode(), "{$route} owner harus 200");
    }

    public function testPlainAdminCannotPostMoneyAffectingForms(): void
    {
        $writes = [
            '/admin/voucher/tambah',
            '/admin/bongkar-metode-pencairan/tambah',
            '/admin/bongkar-katalog/tambah',
        ];

        foreach ($writes as $route) {
            $response = $this->withSession(['admin_id' => $this->adminId, 'admin_role' => 'admin'])
                ->withBodyFormat('form')
                ->withHeaders([csrf_header() => csrf_hash()])
                ->post($route, []);

            $this->assertTrue(
                $response->isRedirect(),
                "POST {$route} seharusnya redirect untuk role admin"
            );
        }
    }
}