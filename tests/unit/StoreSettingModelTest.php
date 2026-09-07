<?php

namespace Tests\Unit;

use App\Models\StoreSettingModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class StoreSettingModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $refresh = false;
    protected StoreSettingModel $settings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settings = new StoreSettingModel();
    }

    public function testSetAndGetVal(): void
    {
        $this->settings->setVal('test_key', 'halo dunia');
        $this->assertEquals('halo dunia', $this->settings->getVal('test_key'));
    }

    public function testUpdateExistingKey(): void
    {
        $this->settings->setVal('test_key_update', 'nilai1');
        $this->settings->setVal('test_key_update', 'nilai2');
        $this->assertEquals('nilai2', $this->settings->getVal('test_key_update'));
    }

    public function testDefaultWhenMissing(): void
    {
        $this->assertEquals('default', $this->settings->getVal('kunci_tidak_ada', 'default'));
    }

    public function testBatchSave(): void
    {
        $this->settings->batchSave([
            'store_name'    => 'Ayong Store Test',
            'store_contact' => '08123456789',
        ]);
        $this->assertEquals('Ayong Store Test', $this->settings->getVal('store_name'));
        $this->assertEquals('08123456789', $this->settings->getVal('store_contact'));
    }
}
