<?php

namespace Tests\Unit;

use App\Models\BongkarCatalogModel;
use App\Models\BongkarPayoutMethodModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class BongkarAdminModelsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate  = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    // Mirrors Admin\BongkarCatalogController::update(): $data['id'] = $id; $model->save($data);
    public function testBongkarCatalogSaveWithIdInPayloadDoesNotThrow(): void
    {
        $catalogs = new BongkarCatalogModel();
        $id = $catalogs->insert([
            'code'       => 'KARTU-TEST',
            'name'       => 'Kartu Test',
            'unit_label' => 'kartu',
            'base_rate'  => 50000,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);
        $this->assertNotFalse($id, 'Insert awal gagal.');

        $saved = $catalogs->save([
            'id'         => $id,
            'code'       => 'KARTU-TEST',
            'name'       => 'Kartu Test Updated',
            'unit_label' => 'kartu',
            'base_rate'  => 55000,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        $this->assertTrue($saved, 'Save gagal: ' . implode(', ', $catalogs->errors() ?? []));
        $this->assertSame('Kartu Test Updated', $catalogs->find($id)['name']);
    }

    // Mirrors Admin\BongkarPayoutMethodController::update(): $data['id'] = $id; $model->save($data);
    public function testBongkarPayoutMethodSaveWithIdInPayloadDoesNotThrow(): void
    {
        $methods = new BongkarPayoutMethodModel();
        $id = $methods->insert([
            'code'       => 'TESTBANK',
            'name'       => 'Test Bank',
            'category'   => 'bank',
            'sort_order' => 1,
            'is_active'  => 1,
        ]);
        $this->assertNotFalse($id, 'Insert awal gagal.');

        $saved = $methods->save([
            'id'         => $id,
            'code'       => 'TESTBANK',
            'name'       => 'Test Bank Updated',
            'category'   => 'ewallet',
            'sort_order' => 2,
            'is_active'  => 1,
        ]);

        $this->assertTrue($saved, 'Save gagal: ' . implode(', ', $methods->errors() ?? []));
        $this->assertSame('ewallet', $methods->find($id)['category']);
    }
}
