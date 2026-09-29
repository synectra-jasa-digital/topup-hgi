<?php

namespace App\Controllers;

use App\Libraries\IntegrationSettings;
use App\Libraries\TelegramBot;
use App\Libraries\Telegram\CallbackHandler;
use App\Libraries\Telegram\Commands;
use App\Models\AdminModel;
use App\Models\TelegramSessionModel;
use App\Models\TelegramUpdateModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class TelegramWebhookController extends Controller
{
    private TelegramBot $bot;
    private TelegramSessionModel $sessions;
    private TelegramUpdateModel $updates;

    public function __construct()
    {
        $this->bot      = new TelegramBot();
        $this->sessions = new TelegramSessionModel();
        $this->updates  = new TelegramUpdateModel();
    }

    public function handle(): ResponseInterface
    {
        // 1. Verifikasi X-Telegram-Bot-Api-Secret-Token
        $integration = new IntegrationSettings();
        $secret = $integration->get('telegram_webhook_secret', 'TELEGRAM_WEBHOOK_SECRET');

        if ($secret !== '') {
            $incomingSecret = $this->request->getHeaderLine('X-Telegram-Bot-Api-Secret-Token');
            if (! hash_equals($secret, $incomingSecret)) {
                return $this->response->setStatusCode(403)->setBody('Forbidden');
            }
        }

        $rawInput = (string) $this->request->getBody();
        $update   = json_decode($rawInput, true);

        if (! is_array($update) || ! isset($update['update_id'])) {
            return $this->response->setStatusCode(400)->setBody('Bad Request');
        }

        $updateId = (int) $update['update_id'];

        // 2. Idempotensi update_id
        if (! $this->updates->record($updateId)) {
            // Sudah diproses sebelumnya
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // 3. Ambil pengirim dan rate limiting (maks 30 per menit per user)
        $userId = (int) (
            $update['message']['from']['id']
            ?? $update['callback_query']['from']['id']
            ?? 0
        );

        if ($userId > 0 && $this->isRateLimited($userId)) {
            return $this->response->setStatusCode(200)->setBody('Rate limited');
        }

        // 4. Cari sesi aktif
        $session = $this->resolveSession($userId);

        // 5. Routing update
        try {
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update, $session);
            } elseif (isset($update['message'])) {
                $this->handleMessage($update, $session);
            }
        } catch (\Throwable $e) {
            log_message('error', 'TelegramWebhook error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }

    private function resolveSession(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $session = $this->sessions->findByUserId($userId);
        if (! $session) {
            return null;
        }

        // Validasi apakah admin masih ada dan aktif
        $admin = (new AdminModel())->find($session['admin_id']);
        if (! $admin || ! (bool) $admin['is_active']) {
            $this->sessions->deleteByUserId($userId);
            return null;
        }

        // Gabungkan info admin ke dalam sesi
        $session['admin_name'] = $admin['name'];
        $session['admin_role'] = $admin['role'];

        // Perbarui aktivitas
        $this->sessions->touch($userId);

        return $session;
    }

    private function handleMessage(array $update, ?array $session): void
    {
        $text   = trim($update['message']['text'] ?? '');
        $userId = (int) ($update['message']['from']['id'] ?? 0);
        $chatId = (int) ($update['message']['chat']['id'] ?? 0);

        // Cek jika sedang dalam flow menunggu alasan penolakan
        $rejectState = \Config\Services::cache()->get("tg_reject_state_{$userId}");
        if ($rejectState && $session !== null) {
            (new CallbackHandler($this->bot, $this->sessions))->handleRejectReason($update, $session);
            return;
        }

        // Cek jika sedang dalam proses input login (/login)
        $loginState = \Config\Services::cache()->get('tg_login_state_' . $userId);
        if ($loginState && ! str_starts_with($text, '/')) {
            (new Commands\LoginCommand($this->bot, $this->sessions))->handle($update, $session);
            return;
        }

        // Ekstraksi command (/command arg1 arg2)
        $parts   = explode(' ', $text);
        $rawCmd  = strtolower($parts[0]);
        // Potong nama bot jika command dalam format /cmd@botname
        $command = explode('@', $rawCmd)[0];

        switch ($command) {
            case '/start':
                (new Commands\StartCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/help':
                (new Commands\HelpCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/login':
                (new Commands\LoginCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/logout':
                (new Commands\LogoutCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/status':
                (new Commands\StatusCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/tutup':
                (new Commands\CloseCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/buka':
                (new Commands\OpenCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/pending':
                (new Commands\PendingCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/pesanan':
                (new Commands\OrderCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/selesai':
                (new Commands\CompleteCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/notif':
                (new Commands\NotifCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/laporan':
                (new Commands\ReportCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            case '/sesi':
                (new Commands\SessionCommand($this->bot, $this->sessions))->handle($update, $session);
                break;

            default:
                if (str_starts_with($command, '/')) {
                    if ($session === null) {
                        $this->bot->sendMessage($chatId, 'Silakan /login terlebih dahulu.');
                    } else {
                        $this->bot->sendMessage($chatId, 'Command tidak dikenali. Ketik /help untuk melihat daftar command.');
                    }
                }
                break;
        }
    }

    private function handleCallbackQuery(array $update, ?array $session): void
    {
        $cbq    = $update['callback_query'];
        $cbqId  = $cbq['id'];
        $chatId = (int) $cbq['message']['chat']['id'];

        if ($session === null) {
            $this->bot->answerCallbackQuery($cbqId, 'Silakan login terlebih dahulu.', true);
            $this->bot->sendMessage($chatId, 'Silakan /login terlebih dahulu.');
            return;
        }

        (new CallbackHandler($this->bot, $this->sessions))->handle($update, $session);
    }

    private function isRateLimited(int $userId): bool
    {
        $cache = \Config\Services::cache();
        $key   = 'tg_ratelimit_' . $userId;
        $count = (int) $cache->get($key);

        if ($count >= 30) {
            return true;
        }

        $cache->save($key, $count + 1, 60);
        return false;
    }
}
