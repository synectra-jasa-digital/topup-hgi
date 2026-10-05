<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Guard batas nilai voucher.
 *
 * voucherData() hanya melakukan cast (float)/(int) tanpa aturan validasi,
 * sehingga admin bisa mengirim nilai di luar rentang yang masuk akal dan
 * calculateDiscount hanya membatasi ke subtotal. Percentage 100 membuat
 * pesanan gratis; nilai negatif atau non-numerik lolos ke database.
 */
final class VoucherInputValidationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private int $adminId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();

        // AuthFilter membaca ulang baris admins pada tiap request, jadi
        // session harus menunjuk admin yang benar-benar ada di database.
        $admins = new \App\Models\AdminModel();
        $this->adminId = (int) $admins->insert([
            'name'      => 'Owner Uji Voucher',
            'email'     => 'uji-voucher@example.test',
            'password'  => password_hash('tidak-dipakai', PASSWORD_BCRYPT),
            'role'      => 'owner',
            'is_active' => 1,
        ]);
    }

    private function postVoucher(array $overrides = [])
    {
        $data = $overrides + [
            'code'         => 'UJI' . strtoupper(bin2hex(random_bytes(3))),
            'type'         => 'percentage',
            'value'        => '10',
            'min_purchase' => '0',
            'quota'        => '5',
            'start_date'   => '',
            'end_date'     => '',
            'is_active'    => '1',
        ];

        return $this->withSession(['admin_id' => $this->adminId, 'admin_role' => 'owner'])
            ->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('/admin/voucher/tambah', $data);
    }

    private function countVouchers(): int
    {
        return (int) $this->db->table('vouchers')->countAllResults();
    }

    public function testValidPercentageVoucherIsAccepted(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'percentage', 'value' => '15'])->assertRedirect();

        $this->assertSame(
            $before + 1,
            $this->countVouchers(),
            'voucher percentage 15% yang valid seharusnya tersimpan'
        );
    }

    public function testPercentageAboveHundredIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'percentage', 'value' => '150']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'percentage di atas 100% tidak boleh tersimpan (pesanan jadi gratis)'
        );
    }

    public function testNegativePercentageIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'percentage', 'value' => '-10']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'percentage negatif tidak boleh tersimpan'
        );
    }

    public function testNonNumericValueIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'nominal', 'value' => 'abc']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'value non-numerik harus ditolak, bukan tersimpan sebagai 0'
        );
    }

    public function testNegativeNominalIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'nominal', 'value' => '-5000']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'nominal negatif tidak boleh tersimpan'
        );
    }

    public function testUnknownVoucherTypeIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'gratis', 'value' => '10']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'type di luar enum database tidak boleh tersimpan'
        );
    }

    public function testNegativeQuotaIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['quota' => '-5']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'quota negatif tidak boleh tersimpan'
        );
    }

    public function testNegativeMinPurchaseIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['min_purchase' => '-1000']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'min_purchase negatif tidak boleh tersimpan'
        );
    }

    public function testPercentageZeroIsRejected(): void
    {
        $before = $this->countVouchers();

        $this->postVoucher(['type' => 'percentage', 'value' => '0']);

        $this->assertSame(
            $before,
            $this->countVouchers(),
            'percentage 0% tidak berguna dan sebaiknya ditolak'
        );
    }
}