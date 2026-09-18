<?php

namespace Tests\Feature;

use App\Models\PaymentChannelModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class PaymentChannelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    public function testChangingQrisChannelToBankRemovesItsQrAsset(): void
    {
        $relativePath = 'assets/uploads/payment_channels/test-qris.png';
        $absolutePath = FCPATH . $relativePath;
        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, 'test');

        $model = new PaymentChannelModel();
        $channelId = $model->insert([
            'type'          => 'qris',
            'name'          => 'QRIS Toko',
            'qr_image_path' => $relativePath,
            'sort_order'    => 1,
            'is_active'     => 1,
        ]);

        $result = $this->withSession([
            'admin_id'   => 1,
            'admin_role' => 'owner',
        ])->withHeaders([
            csrf_header() => csrf_hash(),
        ])->post('/admin/metode-bayar/' . $channelId . '/ubah', [
            'type'           => 'bank',
            'name'           => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Ayong Store',
            'sort_order'     => 1,
            'is_active'      => 1,
        ]);

        $result->assertRedirectTo('/admin/metode-bayar');
        self::assertNull($model->find($channelId)['qr_image_path']);
        self::assertFileDoesNotExist($absolutePath);
    }
}
