<?php

namespace Tests\Feature;

use App\Models\BongkarRequestModel;
use App\Models\StoreSettingModel;
use App\Models\WaOutboxModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Seam notifikasi bongkar: semua kiriman harus melewati WhatsAppNotifier
 * (driver-aware, toggle-aware, outbox retry) — tanpa jalur direct gateway
 * yang menyampingi pengaturan wa_driver.
 */
final class BongkarNotificationSeamTest extends CIUnitTestCase
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

    private function makeRequest(): int
    {
        $catalogs = new \App\Models\BongkarCatalogModel();
        $catalogs->db->query('PRAGMA foreign_keys = OFF');
        $catalogs->insert([
            'code' => 'kartu-ungu', 'name' => 'Kartu Ungu', 'unit_label' => 'kartu',
            'base_rate' => 65000, 'sort_order' => 1, 'is_active' => 1,
        ]);

        return (int) (new BongkarRequestModel())->insert([
            'request_number'        => 'BGK' . bin2hex(random_bytes(4)),
            'bongkar_catalog_id'    => $catalogs->getInsertID(),
            'catalog_code_snapshot' => 'kartu-ungu',
            'catalog_name_snapshot' => 'Kartu Ungu',
            'unit_label_snapshot'   => 'kartu',
            'quantity'              => 2,
            'rate_snapshot'         => 65000,
            'estimated_amount'      => 130000,
            'customer_whatsapp'     => '0811111111',
            'payout_method'         => 'BCA',
            'status'                => 'pending',
            'notification_status'   => 'pending',
            'notification_retry_count' => 0,
        ]);
    }

    private function postStatus(int $id, string $status)
    {
        // AuthFilter membaca ulang tabel admins, jadi session harus menunjuk
        // baris owner yang benar-benar ada.
        $admins = new \App\Models\AdminModel();
        $admins->where('email', 'owner-bongkar-seam@example.test')->delete();
        $adminId = (int) $admins->insert([
            'name'      => 'Owner Uji Bongkar',
            'email'     => 'owner-bongkar-seam@example.test',
            'password'  => password_hash('tidak-dipakai', PASSWORD_BCRYPT),
            'role'      => 'owner',
            'is_active' => 1,
        ]);

        return $this->withSession(['admin_id' => $adminId, 'admin_role' => 'owner'])
            ->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('/admin/bongkar-pesanan/' . $id . '/status', ['status' => $status]);
    }

    public function testStatusUpdateWithDriverOffSkipsNotificationWithoutFailing(): void
    {
        (new StoreSettingModel())->setVal('wa_driver', 'off');
        $id = $this->makeRequest();

        $this->postStatus($id, 'diproses')->assertRedirect();

        $row = (new BongkarRequestModel())->find($id);
        self::assertSame('diproses', $row['status']);
        self::assertSame(
            'sent',
            $row['notification_status'],
            'Driver off = sengaja dilewat, ditandai selesai — bukan gagal yang di-retry terus'
        );
        self::assertSame(0, (new WaOutboxModel())->countAllResults(), 'Driver off tidak boleh mengantre pesan apa pun');
    }

    public function testStatusUpdateWithDriverBaileysQueuesThroughOutboxOnce(): void
    {
        (new StoreSettingModel())->setVal('wa_driver', 'baileys');
        $id = $this->makeRequest();

        $this->postStatus($id, 'diproses')->assertRedirect();

        $outbox = new WaOutboxModel();
        $rows   = $outbox->where('dedupe_key', "bongkar_status:{$id}:diproses")->findAll();

        self::assertCount(1, $rows, 'Tepat satu entri outbox per transisi (tanpa dobel, tanpa direct gateway)');
        self::assertSame('bongkar_status', $rows[0]['type']);
        self::assertSame('0811111111', $rows[0]['recipient']);

        $row = (new BongkarRequestModel())->find($id);
        self::assertSame('sent', $row['notification_status'], 'Setelah diserahkan ke outbox, antrean bongkar ditandai selesai');
    }
}
