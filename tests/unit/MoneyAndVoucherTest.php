<?php

namespace Tests\Unit;

use App\Libraries\Money;
use App\Models\VoucherModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class MoneyAndVoucherTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrations = \Config\Services::migrations();
        $migrations->setNamespace('App')->setGroup('tests');
        $migrations->latest();
    }

    public function testPercentageDiscountRoundsHalfUpAndNeverExceedsSubtotal(): void
    {
        $this->assertSame(51, Money::percentage(101, 50));
        $this->assertSame(101, (new VoucherModel())->calculateDiscount([
            'type' => 'percentage', 'value' => 99.9, 'max_discount' => null,
        ], 101));
    }

    public function testVoucherReservationUsesUsedAndReservedQuota(): void
    {
        $vouchers = new VoucherModel();
        $id = $vouchers->insert([
            'code' => 'RESERVE' . time(), 'type' => 'nominal', 'value' => 1000,
            'min_purchase' => 0, 'quota' => 1, 'used_count' => 0,
            'reserved_count' => 0, 'is_active' => 1,
        ]);

        $this->assertTrue($vouchers->reserve((int) $id));
        $this->assertFalse($vouchers->reserve((int) $id));
        $this->assertSame(1, (int) $vouchers->find($id)['reserved_count']);
        $this->assertTrue($vouchers->commitReservation((int) $id));
        $row = $vouchers->find($id);
        $this->assertSame(1, (int) $row['used_count']);
        $this->assertSame(0, (int) $row['reserved_count']);
    }

    public function testVoucherReservationCanBeReleased(): void
    {
        $vouchers = new VoucherModel();
        $id = $vouchers->insert([
            'code' => 'RELEASE' . time(), 'type' => 'nominal', 'value' => 1000,
            'min_purchase' => 0, 'quota' => 1, 'reserved_count' => 1, 'is_active' => 1,
        ]);

        $this->assertTrue($vouchers->releaseReservation((int) $id));
        $this->assertSame(0, (int) $vouchers->find($id)['reserved_count']);
    }
}