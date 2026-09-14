<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

final class AdminAuthorizationTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testAdminCannotAccessOwnerOnlyReport(): void
    {
        $result = $this->withSession([
            'admin_id' => 1,
            'admin_role' => 'admin',
        ])->get('/admin/laporan');

        $result->assertRedirectTo('/admin/dashboard');
    }
}
