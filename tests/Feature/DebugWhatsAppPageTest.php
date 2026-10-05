<?php

namespace Tests\Feature;

use App\Models\StoreSettingModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * DEBUG SEMENTARA — dump body respons /admin/whatsapp.
 * Hapus setelah selesai.
 */
final class DebugWhatsAppPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();

        $config = config('Encryption');
        if ($config->key === '') {
            $config->key = 'hex2bin:' . str_repeat('2b', 32);
            \CodeIgniter\Config\Services::reset('encrypter');
        }

        $admins = new \App\Models\AdminModel();
        $this->adminId = (int) $admins->insert([
            'name' => 'O', 'email' => 'dbg-' . bin2hex(random_bytes(3)) . '@example.test',
            'password' => password_hash('x', PASSWORD_BCRYPT), 'role' => 'owner', 'is_active' => 1,
        ]);
    }

    public function testDumpBody(): void
    {
        (new StoreSettingModel())->setVal('wa_gateway_key', 'rahasia');

        fwrite(STDERR, "\n=== adminId: {$this->adminId}\n");
        $found = (new \App\Models\AdminModel())->find($this->adminId);
        fwrite(STDERR, '=== AdminModel::find: ' . json_encode($found) . "\n");
        fwrite(STDERR, '=== count admins: ' . (new \App\Models\AdminModel())->countAllResults() . "\n");
        fwrite(STDERR, '=== DB group default: ' . config('Database')->defaultGroup . "\n");
        fwrite(STDERR, '=== tests group: ' . json_encode(config('Database')->tests) . "\n");

        $result = $this->withSession(['admin_id' => $this->adminId, 'admin_role' => 'owner'])
            ->get('/admin/whatsapp');

        fwrite(STDERR, "\n=== STATUS: " . $result->response()->getStatusCode() . "\n");
        fwrite(STDERR, '=== LOCATION: ' . $result->response()->getHeaderLine('Location') . "\n");
        fwrite(STDERR, '=== SESSION admin_id: ' . var_export(session()->get('admin_id'), true) . "\n");
        fwrite(STDERR, '=== ENVIRONMENT: ' . ENVIRONMENT . "\n");
        fwrite(STDERR, '=== maintenance setting: ' . var_export((new StoreSettingModel())->getVal('maintenance'), true) . "\n");
        fwrite(STDERR, '=== baseURL: ' . config('App')->baseURL . "\n");
        fwrite(STDERR, '=== App.baseURL raw env: ' . var_export(getenv('app.baseURL'), true) . "\n");
        fwrite(STDERR, '=== indexPage: [' . config('App')->indexPage . "]\n");
        fwrite(STDERR, '=== forceGlobalSecureRequests: ' . var_export(config('App')->forceGlobalSecureRequests, true) . "\n");
        fwrite(STDERR, '=== basePath: [' . config('App')->basePath . "]\n");
        fwrite(STDERR, "=== BODY RAW: [" . $result->getBody() . "]\n");

        $this->assertTrue(true);
    }
}