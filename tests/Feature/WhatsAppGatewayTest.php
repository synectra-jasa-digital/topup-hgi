<?php

namespace Tests\Feature;

use App\Libraries\WhatsApp\BaileysGateway;
use App\Libraries\WhatsApp\MessageTemplates;
use App\Libraries\WhatsApp\WhatsAppNotifier;
use App\Models\WaOutboxModel;
use CodeIgniter\Test\CIUnitTestCase;

final class WhatsAppGatewayTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('phone');
    }

    public function testNormalizePhone(): void
    {
        self::assertSame('6281234567890', normalize_phone('081234567890'));
        self::assertSame('6281234567890', normalize_phone('81234567890'));
        self::assertSame('6281234567890', normalize_phone('+62 812-3456-7890'));
        self::assertSame('6281234567890', normalize_phone('006281234567890'));
        self::assertSame('', normalize_phone('123'));
        self::assertSame('', normalize_phone(null));
    }

    public function testMessageTemplatesMasking(): void
    {
        self::assertSame('98****32', MessageTemplates::maskGameId('98765432'));
        self::assertSame('1**4', MessageTemplates::maskGameId('1234'));
        self::assertSame('-', MessageTemplates::maskGameId(null));
    }

    public function testWaOutboxModelDeduplicationAndClaim(): void
    {
        $model = new WaOutboxModel();

        $key = 'test_dedupe_' . time() . '_' . rand(1000, 9999);
        $id1 = $model->enqueue([
            'recipient'  => '6281234567890',
            'type'       => 'order_created',
            'message'    => 'Pesan tes 1',
            'dedupe_key' => $key,
        ]);

        self::assertNotNull($id1);

        // Duplicate enqueue must return null
        $id2 = $model->enqueue([
            'recipient'  => '6281234567890',
            'type'       => 'order_created',
            'message'    => 'Pesan tes 2',
            'dedupe_key' => $key,
        ]);

        self::assertNull($id2);

        // Claim
        $claimed = $model->claim(10);
        self::assertNotEmpty($claimed);

        // Verify status in DB is now processing
        $claimedRow = $model->find($id1);
        self::assertSame('processing', $claimedRow['status']);

        // Mark sent
        $model->markSent($id1, 'MSG12345');
        $updated = $model->find($id1);
        self::assertSame('sent', $updated['status']);
        self::assertSame('MSG12345', $updated['gateway_message_id']);
    }

    public function testWhatsAppNotifierOrderCompleted(): void
    {
        (new \App\Models\StoreSettingModel())->setVal('wa_driver', 'baileys');

        $notifier = new WhatsAppNotifier();
        $order = [
            'id'                     => 99999,
            'invoice_number'         => 'INV-TEST-999',
            'whatsapp_number'        => '081234567890',
            'product_name_snapshot'  => 'Koin 1B',
            'nominal_snapshot'       => '1B Gold',
            'game_id'                => '98765432',
        ];

        $notifier->orderCompleted($order);

        $outbox = new WaOutboxModel();
        $row = $outbox->where('dedupe_key', 'order_completed:99999')->first();
        self::assertNotNull($row);
        self::assertSame('6281234567890', normalize_phone($row['recipient']));
        self::assertStringContainsString('INV-TEST-999', $row['message']);
        self::assertStringContainsString('98****32', $row['message']);
    }
}
