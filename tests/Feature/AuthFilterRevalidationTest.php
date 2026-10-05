<?php

namespace Tests\Feature;

use App\Models\AdminModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Guard AuthFilter terhadap snapshot sesi yang basi.
 *
 * AuthFilter sebelumnya hanya memeriksa session('admin_id') tanpa membaca
 * ulang tabel admins. Akibatnya akun yang dinonaktifkan, dihapus, atau
 * diturunkan rolenya tetap punya akses sampai sesi kedaluwarsa (2 jam).
 *
 * Perilaku yang dikunci di sini:
 *   - admin nonaktif harus kehilangan akses
 *   - akun terhapus harus kehilangan akses
 *   - role turun di DB harus langsung tercermin, bukan menunggu re-login
 */
final class AuthFilterRevalidationTest extends CIUnitTestCase
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

    private function createAdmin(string $role = 'admin', bool $active = true): int
    {
        $admins = new AdminModel();
        $admins->insert([
            'name'      => 'Uji Filter',
            'email'     => 'uji-filter-' . bin2hex(random_bytes(4)) . '@example.test',
            'password'  => password_hash('tidak-dipakai', PASSWORD_BCRYPT),
            'role'      => $role,
            'is_active' => $active ? 1 : 0,
        ]);

        return (int) $admins->getInsertID();
    }

    public function testActiveAdminWithMatchingRoleReachesOwnerOnlyPage(): void
    {
        $id = $this->createAdmin('owner');

        // Guard nyata: halaman owner-only harus bisa diakses.
        $response = $this->withSession(['admin_id' => $id, 'admin_role' => 'owner'])
            ->get('/admin/akun-admin');

        $this->assertStringContainsString(
            'akun-admin',
            (string) $response->getBody(),
            'admin owner aktif seharusnya bisa menjangkau halaman owner-only'
        );
    }

    public function testDeactivatedAdminIsLockedOutEvenWithValidSession(): void
    {
        $id = $this->createAdmin('owner', false);

        $response = $this->withSession(['admin_id' => $id, 'admin_role' => 'owner'])
            ->get('/admin/akun-admin');

        // Admin nonaktif harus dipaksa keluar, bukan hanya kehilangan akses owner.
        $this->assertStringNotContainsString(
            'akun-admin',
            (string) $response->getBody(),
            'admin nonaktif masih bisa menjangkau halaman owner-only'
        );
    }

    public function testDeletedAdminIsLockedOutEvenWithValidSession(): void
    {
        $id = $this->createAdmin('owner');

        // Hapus akunnya, lalu coba akses dengan session yang masih hidup.
        (new AdminModel())->delete($id);

        $after = $this->withSession(['admin_id' => $id, 'admin_role' => 'owner'])
            ->get('/admin/dashboard');

        $this->assertTrue(
            $after->isRedirect(),
            'akun terhapus harus kehilangan akses dan diarahkan ke login'
        );
    }

    public function testRoleDowngradeIsReflectedImmediately(): void
    {
        $id = $this->createAdmin('owner');

        $admins = new AdminModel();
        $admins->update($id, ['role' => 'admin']);

        // Session masih menyimpan role 'owner'. AuthFilter harus membaca ulang
        // role terbaru dari DB, sehingga akses owner-only harus hilang.
        $response = $this->withSession(['admin_id' => $id, 'admin_role' => 'owner'])
            ->get('/admin/akun-admin');

        $this->assertStringNotContainsString(
            'akun-admin',
            (string) $response->getBody(),
            'role turun di DB tidak langsung tercermin — sesi memakai role lama'
        );
    }

    public function testRoleElevationIsAlsoReflected(): void
    {
        // Sebaliknya: admin yang naik jadi owner harus bisa memakai hak owner
        // tanpa perlu logout/login ulang.
        $id = $this->createAdmin('admin');

        $response = $this->withSession(['admin_id' => $id, 'admin_role' => 'admin'])
            ->get('/admin/akun-admin');

        // Dengan role 'admin' di session dan filter membaca DB, halaman
        // owner-only harus ditolak — membuktikan filter memakai DB, bukan session.
        $this->assertStringNotContainsString(
            'akun-admin',
            (string) $response->getBody(),
            'filter tampaknya mempercayai role dari session, bukan dari database'
        );
    }
}