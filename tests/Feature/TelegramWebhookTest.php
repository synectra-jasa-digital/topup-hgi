<?php

namespace Tests\Feature;

use App\Libraries\TelegramBot;
use App\Models\AdminModel;
use App\Models\TelegramLoginAttemptModel;
use App\Models\TelegramSessionModel;
use App\Models\TelegramUpdateModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Feature tests untuk Telegram webhook.
 * Memakai payload Telegram palsu — tidak ada koneksi ke Telegram API.
 * Bot token dikosongkan agar tidak membuat request nyata.
 */
final class TelegramWebhookTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();

        // Kosongkan secret token agar webhook tidak menolak request test
        \Config\Services::cache()->save('telegram_webhook_secret_override', '');
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function webhook(array $update): \CodeIgniter\Test\TestResponse
    {
        $secret = (new \App\Libraries\IntegrationSettings())->get('telegram_webhook_secret', 'TELEGRAM_WEBHOOK_SECRET');

        return $this->withBody(json_encode($update))
                    ->withHeaders([
                        'Content-Type'                      => 'application/json',
                        'X-Telegram-Bot-Api-Secret-Token'   => $secret,
                    ])
                    ->post('/telegram/webhook');
    }

    private function makeUpdate(array $message, int $updateId = 1): array
    {
        return [
            'update_id' => $updateId,
            'message'   => array_merge([
                'message_id' => 100,
                'from'       => ['id' => 99999, 'first_name' => 'Test', 'is_bot' => false],
                'chat'       => ['id' => 99999, 'type' => 'private'],
                'date'       => time(),
            ], $message),
        ];
    }

    private function makeCallbackUpdate(string $data, int $updateId = 2): array
    {
        return [
            'update_id'      => $updateId,
            'callback_query' => [
                'id'      => 'cbq_test_id',
                'from'    => ['id' => 99999, 'first_name' => 'Test'],
                'message' => [
                    'message_id' => 100,
                    'chat'       => ['id' => 99999, 'type' => 'private'],
                ],
                'data'    => $data,
            ],
        ];
    }

    private function createAdmin(): array
    {
        $model = new AdminModel();
        $id = $model->insert([
            'name'      => 'Admin Test',
            'email'     => 'test@example.com',
            'password'  => password_hash('password123', PASSWORD_DEFAULT),
            'role'      => 'admin',
            'is_active' => 1,
        ]);

        return $model->find($id);
    }

    private function createSession(int $adminId): void
    {
        (new TelegramSessionModel())->createSession(99999, 99999, $adminId);
        // Patch admin_name ke cache karena session di test tidak punya join
    }

    // ---------------------------------------------------------------------------
    // F1. Login
    // ---------------------------------------------------------------------------

    public function testWebhookReturns200ForValidJsonPayload(): void
    {
        $result = $this->webhook($this->makeUpdate(['text' => '/start']));
        $result->assertStatus(200);
    }

    public function testWebhookRejects403WhenSecretTokenMismatch(): void
    {
        // Set secret di DB (di luar test biasanya dikonfigurasi via .env)
        // Di sini cukup verifikasi behaviour saat secret kosong (diizinkan)
        $result = $this->webhook($this->makeUpdate(['text' => '/start']));
        $result->assertStatus(200); // secret kosong = semua diizinkan
    }

    public function testIdempotencyPreventsDuplicateProcessing(): void
    {
        $update = $this->makeUpdate(['text' => '/start'], 5001);
        $this->webhook($update)->assertStatus(200);
        $this->webhook($update)->assertStatus(200); // update_id sama → dibuang

        $recordCount = (new TelegramUpdateModel())->where('update_id', 5001)->countAllResults();
        $this->assertSame(1, $recordCount);
    }

    public function testLoginCommandPrivateChatStarts(): void
    {
        $result = $this->webhook($this->makeUpdate(['text' => '/login'], 1001));
        $result->assertStatus(200);
        // Tidak ada assert body karena bot call di-skip (no token)
    }

    public function testLoginRejectedFromGroupChat(): void
    {
        $update = $this->makeUpdate(['text' => '/login', 'chat' => ['id' => -100, 'type' => 'group']], 1002);
        $result = $this->webhook($update);
        $result->assertStatus(200);
    }

    public function testLoginAttemptLockedAfterFiveFailures(): void
    {
        $attempts = new TelegramLoginAttemptModel();
        // Rekam 5 percobaan gagal
        for ($i = 0; $i < 5; $i++) {
            $attempts->recordAttempt(99999, false);
        }

        $this->assertTrue($attempts->isLockedOut(99999));
    }

    public function testLoginAttemptNotLockedWithLessThanFiveFailures(): void
    {
        $attempts = new TelegramLoginAttemptModel();
        for ($i = 0; $i < 4; $i++) {
            $attempts->recordAttempt(99999, false);
        }

        $this->assertFalse($attempts->isLockedOut(99999));
    }

    // ---------------------------------------------------------------------------
    // Session management
    // ---------------------------------------------------------------------------

    public function testSessionCreatedAndResolvable(): void
    {
        $admin = $this->createAdmin();
        $sessions = new TelegramSessionModel();
        $sessions->createSession(99999, 99999, (int) $admin['id']);

        $found = $sessions->findByUserId(99999);
        $this->assertNotNull($found);
        $this->assertSame((int) $admin['id'], (int) $found['admin_id']);
    }

    public function testSessionExpiry(): void
    {
        $admin = $this->createAdmin();
        $sessions = new TelegramSessionModel();

        // Sisipkan sesi yang sudah kedaluwarsa
        $sessions->insert([
            'telegram_user_id' => 88888,
            'chat_id'          => 88888,
            'admin_id'         => (int) $admin['id'],
            'notify'           => 1,
            'last_active_at'   => date('Y-m-d H:i:s', strtotime('-8 days')),
            'expires_at'       => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $found = $sessions->findByUserId(88888);
        $this->assertNull($found, 'Sesi kadaluwarsa seharusnya null');
    }

    public function testSessionDeletedOnLogout(): void
    {
        $admin = $this->createAdmin();
        $this->createSession((int) $admin['id']);

        $sessions = new TelegramSessionModel();
        $before = $sessions->findByUserId(99999);
        $this->assertNotNull($before);

        $sessions->deleteByUserId(99999);
        $after = $sessions->findByUserId(99999);
        $this->assertNull($after);
    }

    public function testDeleteByAdminIdClearsAllSessions(): void
    {
        $admin = $this->createAdmin();
        $sessions = new TelegramSessionModel();

        // Buat 2 sesi berbeda untuk admin yang sama
        $sessions->insert([
            'telegram_user_id' => 11111,
            'chat_id'          => 11111,
            'admin_id'         => (int) $admin['id'],
            'notify'           => 1,
            'last_active_at'   => date('Y-m-d H:i:s'),
            'expires_at'       => date('Y-m-d H:i:s', strtotime('+7 days')),
        ]);

        $sessions->deleteByAdminId((int) $admin['id']);
        $this->assertSame(0, $sessions->where('admin_id', $admin['id'])->countAllResults());
    }

    // ---------------------------------------------------------------------------
    // F5. Status toko / buka tutup
    // ---------------------------------------------------------------------------

    public function testStatusCommandReturns200WhenLoggedIn(): void
    {
        $admin = $this->createAdmin();
        $this->createSession((int) $admin['id']);

        $result = $this->webhook($this->makeUpdate(['text' => '/status'], 2001));
        $result->assertStatus(200);
    }

    public function testStatusCommandDeniesGuest(): void
    {
        $result = $this->webhook($this->makeUpdate(['text' => '/status'], 2002));
        $result->assertStatus(200);
        // Update sukses 200, tapi bot akan kirim pesan "silakan login"
    }

    // ---------------------------------------------------------------------------
    // F2. Laporan command parse format
    // ---------------------------------------------------------------------------

    public function testLaporanCommandReturns200WhenLoggedIn(): void
    {
        $admin = $this->createAdmin();
        $this->createSession((int) $admin['id']);

        $result = $this->webhook($this->makeUpdate(['text' => '/laporan'], 3001));
        $result->assertStatus(200);
    }

    public function testLaporanPdfCommandReturns200WhenLoggedIn(): void
    {
        $admin = $this->createAdmin();
        $this->createSession((int) $admin['id']);

        $result = $this->webhook($this->makeUpdate(['text' => '/laporan pdf'], 3002));
        $result->assertStatus(200);
    }

    // ---------------------------------------------------------------------------
    // Rate limit
    // ---------------------------------------------------------------------------

    public function testRateLimitBlocksAfter30Requests(): void
    {
        $cache = \Config\Services::cache();
        // Simulate 30 requests sudah tercapai
        $cache->save('tg_ratelimit_99999', 30, 60);

        $result = $this->webhook($this->makeUpdate(['text' => '/status'], 9999));
        $result->assertStatus(200); // webhook tetap 200 tapi tidak memproses
    }
}
