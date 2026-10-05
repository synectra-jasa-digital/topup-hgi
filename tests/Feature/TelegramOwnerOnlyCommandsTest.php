<?php

namespace Tests\Feature;

use App\Libraries\Telegram\CallbackHandler;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Guard privilege escalation lewat Telegram bot.
 *
 * Panel web membatasi /laporan dan /admin/pengaturan-toko untuk owner.
 * Perintah Telegram untuk aksi yang sama harus memakai batas yang sama;
 * sebelumnya hanya requireLogin(), sehingga admin non-owner bisa menarik
 * laporan penjualan dan mematikan seluruh toko.
 */
class TelegramOwnerOnlyCommandsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    private const WEBHOOK = '/telegram/webhook';

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        \Config\Services::migrations()->setNamespace('App')->setGroup('tests')->latest();

        // Nonaktifkan verifikasi secret supaya test fokus pada otorisasi role.
        putenv('TELEGRAM_WEBHOOK_SECRET=');
        unset($_ENV['TELEGRAM_WEBHOOK_SECRET']);
    }

    private function postCommand(string $text, array $session): \CodeIgniter\Test\TestResponse
    {
        $payload = [
            'update_id'   => 900001,
            'message'     => [
                'message_id' => 55,
                'from'       => ['id' => 424242, 'is_bot' => false],
                'chat'       => ['id' => 424242, 'type' => 'private'],
                'text'       => $text,
                'date'       => time(),
            ],
        ];

        return $this->withBody(json_encode($payload))
            ->post(self::WEBHOOK);
    }

    private function adminSession(string $role): array
    {
        return [
            'id'         => 7,
            'admin_id'   => 7,
            'admin_name' => 'Admin Biasa',
            'admin_role' => $role,
            'chat_id'    => 424242,
        ];
    }

    private function seedSession(array $session): void
    {
        // Sesi Telegram menyimpan admin_id saja; role diambil ulang dari
        // tabel admins oleh layer Telegram saat session di-resolve.
        $admins = new \App\Models\AdminModel();
        $admin  = $admins->where('email', 'owner-only-test@example.test')->first();

        if ($admin === null) {
            $admins->insert([
                'name'      => $session['admin_name'],
                'email'     => 'owner-only-test@example.test',
                'password'  => password_hash('tidak-dipakai', PASSWORD_BCRYPT),
                'role'      => $session['admin_role'],
                'is_active' => 1,
            ]);
            $admin = $admins->where('email', 'owner-only-test@example.test')->first();
        } else {
            $admins->update($admin['id'], [
                'role'      => $session['admin_role'],
                'is_active' => 1,
            ]);
        }

        $sessions = new \App\Models\TelegramSessionModel();
        $sessions->where('telegram_user_id', 424242)->delete();
        $sessions->createSession(424242, $session['chat_id'], (int) $admin['id']);
    }

    public function testNonOwnerCannotOpenTheStoreViaTelegram(): void
    {
        $this->seedSession($this->adminSession('admin'));

        $this->postCommand('/buka', $this->adminSession('admin'))
            ->assertStatus(200);

        $settings = new \App\Models\StoreSettingModel();

        // Setting maintenance TIDAK boleh berubah oleh admin non-owner.
        $this->assertNotSame(
            '0',
            (string) $settings->getVal('maintenance'),
            'admin non-owner berhasil membuka toko lewat /buka'
        );
    }

    public function testNonOwnerCannotCloseTheStoreViaTelegram(): void
    {
        $this->seedSession($this->adminSession('admin'));

        // Pastikan toko sedang terbuka sebelum dicoba ditutup.
        $settings = new \App\Models\StoreSettingModel();
        $settings->setVal('maintenance', '0');

        $this->postCommand('/tutup alasan uji', $this->adminSession('admin'))
            ->assertStatus(200);

        $this->assertNotSame(
            '1',
            (string) $settings->getVal('maintenance'),
            'admin non-owner berhasil menutup toko lewat /tutup'
        );
    }

    public function testOwnerCanStillOpenTheStoreViaTelegram(): void
    {
        $this->seedSession($this->adminSession('owner'));

        $settings = new \App\Models\StoreSettingModel();
        $settings->setVal('maintenance', '1');

        $this->postCommand('/buka', $this->adminSession('owner'))
            ->assertStatus(200);

        $this->assertSame(
            '0',
            (string) $settings->getVal('maintenance'),
            'owner seharusnya tetap bisa membuka toko'
        );
    }

    public function testNonOwnerCannotFetchSalesReportViaTelegram(): void
    {
        $this->seedSession($this->adminSession('admin'));

        $response = $this->postCommand('/laporan', $this->adminSession('admin'));

        $response->assertStatus(200);

        // Perintah ditolak, jadi bot tidak boleh mengirim dokumen laporan.
        $body = (string) $response->getBody();

        $this->assertStringNotContainsString(
            'laporan_',
            $body,
            'admin non-owner berhasil memicu pembuatan laporan penjualan'
        );
    }

    public function testCallbackHandlerRoutesReportCommandThroughOwnerCheck(): void
    {
        // Pastikan ReportCommand benar-benar memakai requireRole, bukan
        // requireLogin — ini guard statis terhadap regresi.
        $source = (string) file_get_contents(
            ROOTPATH . 'app/Libraries/Telegram/Commands/ReportCommand.php'
        );

        $this->assertStringContainsString(
            "requireRole(\$update, \$session, ['owner'])",
            $source,
            'ReportCommand harus memblokir non-owner secara eksplisit'
        );
        $this->assertStringNotContainsString(
            'requireLogin($update, $session)',
            $source,
            'ReportCommand tidak boleh lagi hanya mengandalkan requireLogin'
        );
    }
}