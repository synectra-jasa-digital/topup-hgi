<?php

namespace Tests\Feature;

use App\Libraries\IntegrationSettings;
use App\Models\StoreSettingModel;
use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class WhatsAppSettingsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private string $originalKey = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();

        $config            = config('Encryption');
        $this->originalKey = $config->key;
        if ($config->key === '') {
            $config->key = 'hex2bin:' . str_repeat('2b', 32);
            Services::reset('encrypter');
        }
    }

    protected function tearDown(): void
    {
        $config         = config('Encryption');
        $config->key    = $this->originalKey;
        Services::reset('encrypter');
        parent::tearDown();
    }

    private function ownerSession(): array
    {
        return ['admin_id' => 1, 'admin_role' => 'owner'];
    }

    public function testSettingsPageNeverRendersTheStoredGatewayKey(): void
    {
        // Kasus terburuk: nilai tersimpan plaintext lama — tetap tidak boleh muncul di HTML.
        (new StoreSettingModel())->setVal('wa_gateway_key', 'kunci-rahasia-sangat-sensitif');

        $result = $this->withSession($this->ownerSession())->get('/admin/whatsapp');
        $body   = $result->getBody();

        $result->assertOK();
        self::assertStringNotContainsString('kunci-rahasia-sangat-sensitif', $body, 'API key tidak boleh dirender ke HTML');
        self::assertStringContainsString('name="wa_gateway_key"', $body, 'Field input tetap harus ada');
        self::assertStringContainsString('Tidak terlindungi', $body, 'Panel harus menandai nilai yang gagal didekripsi');
    }

    public function testSavingNewGatewayKeyEncryptsItAtRest(): void
    {
        $result = $this->withSession($this->ownerSession())
            ->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('/admin/whatsapp/settings', [
                'wa_driver'      => 'baileys',
                'wa_gateway_url' => 'http://127.0.0.1:3000',
                'wa_gateway_key' => 'kunci-baru-secret-999',
            ]);

        $result->assertRedirect();

        $raw = (new StoreSettingModel())->getVal('wa_gateway_key');
        self::assertStringNotContainsString('kunci-baru', $raw, 'API key baru harus terenkripsi di database');
        self::assertSame('kunci-baru-secret-999', (new IntegrationSettings())->get('wa_gateway_key'));
    }

    public function testEmptyGatewayKeyFieldKeepsExistingKey(): void
    {
        (new IntegrationSettings())->set('wa_gateway_key', 'kunci-lama-tetap-berlaku');

        $result = $this->withSession($this->ownerSession())
            ->withBodyFormat('form')
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('/admin/whatsapp/settings', [
                'wa_driver'      => 'baileys',
                'wa_gateway_url' => 'http://127.0.0.1:3000',
                'wa_gateway_key' => '',
            ]);

        $result->assertRedirect();
        self::assertSame('kunci-lama-tetap-berlaku', (new IntegrationSettings())->get('wa_gateway_key'));
    }
}
