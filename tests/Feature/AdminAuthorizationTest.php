<?php

namespace Tests\Feature;

use App\Models\AdminModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class AdminAuthorizationTest extends CIUnitTestCase
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

    public function testAdminCannotAccessOwnerOnlyReport(): void
    {
        // AuthFilter memvalidasi ulang baris admins, jadi session harus
        // menunjuk admin aktif yang benar-benar ada di database.
        $adminId = (int) (new AdminModel())->insert([
            'name'      => 'Admin Biasa',
            'email'     => 'admin-otorisasi@example.test',
            'password'  => password_hash('tidak-dipakai', PASSWORD_BCRYPT),
            'role'      => 'admin',
            'is_active' => 1,
        ]);

        $result = $this->withSession([
            'admin_id' => $adminId,
            'admin_role' => 'admin',
        ])->get('/admin/laporan');

        $result->assertRedirectTo('/admin/dashboard');
    }
}