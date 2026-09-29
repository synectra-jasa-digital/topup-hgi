<?php

namespace App\Libraries\Telegram;

use App\Libraries\TelegramBot;
use App\Libraries\WablasGateway;
use App\Models\ActivityLogModel;
use App\Models\AdminModel;
use App\Models\OrderModel;
use App\Models\TelegramOutboxModel;
use App\Models\TelegramSessionModel;

/**
 * Menangani semua callback_query dari tombol inline keyboard.
 */
class CallbackHandler
{
    private TelegramBot $bot;
    private TelegramSessionModel $sessions;

    public function __construct(?TelegramBot $bot = null, ?TelegramSessionModel $sessions = null)
    {
        $this->bot      = $bot ?? new TelegramBot();
        $this->sessions = $sessions ?? new TelegramSessionModel();
    }

    public function handle(array $update, array $session): void
    {
        $cbq    = $update['callback_query'];
        $data   = $cbq['data'] ?? '';
        $chatId = (int) $cbq['message']['chat']['id'];
        $msgId  = (int) $cbq['message']['message_id'];
        $cbqId  = $cbq['id'];

        if (str_starts_with($data, 'order_verify:')) {
            $orderId = (int) substr($data, strlen('order_verify:'));
            $this->handleVerify($orderId, $session, $chatId, $msgId, $cbqId);
            return;
        }

        if (str_starts_with($data, 'order_reject:')) {
            $orderId = (int) substr($data, strlen('order_reject:'));
            $this->handleRejectPrompt($orderId, $session, $chatId, $msgId, $cbqId);
            return;
        }

        if (str_starts_with($data, 'close_confirm:')) {
            (new Commands\CloseCommand($this->bot, $this->sessions))->handle($update, $session);
            return;
        }

        if ($data === 'close_cancel') {
            $this->bot->answerCallbackQuery($cbqId, 'Dibatalkan.');
            $this->bot->editMessageText($chatId, $msgId, '❎ Penutupan toko dibatalkan.');
            return;
        }

        if (str_starts_with($data, 'revoke_session:')) {
            (new Commands\SessionCommand($this->bot, $this->sessions))->handle($update, $session);
            return;
        }

        if (str_starts_with($data, 'laporan:')) {
            (new Commands\ReportCommand($this->bot, $this->sessions))->handle($update, $session);
            return;
        }
    }

    private function handleVerify(int $orderId, array $session, int $chatId, int $msgId, string $cbqId): void
    {
        $orderModel = new OrderModel();
        $adminId    = (int) $session['admin_id'];
        $adminName  = $session['admin_name'] ?? 'Admin';

        $success = $orderModel->verifyPayment($orderId, $adminId);

        if (! $success) {
            // Sudah diproses admin lain
            $order = $orderModel->find($orderId);
            $actor = '';
            if ($order && ! empty($order['payment_verified_by'])) {
                $admin = (new AdminModel())->find($order['payment_verified_by']);
                $actor = $admin ? $admin['name'] : 'admin lain';
            }
            $this->bot->answerCallbackQuery($cbqId, "Pesanan sudah diproses oleh {$actor}", true);
            return;
        }

        $this->bot->answerCallbackQuery($cbqId, '✅ Pembayaran berhasil diverifikasi');

        $caption = "✅ <b>Diverifikasi oleh {$adminName}</b>";

        // Edit pesan di chat yang aktif
        $this->bot->editMessageCaption($chatId, $msgId, $caption, []);

        // Log aktivitas
        (new ActivityLogModel())->log($adminId, 'telegram_verifikasi_pembayaran', "Verifikasi pesanan ID {$orderId} via Telegram");

        // Beritahu admin lain via edit pesan mereka
        $this->broadcastStatusUpdate($orderId, 'order_payment_proof', $caption);
    }

    private function handleRejectPrompt(int $orderId, array $session, int $chatId, int $msgId, string $cbqId): void
    {
        // Simpan state menunggu alasan tolak di cache
        $userId   = (int) ($session['telegram_user_id'] ?? 0);
        $stateKey = "tg_reject_state_{$userId}";
        \Config\Services::cache()->save($stateKey, [
            'order_id'   => $orderId,
            'message_id' => $msgId,
        ], 300);

        $this->bot->answerCallbackQuery($cbqId, 'Masukkan alasan penolakan');
        $this->bot->sendMessage(
            $chatId,
            "❌ <b>Tolak Pembayaran</b>\n\nMasukkan alasan penolakan (maks. 500 karakter):\n"
            . "(Kirim /batal untuk membatalkan)"
        );
    }

    public function handleRejectReason(array $update, array $session): void
    {
        $userId   = (int) ($session['telegram_user_id'] ?? 0);
        $stateKey = "tg_reject_state_{$userId}";
        $state    = \Config\Services::cache()->get($stateKey);

        if (! $state) {
            return;
        }

        $chatId  = (int) ($update['message']['chat']['id'] ?? 0);
        $reason  = trim($update['message']['text'] ?? '');

        if ($reason === '/batal') {
            \Config\Services::cache()->delete($stateKey);
            $this->bot->sendMessage($chatId, '❎ Penolakan dibatalkan.');
            return;
        }

        if (strlen($reason) > 500) {
            $this->bot->sendMessage($chatId, '❌ Alasan terlalu panjang (maks. 500 karakter). Coba lagi:');
            return;
        }

        \Config\Services::cache()->delete($stateKey);

        $orderId    = (int) $state['order_id'];
        $msgId      = (int) $state['message_id'];
        $adminId    = (int) $session['admin_id'];
        $adminName  = $session['admin_name'] ?? 'Admin';

        $orderModel = new OrderModel();
        $success    = $orderModel->rejectPayment($orderId, $adminId, $reason);

        if (! $success) {
            $this->bot->sendMessage($chatId, '⚠️ Pesanan sudah diproses oleh admin lain.');
            return;
        }

        (new ActivityLogModel())->log($adminId, 'telegram_tolak_pembayaran', "Menolak pesanan ID {$orderId} alasan: {$reason}");

        $caption = "❌ <b>Ditolak oleh {$adminName}</b>\nAlasan: " . htmlspecialchars($reason);

        $this->bot->editMessageCaption($chatId, $msgId, $caption, []);
        $this->bot->sendMessage($chatId, "✅ Penolakan berhasil diproses.");

        $this->broadcastStatusUpdate($orderId, 'order_payment_proof', $caption);
    }

    /**
     * Edit pesan notifikasi di inbox admin lain yang sudah menerima notifikasi pesanan ini.
     */
    private function broadcastStatusUpdate(int $orderId, string $type, string $newCaption): void
    {
        $outbox = new TelegramOutboxModel();
        $items  = $outbox->findSentByOrderAndType($orderId, $type);

        foreach ($items as $item) {
            if (! empty($item['sent_message_id'])) {
                $this->bot->editMessageCaption(
                    (int) $item['chat_id'],
                    (int) $item['sent_message_id'],
                    $newCaption,
                    []
                );
            }
        }
    }
}
