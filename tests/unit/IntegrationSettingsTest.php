<?php

namespace Tests\Unit;

use App\Libraries\IntegrationSettings;
use App\Models\StoreSettingModel;
use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class IntegrationSettingsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;

    private string $originalKey = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();

        // Kunci enkripsi deterministik untuk test, tanpa bergantung pada .env mesin.
        $config            = config('Encryption');
        $this->originalKey = $config->key;
        if ($config->key === '') {
            $config->key = 'hex2bin:' . str_repeat('1a', 32);
            Services::reset('encrypter');
        }
    }

    protected function tearDown(): void
    {
        $config = config('Encryption');
        $config->key = $this->originalKey;
        Services::reset('encrypter');
        parent::tearDown();
    }

    public function testSetEncryptsValueAtRestAndGetRoundTrips(): void
    {
        $settings = new IntegrationSettings();
        $settings->set('unit_secret', 'rahasia-123');

        $raw = (new StoreSettingModel())->getVal('unit_secret');
        self::assertStringNotContainsString('rahasia', $raw, 'Nilai harus tersimpan terenkripsi, bukan plaintext');
        self::assertSame('rahasia-123', $settings->get('unit_secret'));
    }

    public function testGetReturnsLegacyPlaintextStoredValue(): void
    {
        (new StoreSettingModel())->setVal('legacy_secret', 'plain-lama');

        $settings = new IntegrationSettings();

        self::assertSame('plain-lama', $settings->get('legacy_secret'));
    }

    public function testSetRefusesToStorePlaintextWhenEncrypterUnavailable(): void
    {
        $config         = config('Encryption');
        $config->key    = '';
        Services::reset('encrypter');

        $thrown = null;
        try {
            (new IntegrationSettings())->set('unit_secret_fail', 'tidak-boleh-tersimpan');
        } catch (\RuntimeException $e) {
            $thrown = $e;
        } finally {
            $config->key = $this->originalKey;
            Services::reset('encrypter');
        }

        self::assertNotNull($thrown, 'set() harus gagal keras saat encrypter tidak tersedia');
        self::assertStringContainsString('encryption.key', $thrown->getMessage());
        self::assertSame('', (new StoreSettingModel())->getVal('unit_secret_fail'), 'Nilai tidak boleh tersimpan saat enkripsi gagal');
    }

    public function testStateReportsEncryptedPlainAndUnset(): void
    {
        $settings = new IntegrationSettings();

        self::assertSame('unset', $settings->state('state_probe'));

        $settings->set('state_probe', 'v');
        self::assertSame('encrypted', $settings->state('state_probe'));

        (new StoreSettingModel())->setVal('state_plain', 'plain');
        self::assertSame('unprotected', $settings->state('state_plain'));
    }
}
