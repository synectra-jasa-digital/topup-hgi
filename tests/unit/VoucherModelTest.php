<?php

namespace Tests\Unit;

use App\Models\VoucherModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class VoucherModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $refresh     = true;
    protected VoucherModel $vouchers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
        $this->vouchers = new VoucherModel();
    }

    // 1. Insert voucher valid & cek ada di DB
    public function testInsertAndFind(): void
    {
        $code = 'TEST' . time();
        $id   = $this->vouchers->insert([
            'code'         => $code,
            'type'         => 'percentage',
            'value'        => 10,
            'max_discount' => 20000,
            'min_purchase' => 0,
            'quota'        => 5,
            'start_date'   => date('Y-m-d'),
            'end_date'     => date('Y-m-d', strtotime('+30 days')),
            'is_active'    => 1,
        ]);

        $this->assertNotFalse($id, 'Insert gagal.');
        $row = $this->vouchers->find($id);
        $this->assertEquals($code, $row['code']);
        $this->assertEquals('percentage', $row['type']);
    }

    // 2. Validasi: kode duplikat harus gagal
    public function testDuplicateCodeFails(): void
    {
        $code = 'DUPTEST' . time();
        $this->vouchers->insert([
            'code'       => $code,
            'type'       => 'nominal',
            'value'      => 5000,
            'min_purchase'=> 0,
            'is_active'  => 1,
        ]);

        $result = $this->vouchers->insert([
            'code'       => $code,
            'type'       => 'nominal',
            'value'      => 5000,
            'min_purchase'=> 0,
            'is_active'  => 1,
        ]);

        // DB unique constraint -> insert harus gagal / return false
        $this->assertFalse($result, 'Kode duplikat seharusnya gagal.');
    }

    // 3. Update is_active
    public function testToggleActive(): void
    {
        $id = $this->vouchers->insert([
            'code'       => 'TOGGLE' . time(),
            'type'       => 'nominal',
            'value'      => 1000,
            'min_purchase'=> 0,
            'is_active'  => 1,
        ]);

        $this->vouchers->update($id, ['is_active' => 0]);
        $row = $this->vouchers->find($id);
        $this->assertEquals(0, (int) $row['is_active']);
    }

    // 4. Delete voucher
    public function testDelete(): void
    {
        $id = $this->vouchers->insert([
            'code'       => 'DEL' . time(),
            'type'       => 'nominal',
            'value'      => 500,
            'min_purchase'=> 0,
            'is_active'  => 1,
        ]);

        $this->vouchers->delete($id);
        $this->assertNull($this->vouchers->find($id));
    }
}
